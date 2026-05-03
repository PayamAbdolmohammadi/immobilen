<?php

namespace App\Http\Controllers;

use App\Domain\Billing\GenerateMonthlyRentInvoices;
use App\Domain\Billing\RentAmountCalculator;
use App\Http\Controllers\Concerns\ResolvesCurrentMandant;
use App\Models\BankImport;
use App\Models\BankTransaction;
use App\Models\Mietvertrag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DemoFlowController extends Controller
{
    use ResolvesCurrentMandant;

    public function index(Request $request): View
    {
        $mandantId = $this->mandantId($request);
        $period = now()->format('Y-m');
        $expectedCent = $this->primaryActiveLeaseTotalCent($mandantId);

        return view('demo-flow.index', [
            'billingPeriod' => $period,
            'expectedRentEuroFormatted' => number_format(max(0, $expectedCent) / 100, 2, ',', '.'),
            'hasActiveLease' => $expectedCent > 0,
        ]);
    }

    public function startAutoDemo(Request $request, GenerateMonthlyRentInvoices $service): RedirectResponse
    {
        $mandantId = $this->mandantId($request);
        $period = now()->format('Y-m');

        try {
            $created = $service->run($period, $mandantId);
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('demo-flow.index')->withErrors(['demo_auto' => $e->getMessage()]);
        }

        $bankCreated = $this->createDemoBankLineIfMissing($mandantId);

        return redirect()->route('demo-flow.index')
            ->with('status', 'demo-auto-prepared')
            ->with('demo_auto_rent_created', $created)
            ->with('demo_auto_bank_created', $bankCreated);
    }

    public function generateRent(Request $request, GenerateMonthlyRentInvoices $service): RedirectResponse
    {
        $validated = $request->validate([
            'period' => ['nullable', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
        ]);

        $period = $validated['period'] ?? now()->format('Y-m');

        try {
            $created = $service->run($period, $this->mandantId($request));
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('demo-flow.index')->withErrors(['period' => $e->getMessage()]);
        }

        return redirect()->route('demo-flow.index')
            ->with('status', 'demo-rent-generated')
            ->with('demo_rent_created', $created)
            ->with('demo_rent_period', $period);
    }

    public function downloadDemoBankCsv(Request $request): StreamedResponse
    {
        $mandantId = $this->mandantId($request);
        $cent = $this->primaryActiveLeaseTotalCent($mandantId);
        if ($cent <= 0) {
            $cent = 120_000;
        }

        $lease = Mietvertrag::query()
            ->where('mandant_id', $mandantId)
            ->where('status', Mietvertrag::STATUS_ACTIVE)
            ->with('mieter')
            ->orderBy('id')
            ->first();

        $name = $lease?->mieter?->name ?? 'Mieter';
        $date = now()->format('d.m.Y');
        $amount = number_format($cent / 100, 2, ',', '');
        $ref = '[DEMO] Miete '.$name.' — WOW Demo';

        $csv = "Buchungsdatum;Betrag EUR;Gegenpartei;Verwendungszweck\r\n";
        $csv .= sprintf('%s;%s;%s;%s', $date, $amount, $name, $ref);

        $filename = 'wow-demo-bank-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($csv): void {
            echo "\xEF\xBB\xBF";
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function seedBankLine(Request $request): RedirectResponse
    {
        $this->createDemoBankLineIfMissing($this->mandantId($request));

        return redirect()->route('demo-flow.index')
            ->with('status', 'demo-bank-seeded');
    }

    /**
     * Creates one open demo bank line matching the lease total when none exists yet.
     */
    private function createDemoBankLineIfMissing(int $mandantId): bool
    {
        $cent = $this->primaryActiveLeaseTotalCent($mandantId);
        if ($cent <= 0) {
            $cent = 120_000;
        }

        $already = BankTransaction::query()
            ->where('mandant_id', $mandantId)
            ->where('status', BankTransaction::STATUS_OFFEN)
            ->where('betrag_cent', $cent)
            ->where('verwendungszweck', 'like', '%[DEMO]%')
            ->exists();

        if ($already) {
            return false;
        }

        $lease = Mietvertrag::query()
            ->where('mandant_id', $mandantId)
            ->where('status', Mietvertrag::STATUS_ACTIVE)
            ->with('mieter')
            ->orderBy('id')
            ->first();

        $name = $lease?->mieter?->name ?? 'Mieter';

        $import = BankImport::query()->create([
            'mandant_id' => $mandantId,
            'original_filename' => '[DEMO] wow-demo-bank-line',
            'csv_profile' => null,
            'row_count' => 1,
            'status' => BankImport::STATUS_COMPLETED,
            'error_message' => null,
        ]);

        BankTransaction::query()->create([
            'mandant_id' => $mandantId,
            'bank_import_id' => $import->id,
            'buchungsdatum' => now()->toDateString(),
            'betrag_cent' => $cent,
            'gegenpartei' => $name,
            'verwendungszweck' => '[DEMO] Miete '.$name.' — WOW Demo (direkt)',
            'raw_row' => null,
            'status' => BankTransaction::STATUS_OFFEN,
        ]);

        return true;
    }

    private function primaryActiveLeaseTotalCent(int $mandantId): int
    {
        $lease = Mietvertrag::query()
            ->where('mandant_id', $mandantId)
            ->where('status', Mietvertrag::STATUS_ACTIVE)
            ->orderBy('id')
            ->first();

        if ($lease === null) {
            return 0;
        }

        return (new RentAmountCalculator)->forLease($lease)['total_cent'];
    }
}
