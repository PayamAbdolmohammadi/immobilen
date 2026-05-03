<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesCurrentMandant;
use App\Models\Einheit;
use App\Models\Objekt;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EinheitController extends Controller
{
    use AuthorizesRequests;
    use ResolvesCurrentMandant;

    public function index(): View
    {
        $this->authorize('viewAny', Einheit::class);

        $einheiten = Einheit::query()
            ->where('mandant_id', $this->mandantId())
            ->with('objekt')
            ->orderBy('name')
            ->paginate(20);

        return view('einheiten.index', compact('einheiten'));
    }

    public function create(): View
    {
        $this->authorize('create', Einheit::class);

        $objekte = Objekt::query()
            ->where('mandant_id', $this->mandantId())
            ->orderBy('name')
            ->get();

        return view('einheiten.create', compact('objekte'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Einheit::class);

        $mandantId = $this->mandantId($request);

        $data = $request->validate([
            'objekt_id' => [
                'required',
                'integer',
                Rule::exists('objekte', 'id')->where('mandant_id', $mandantId),
            ],
            'name' => ['required', 'string', 'max:255'],
        ]);

        Einheit::query()->create([
            'mandant_id' => $mandantId,
            'objekt_id' => $data['objekt_id'],
            'name' => $data['name'],
        ]);

        return redirect()->route('einheiten.index')->with('status', 'einheit-saved');
    }

    public function edit(Einheit $einheit): View
    {
        $this->authorize('update', $einheit);

        $objekte = Objekt::query()
            ->where('mandant_id', $this->mandantId())
            ->orderBy('name')
            ->get();

        return view('einheiten.edit', compact('einheit', 'objekte'));
    }

    public function update(Request $request, Einheit $einheit): RedirectResponse
    {
        $this->authorize('update', $einheit);

        $mandantId = $this->mandantId($request);

        $data = $request->validate([
            'objekt_id' => [
                'required',
                'integer',
                Rule::exists('objekte', 'id')->where('mandant_id', $mandantId),
            ],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $einheit->update([
            'objekt_id' => $data['objekt_id'],
            'name' => $data['name'],
        ]);

        return redirect()->route('einheiten.index')->with('status', 'einheit-saved');
    }

    public function destroy(Einheit $einheit): RedirectResponse
    {
        $this->authorize('delete', $einheit);

        $einheit->delete();

        return redirect()->route('einheiten.index')->with('status', 'einheit-deleted');
    }
}
