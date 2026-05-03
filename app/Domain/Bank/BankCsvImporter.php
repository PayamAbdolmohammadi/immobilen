<?php

namespace App\Domain\Bank;

use App\Models\BankImport;
use App\Models\BankTransaction;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class BankCsvImporter
{
    public const PROFILE_GENERIC = 'generic';

    public const PROFILE_SPARKASSE = 'sparkasse';

    public const PROFILE_COMDIRECT = 'comdirect';

    public function import(UploadedFile $file, int $mandantId, string $originalName, ?string $csvProfile = null): BankImport
    {
        $path = $file->getRealPath();
        if ($path === false) {
            throw new InvalidArgumentException('Upload could not be read.');
        }

        $raw = file_get_contents($path);
        if ($raw === false || $raw === '') {
            throw new InvalidArgumentException('The CSV file is empty.');
        }

        return $this->importFromContents($raw, $mandantId, $originalName, $csvProfile);
    }

    public function importFromContents(string $raw, int $mandantId, string $originalName, ?string $csvProfile = null): BankImport
    {
        $normalized = str_replace(["\r\n", "\r"], "\n", $raw);
        $lines = array_values(array_filter(explode("\n", $normalized), fn (string $line) => trim($line) !== ''));

        if ($lines === []) {
            throw new InvalidArgumentException('The CSV file is empty.');
        }

        $delimiter = $this->detectDelimiter($lines[0]);
        $headerLine = array_shift($lines);
        $headers = $this->readCsvLine($headerLine, $delimiter);
        $map = $this->mapHeaders($headers, $csvProfile);

        if ($map['date'] === null || $map['amount'] === null) {
            throw new InvalidArgumentException(
                'CSV must include recognizable columns for booking date and amount (e.g. Buchungstag, Betrag).'
            );
        }

        return DB::transaction(function () use ($lines, $delimiter, $map, $mandantId, $originalName, $headers, $csvProfile): BankImport {
            $import = BankImport::query()->create([
                'mandant_id' => $mandantId,
                'original_filename' => $originalName,
                'csv_profile' => $csvProfile,
                'row_count' => 0,
                'status' => BankImport::STATUS_COMPLETED,
                'error_message' => null,
            ]);

            $rowCount = 0;
            foreach ($lines as $line) {
                $cells = $this->readCsvLine($line, $delimiter);
                if ($this->rowIsEmpty($cells)) {
                    continue;
                }

                $dateIdx = $map['date'];
                $amountIdx = $map['amount'];
                $dateRaw = $cells[$dateIdx] ?? '';
                $amountRaw = $cells[$amountIdx] ?? '';

                $buchungsdatum = $this->parseDate($dateRaw);
                if ($buchungsdatum === null) {
                    continue;
                }

                $betragCent = $this->parseGermanAmountToCent($amountRaw);
                if ($betragCent === 0) {
                    continue;
                }

                $gegenpartei = $map['counterparty'] !== null
                    ? trim((string) ($cells[$map['counterparty']] ?? ''))
                    : null;
                $verwendung = $map['reference'] !== null
                    ? trim((string) ($cells[$map['reference']] ?? ''))
                    : null;

                $rawRow = [];
                foreach ($headers as $i => $key) {
                    $k = trim((string) $key);
                    if ($k !== '') {
                        $rawRow[$k] = $cells[$i] ?? null;
                    }
                }

                BankTransaction::query()->create([
                    'mandant_id' => $mandantId,
                    'bank_import_id' => $import->id,
                    'buchungsdatum' => $buchungsdatum,
                    'betrag_cent' => $betragCent,
                    'gegenpartei' => $gegenpartei !== '' ? $gegenpartei : null,
                    'verwendungszweck' => $verwendung !== '' ? $verwendung : null,
                    'raw_row' => $rawRow !== [] ? $rawRow : null,
                    'status' => BankTransaction::STATUS_OFFEN,
                ]);

                $rowCount++;
            }

            $import->update(['row_count' => $rowCount]);

            return $import->fresh() ?? $import;
        });
    }

    private function detectDelimiter(string $firstLine): string
    {
        $semi = substr_count($firstLine, ';');
        $comma = substr_count($firstLine, ',');

        return $semi >= $comma ? ';' : ',';
    }

    /**
     * @return array<int, string|null>
     */
    private function readCsvLine(string $line, string $delimiter): array
    {
        return str_getcsv($line, $delimiter);
    }

    /**
     * @param  array<int, string|null>  $headers
     * @return array{date: ?int, amount: ?int, counterparty: ?int, reference: ?int}
     */
    private function mapHeaders(array $headers, ?string $csvProfile): array
    {
        $norm = [];
        foreach ($headers as $i => $h) {
            $norm[$i] = mb_strtolower(trim((string) $h));
        }

        $dateNeedles = [
            'buchungstag', 'buchungsdatum', 'valuta', 'datum', 'booking date', 'date', 'wertstellung',
        ];
        $amountNeedles = [
            'betrag', 'umsatz', 'amount', 'value', 'soll', 'haben',
        ];
        $counterpartyNeedles = [
            'auftraggeber', 'beguenstigter', 'empfaenger', 'name', 'gegenpartei', 'counterparty',
        ];
        $referenceNeedles = [
            'verwendungszweck', 'buchungstext', 'reference', 'text', 'zweck',
        ];

        if ($csvProfile === self::PROFILE_SPARKASSE) {
            $dateNeedles = array_merge($dateNeedles, ['buchung', 'valutadatum']);
            $amountNeedles = array_merge($amountNeedles, ['umsatz (eur)', 'betrag (eur)']);
        }

        if ($csvProfile === self::PROFILE_COMDIRECT) {
            $dateNeedles = array_merge($dateNeedles, ['buchungstag (auftrag)', 'wertstellung (auftrag)']);
            $amountNeedles = array_merge($amountNeedles, ['umsatz in eur', 'betrag in eur']);
        }

        $dateIdx = $this->firstIndexMatching($norm, $dateNeedles);
        $amountIdx = $this->firstIndexMatching($norm, $amountNeedles);
        $counterpartyIdx = $this->firstIndexMatching($norm, $counterpartyNeedles);
        $referenceIdx = $this->firstIndexMatching($norm, $referenceNeedles);

        return [
            'date' => $dateIdx,
            'amount' => $amountIdx,
            'counterparty' => $counterpartyIdx,
            'reference' => $referenceIdx,
        ];
    }

    /**
     * @param  array<int, string>  $norm
     * @param  list<string>  $needles
     */
    private function firstIndexMatching(array $norm, array $needles): ?int
    {
        foreach ($norm as $i => $cell) {
            foreach ($needles as $needle) {
                if ($cell === $needle || str_contains($cell, $needle)) {
                    return $i;
                }
            }
        }

        return null;
    }

    /**
     * @param  array<int, string|null>  $cells
     */
    private function rowIsEmpty(array $cells): bool
    {
        foreach ($cells as $c) {
            if (trim((string) $c) !== '') {
                return false;
            }
        }

        return true;
    }

    private function parseDate(string $value): ?Carbon
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $formats = ['d.m.Y', 'd.m.y', 'Y-m-d', 'd/m/Y', 'm/d/Y'];
        foreach ($formats as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, $value)->startOfDay();
            } catch (\Throwable) {
            }
        }

        return null;
    }

    private function parseGermanAmountToCent(string $value): int
    {
        $trimmed = trim(str_replace("\xc2\xa0", '', $value));
        if ($trimmed === '') {
            return 0;
        }

        $negative = false;
        if (str_starts_with($trimmed, '-')) {
            $negative = true;
            $trimmed = substr($trimmed, 1);
        }

        $trimmed = str_replace('.', '', $trimmed);
        $trimmed = str_replace(',', '.', $trimmed);

        if (! is_numeric($trimmed)) {
            return 0;
        }

        $float = (float) $trimmed;
        $cent = (int) round(abs($float) * 100);

        return $negative ? -$cent : $cent;
    }
}
