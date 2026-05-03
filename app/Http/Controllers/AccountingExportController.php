<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesCurrentMandant;
use App\Models\User;
use App\Services\Accounting\DatevExportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountingExportController extends Controller
{
    use ResolvesCurrentMandant;

    public function index(Request $request): View
    {
        $this->authorizeOwner($request);

        return view('export.accounting', [
            'defaultYear' => (int) now()->format('Y'),
        ]);
    }

    public function exportSimpleCsv(Request $request, DatevExportService $service): StreamedResponse
    {
        $this->authorizeOwner($request);
        $data = $this->validatedYearMonth($request);

        return $service->streamSimpleCsv(
            $this->mandantId($request),
            $data['year'],
            $data['month'] ?? null,
        );
    }

    public function exportDatevCsv(Request $request, DatevExportService $service): StreamedResponse
    {
        $this->authorizeOwner($request);
        $data = $this->validatedDatevRequest($request);

        return $service->streamDatevCsv(
            $this->mandantId($request),
            $data['year'],
            $data['month'] ?? null,
            $data['chart_of_accounts'],
        );
    }

    private function authorizeOwner(Request $request): void
    {
        abort_unless($request->user() instanceof User && $request->user()->role === User::ROLE_OWNER, 403);
    }

    /**
     * @return array{year: int, month: ?int}
     */
    private function validatedYearMonth(Request $request): array
    {
        return $request->validate([
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);
    }

    /**
     * @return array{year: int, month: ?int, chart_of_accounts: string}
     */
    private function validatedDatevRequest(Request $request): array
    {
        return $request->validate([
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'chart_of_accounts' => ['required', 'string', 'in:SKR03,SKR04'],
        ]);
    }
}
