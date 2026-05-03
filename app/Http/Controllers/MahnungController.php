<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesCurrentMandant;
use App\Mail\MahnungMail;
use App\Models\Rechnung;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class MahnungController extends Controller
{
    use AuthorizesRequests;
    use ResolvesCurrentMandant;

    public function store(Rechnung $rechnung): RedirectResponse
    {
        $this->authorize('sendMahnung', $rechnung);

        $rechnung->loadMissing('mieter');

        Mail::to($rechnung->mieter->email)->send(new MahnungMail($rechnung));

        return redirect()->route('dashboard')->with('status', 'mahnung-sent');
    }
}
