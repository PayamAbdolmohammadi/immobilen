<?php

namespace App\Http\Controllers;

use App\Models\Rechnung;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RechnungStornoController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Rechnung $rechnung): RedirectResponse
    {
        $this->authorize('storno', $rechnung);

        $request->validate([
            'confirm' => ['accepted'],
        ]);

        $rechnung->update([
            'status' => Rechnung::STATUS_STORNIERT,
            'storniert_am' => now(),
        ]);

        return redirect()->route('dashboard')->with('status', 'rechnung-storniert');
    }
}
