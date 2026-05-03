<?php

namespace App\Http\Controllers;

use App\Domain\Bank\AllocateBankPayment;
use App\Domain\Bank\BankCsvImporter;
use App\Domain\Bank\BankMatchSuggestionService;
use App\Http\Controllers\Concerns\ResolvesCurrentMandant;
use App\Http\Requests\AllocateBankPaymentRequest;
use App\Jobs\ProcessBankCsvImportJob;
use App\Models\BankImport;
use App\Models\BankTransaction;
use App\Models\Rechnung;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Throwable;

class BankController extends Controller
{
    use ResolvesCurrentMandant;

    /**
     * @return array<string, mixed>
     */
    protected function matchingViewData(): array
    {
        $mandantId = $this->mandantId();

        $transactions = BankTransaction::query()
            ->where('mandant_id', $mandantId)
            ->with('zahlungszuordnungen.rechnung')
            ->orderByDesc('buchungsdatum')
            ->orderByDesc('id')
            ->paginate(25);

        $openInvoices = Rechnung::query()
            ->where('mandant_id', $mandantId)
            ->where('status', Rechnung::STATUS_OFFEN)
            ->with('mieter')
            ->orderByRaw('faellig_am is null')
            ->orderBy('faellig_am')
            ->limit(100)
            ->get();

        $matchSuggestions = (new BankMatchSuggestionService)
            ->forTransactions($transactions->getCollection(), $openInvoices);

        return compact('transactions', 'openInvoices', 'matchSuggestions');
    }

    public function index(Request $request): View
    {
        $mandantId = $this->mandantId();

        $imports = BankImport::query()
            ->where('mandant_id', $mandantId)
            ->latest()
            ->paginate(15);

        return view('bank.index', array_merge($this->matchingViewData(), compact('imports'), [
            'fromDemo' => $request->boolean('demo'),
        ]));
    }

    public function matching(Request $request): View
    {
        return view('bank.match', array_merge($this->matchingViewData(), [
            'fromDemo' => $request->boolean('demo'),
        ]));
    }

    public function import(Request $request): RedirectResponse
    {
        $mandantId = $this->mandantId($request);

        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
            'csv_profile' => [
                'nullable',
                'string',
                Rule::in([
                    BankCsvImporter::PROFILE_GENERIC,
                    BankCsvImporter::PROFILE_SPARKASSE,
                    BankCsvImporter::PROFILE_COMDIRECT,
                ]),
            ],
        ]);

        $file = $request->file('file');
        $original = $file->getClientOriginalName();

        $profile = $data['csv_profile'] ?? BankCsvImporter::PROFILE_GENERIC;
        $storedProfile = $profile === BankCsvImporter::PROFILE_GENERIC ? null : $profile;

        $relativePath = $file->store('bank-upload-temp');

        ProcessBankCsvImportJob::dispatch($relativePath, (int) $mandantId, $original, $storedProfile);

        return redirect()->route('bank.matching')->with('status', 'bank-import-queued');
    }

    public function allocate(
        AllocateBankPaymentRequest $request,
        BankTransaction $bank_transaction,
        AllocateBankPayment $allocateBankPayment,
    ): RedirectResponse {
        abort_unless(
            $bank_transaction->mandant_id === $this->mandantId($request),
            403
        );

        $rechnung = Rechnung::query()->where('mandant_id', $this->mandantId($request))
            ->whereKey($request->validated('rechnung_id'))
            ->firstOrFail();

        try {
            $allocateBankPayment->assign($bank_transaction, $rechnung, $request->amountCent());
        } catch (Throwable $e) {
            return back()->withErrors(['betrag' => $e->getMessage()]);
        }

        if ($request->validated('return_to_demo') === '1') {
            return redirect()->route('demo-flow.index')->with('status', 'demo-payment-matched');
        }

        return redirect()->route('bank.matching', array_filter([
            'demo' => $request->validated('demo'),
        ]))->with('status', 'payment-allocated');
    }
}
