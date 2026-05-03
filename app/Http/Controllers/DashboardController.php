<?php

namespace App\Http\Controllers;

use App\Domain\Billing\RentAmountCalculator;
use App\Http\Controllers\Concerns\ResolvesCurrentMandant;
use App\Models\Mietvertrag;
use App\Models\Rechnung;
use App\Models\Zahlungszuordnung;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use ResolvesCurrentMandant;

    public function __invoke(): View
    {
        $mandantId = $this->mandantId();

        $today = now()->toDateString();

        $openInvoicesQuery = Rechnung::query()
            ->where('mandant_id', $mandantId)
            ->where('status', Rechnung::STATUS_OFFEN);

        $openCount = (clone $openInvoicesQuery)->count();

        $overdueInvoices = (clone $openInvoicesQuery)
            ->whereNotNull('faellig_am')
            ->whereDate('faellig_am', '<', $today)
            ->with('mieter')
            ->orderBy('faellig_am')
            ->limit(15)
            ->get();

        $openInvoicesPreview = (clone $openInvoicesQuery)
            ->with('mieter')
            ->orderByRaw('faellig_am is null')
            ->orderBy('faellig_am')
            ->limit(10)
            ->get();

        $recentPayments = Zahlungszuordnung::query()
            ->where('mandant_id', $mandantId)
            ->with(['rechnung.mieter', 'bankTransaction'])
            ->latest()
            ->limit(10)
            ->get();

        $overdueCount = (clone $openInvoicesQuery)
            ->whereNotNull('faellig_am')
            ->whereDate('faellig_am', '<', $today)
            ->count();

        $rentCalculator = new RentAmountCalculator;

        $activeLeases = Mietvertrag::query()
            ->where('mandant_id', $mandantId)
            ->where('status', Mietvertrag::STATUS_ACTIVE)
            ->with(['einheit.objekt', 'mieter'])
            ->orderBy('starts_on')
            ->get();

        $leasePreviews = $activeLeases->map(function (Mietvertrag $lease) use ($rentCalculator): array {
            $snapshot = $rentCalculator->forLease($lease);

            return [
                'lease' => $lease,
                'total_cent' => $snapshot['total_cent'],
                'next_period_hint' => $lease->next_billing_period ?? now()->format('Y-m'),
            ];
        });

        return view('dashboard', compact(
            'openCount',
            'overdueCount',
            'overdueInvoices',
            'openInvoicesPreview',
            'recentPayments',
            'leasePreviews'
        ));
    }
}
