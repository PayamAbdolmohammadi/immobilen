<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Mietvertrag;
use App\Models\Rechnung;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $mieter = Auth::guard('mieter')->user();
        abort_unless($mieter !== null, 403);

        $lease = Mietvertrag::query()
            ->where('mieter_id', $mieter->id)
            ->where('status', Mietvertrag::STATUS_ACTIVE)
            ->with(['einheit.objekt'])
            ->orderByDesc('starts_on')
            ->first();

        $openInvoices = Rechnung::query()
            ->where('mieter_id', $mieter->id)
            ->where('status', Rechnung::STATUS_OFFEN)
            ->with(['einheit.objekt'])
            ->orderByRaw('faellig_am IS NULL')
            ->orderBy('faellig_am')
            ->orderByDesc('id')
            ->get();

        return view('portal.dashboard', [
            'mieter' => $mieter,
            'lease' => $lease,
            'openInvoices' => $openInvoices,
        ]);
    }
}
