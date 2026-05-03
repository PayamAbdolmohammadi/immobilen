<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesCurrentMandant;
use App\Models\Objekt;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ObjektController extends Controller
{
    use AuthorizesRequests;
    use ResolvesCurrentMandant;

    public function index(): View
    {
        $this->authorize('viewAny', Objekt::class);

        $objekte = Objekt::query()
            ->where('mandant_id', $this->mandantId())
            ->orderBy('name')
            ->paginate(20);

        return view('objekte.index', compact('objekte'));
    }

    public function create(): View
    {
        $this->authorize('create', Objekt::class);

        return view('objekte.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Objekt::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Objekt::query()->create([
            'mandant_id' => $this->mandantId($request),
            'name' => $data['name'],
        ]);

        return redirect()->route('objekte.index')->with('status', 'objekt-saved');
    }

    public function edit(Objekt $objekt): View
    {
        $this->authorize('update', $objekt);

        return view('objekte.edit', compact('objekt'));
    }

    public function update(Request $request, Objekt $objekt): RedirectResponse
    {
        $this->authorize('update', $objekt);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $objekt->update(['name' => $data['name']]);

        return redirect()->route('objekte.index')->with('status', 'objekt-saved');
    }

    public function destroy(Objekt $objekt): RedirectResponse
    {
        $this->authorize('delete', $objekt);

        $objekt->delete();

        return redirect()->route('objekte.index')->with('status', 'objekt-deleted');
    }
}
