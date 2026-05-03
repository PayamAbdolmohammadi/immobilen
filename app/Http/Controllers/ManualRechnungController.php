<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesCurrentMandant;
use App\Http\Requests\StoreManualRechnungRequest;
use App\Http\Requests\UpdateManualRechnungRequest;
use App\Models\Einheit;
use App\Models\Mieter;
use App\Models\Rechnung;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ManualRechnungController extends Controller
{
    use AuthorizesRequests;
    use ResolvesCurrentMandant;

    public function index(): View
    {
        $this->authorize('viewAny', Rechnung::class);

        $mandantId = $this->mandantId();

        $rechnungen = Rechnung::query()
            ->where('mandant_id', $mandantId)
            ->manual()
            ->with(['mieter', 'einheit'])
            ->orderByRaw('faellig_am is null')
            ->orderBy('faellig_am')
            ->orderByDesc('id')
            ->paginate(20);

        return view('rechnungen.manual.index', compact('rechnungen'));
    }

    public function create(): View
    {
        $this->authorize('create', Rechnung::class);

        $mandantId = $this->mandantId();

        $mieter = Mieter::query()->where('mandant_id', $mandantId)->orderBy('name')->get();
        $einheiten = Einheit::query()->where('mandant_id', $mandantId)->orderBy('name')->get();

        return view('rechnungen.manual.create', compact('mieter', 'einheiten'));
    }

    public function store(StoreManualRechnungRequest $request): RedirectResponse
    {
        $mandantId = $this->mandantId($request);

        $bezahltAm = null;
        if ($request->validated('status') === Rechnung::STATUS_BEZAHLT) {
            $bezahltAm = $request->validated('bezahlt_am')
                ? Carbon::parse($request->validated('bezahlt_am'))
                : now();
        }

        Rechnung::query()->create([
            'mandant_id' => $mandantId,
            'mieter_id' => $request->validated('mieter_id'),
            'einheit_id' => $request->validated('einheit_id'),
            'mietvertrag_id' => null,
            'typ' => Rechnung::TYP_MANUAL,
            'billing_period' => null,
            'betrag_cent' => $request->betragCent(),
            'source_data' => null,
            'status' => $request->validated('status'),
            'faellig_am' => $request->validated('faellig_am'),
            'bezahlt_am' => $bezahltAm,
        ]);

        return redirect()->route('manual-rechnungen.index')
            ->with('status', 'rechnung-created');
    }

    public function edit(Rechnung $manual_rechnung): View
    {
        $this->authorize('update', $manual_rechnung);

        $mandantId = $this->mandantId();

        $mieter = Mieter::query()->where('mandant_id', $mandantId)->orderBy('name')->get();
        $einheiten = Einheit::query()->where('mandant_id', $mandantId)->orderBy('name')->get();

        return view('rechnungen.manual.edit', [
            'rechnung' => $manual_rechnung,
            'mieter' => $mieter,
            'einheiten' => $einheiten,
        ]);
    }

    public function update(UpdateManualRechnungRequest $request, Rechnung $manual_rechnung): RedirectResponse
    {
        $this->authorize('update', $manual_rechnung);

        $bezahltAm = null;
        if ($request->validated('status') === Rechnung::STATUS_BEZAHLT) {
            $bezahltAm = $request->validated('bezahlt_am')
                ? Carbon::parse($request->validated('bezahlt_am'))
                : ($manual_rechnung->bezahlt_am ?? now());
        }

        $manual_rechnung->update([
            'mieter_id' => $request->validated('mieter_id'),
            'einheit_id' => $request->validated('einheit_id'),
            'betrag_cent' => $request->betragCent(),
            'status' => $request->validated('status'),
            'faellig_am' => $request->validated('faellig_am'),
            'bezahlt_am' => $bezahltAm,
        ]);

        return redirect()->route('manual-rechnungen.index')
            ->with('status', 'rechnung-updated');
    }

    public function destroy(Rechnung $manual_rechnung): RedirectResponse
    {
        $this->authorize('delete', $manual_rechnung);

        $manual_rechnung->delete();

        return redirect()->route('manual-rechnungen.index')
            ->with('status', 'rechnung-deleted');
    }
}
