<?php

namespace App\Services\Accounting;

use App\Models\Rechnung;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * DATEV-ready preparation export. Must be checked by a Steuerberater before use.
 */
final class DatevExportService
{
    private const REVENUE_SKR03 = '8400';

    private const REVENUE_SKR04 = '4400';

    private const BANK_SKR03 = '1200';

    private const BANK_SKR04 = '1800';

    /**
     * @return Builder<Rechnung>
     */
    public function invoiceQuery(int $mandantId, int $year, ?int $month): Builder
    {
        return Rechnung::query()
            ->where('mandant_id', $mandantId)
            ->whereYear('created_at', $year)
            ->when($month !== null, fn (Builder $q) => $q->whereMonth('created_at', $month))
            ->with([
                'mieter',
                'einheit.objekt',
                'mandant',
                'zahlungszuordnungen.bankTransaction',
            ])
            ->orderBy('id');
    }

    /**
     * Invoices for DATEV-ready CSV (excludes storno — must not appear as revenue).
     *
     * @return Builder<Rechnung>
     */
    public function datevInvoiceQuery(int $mandantId, int $year, ?int $month): Builder
    {
        return $this->invoiceQuery($mandantId, $year, $month)
            ->where('status', '!=', Rechnung::STATUS_STORNIERT);
    }

    public function streamSimpleCsv(int $mandantId, int $year, ?int $month): StreamedResponse
    {
        $filename = $this->simpleFilename($year, $month);

        return $this->streamCsv($filename, function ($out) use ($mandantId, $year, $month): void {
            $this->writeBom($out);
            fputcsv($out, [
                'Rechnungsnummer',
                'Rechnungsdatum',
                'Fällig am',
                'Bezahlt am',
                'Mieter',
                'Objekt',
                'Einheit',
                'Typ',
                'Netto',
                'Steuer',
                'Brutto',
                'Status',
                'Banktext',
            ], ';');

            $this->invoiceQuery($mandantId, $year, $month)->chunkById(200, function ($chunk) use ($out): void {
                foreach ($chunk as $rechnung) {
                    assert($rechnung instanceof Rechnung);
                    $netSteuerBrutto = $this->resolveNetSteuerBrutto($rechnung);
                    fputcsv($out, [
                        (string) $rechnung->id,
                        $rechnung->created_at->format('d.m.Y'),
                        $rechnung->faellig_am?->format('d.m.Y') ?? '',
                        $rechnung->bezahlt_am?->format('d.m.Y H:i') ?? '',
                        $rechnung->mieter?->name ?? '',
                        $rechnung->einheit?->objekt?->name ?? '',
                        $rechnung->einheit?->name ?? '',
                        $rechnung->typ,
                        $netSteuerBrutto['netto'],
                        $netSteuerBrutto['steuer'],
                        $netSteuerBrutto['brutto'],
                        $this->exportPaymentStatus($rechnung),
                        $this->bankText($rechnung),
                    ], ';');
                }
            });
        });
    }

    public function streamDatevCsv(int $mandantId, int $year, ?int $month, string $chart): StreamedResponse
    {
        $filename = $this->datevFilename($year, $month);
        $revenue = $chart === 'SKR04' ? self::REVENUE_SKR04 : self::REVENUE_SKR03;
        $bank = $chart === 'SKR04' ? self::BANK_SKR04 : self::BANK_SKR03;

        return $this->streamCsv($filename, function ($out) use ($mandantId, $year, $month, $revenue, $bank): void {
            $this->writeBom($out);
            fputcsv($out, [
                'Umsatz',
                'Soll/Haben',
                'WKZ Umsatz',
                'Konto',
                'Gegenkonto',
                'Belegdatum',
                'Belegfeld 1',
                'Buchungstext',
                'Mandant',
                'Kostenstelle',
                'Rechnungsnummer',
                'Zahlungsstatus',
                'Netto',
                'Steuer',
                'Brutto',
            ], ';');

            $this->datevInvoiceQuery($mandantId, $year, $month)->chunkById(200, function ($chunk) use ($out, $revenue, $bank): void {
                foreach ($chunk as $rechnung) {
                    assert($rechnung instanceof Rechnung);
                    $paid = $rechnung->status === Rechnung::STATUS_BEZAHLT;
                    $netSteuerBrutto = $this->resolveNetSteuerBrutto($rechnung);
                    fputcsv($out, [
                        $this->formatEuro($rechnung->betrag_cent),
                        'H',
                        'EUR',
                        $revenue,
                        $paid ? $bank : '',
                        $rechnung->created_at->format('d.m.Y'),
                        (string) $rechnung->id,
                        $this->buchungstext($rechnung),
                        $rechnung->mandant?->name ?? '',
                        $rechnung->einheit?->name ?? '',
                        (string) $rechnung->id,
                        $this->exportPaymentStatus($rechnung),
                        $netSteuerBrutto['netto'],
                        $netSteuerBrutto['steuer'],
                        $netSteuerBrutto['brutto'],
                    ], ';');
                }
            });
        });
    }

    /**
     * @return array{netto: string, steuer: string, brutto: string}
     */
    private function resolveNetSteuerBrutto(Rechnung $rechnung): array
    {
        $sd = $rechnung->source_data;
        $brutto = $this->formatEuro($rechnung->betrag_cent);
        $netto = '';
        $steuer = '';
        if (is_array($sd)) {
            if (isset($sd['net_cent']) && is_numeric($sd['net_cent'])) {
                $netto = $this->formatEuro((int) $sd['net_cent']);
            }
            if (isset($sd['steuer_cent']) && is_numeric($sd['steuer_cent'])) {
                $steuer = $this->formatEuro((int) $sd['steuer_cent']);
            }
        }

        return ['netto' => $netto, 'steuer' => $steuer, 'brutto' => $brutto];
    }

    private function buchungstext(Rechnung $rechnung): string
    {
        $name = $rechnung->mieter?->name ?? '';
        $period = $rechnung->billing_period ?? '';

        return trim(implode(' ', array_filter([$name, $period !== '' ? "Periode {$period}" : ''])));
    }

    private function bankText(Rechnung $rechnung): string
    {
        $parts = [];
        foreach ($rechnung->zahlungszuordnungen as $z) {
            $text = $z->bankTransaction?->verwendungszweck;
            if ($text !== null && $text !== '') {
                $parts[] = preg_replace('/\s+/', ' ', $text) ?? $text;
            }
        }

        return implode(' | ', array_unique($parts));
    }

    private function exportPaymentStatus(Rechnung $rechnung): string
    {
        return match ($rechnung->status) {
            Rechnung::STATUS_BEZAHLT => 'BEZAHLT',
            Rechnung::STATUS_STORNIERT => 'STORNIERT',
            default => 'OFFEN',
        };
    }

    private function formatEuro(int $cent): string
    {
        return number_format($cent / 100, 2, ',', '');
    }

    private function simpleFilename(int $year, ?int $month): string
    {
        return $month === null
            ? "accounting-export-{$year}.csv"
            : sprintf('accounting-export-%04d-%02d.csv', $year, $month);
    }

    private function datevFilename(int $year, ?int $month): string
    {
        return $month === null
            ? "datev-ready-export-{$year}.csv"
            : sprintf('datev-ready-export-%04d-%02d.csv', $year, $month);
    }

    /**
     * @param  Closure(resource): void  $callback
     */
    private function streamCsv(string $filename, Closure $callback): StreamedResponse
    {
        return response()->streamDownload(function () use ($callback): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            $callback($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @param  resource  $out
     */
    private function writeBom($out): void
    {
        fwrite($out, "\xEF\xBB\xBF");
    }
}
