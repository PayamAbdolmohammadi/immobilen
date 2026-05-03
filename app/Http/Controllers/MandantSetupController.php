<?php

namespace App\Http\Controllers;

use App\Models\Mandant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class MandantSetupController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()->mandant_id !== null) {
            return redirect()->route('dashboard');
        }

        return view('mandanten.einrichten');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->mandant_id !== null) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'mandanten_name' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($user, $validated): void {
            $mandant = Mandant::query()->create(['name' => $validated['mandanten_name']]);
            $user->forceFill(['mandant_id' => $mandant->id])->save();
        });

        return redirect()->route('dashboard');
    }
}
