<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesCurrentMandant;
use App\Models\Mieter;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MieterController extends Controller
{
    use AuthorizesRequests;
    use ResolvesCurrentMandant;

    public function index(): View
    {
        $this->authorize('viewAny', Mieter::class);

        $mieter = Mieter::query()
            ->where('mandant_id', $this->mandantId())
            ->orderBy('name')
            ->paginate(20);

        return view('mieter.index', compact('mieter'));
    }

    public function create(): View
    {
        $this->authorize('create', Mieter::class);

        return view('mieter.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Mieter::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('mieter', 'email')],
            'portal_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (! empty($data['portal_password']) && empty($data['email'])) {
            return back()
                ->withErrors(['email' => __('An email is required to enable the tenant portal.')])
                ->withInput();
        }

        $row = [
            'mandant_id' => $this->mandantId($request),
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
        ];
        if (! empty($data['portal_password'])) {
            $row['password'] = $data['portal_password'];
        }

        Mieter::query()->create($row);

        return redirect()->route('mieter.index')->with('status', 'mieter-saved');
    }

    public function edit(Mieter $mieter): View
    {
        $this->authorize('update', $mieter);

        return view('mieter.edit', compact('mieter'));
    }

    public function update(Request $request, Mieter $mieter): RedirectResponse
    {
        $this->authorize('update', $mieter);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('mieter', 'email')->ignore($mieter->id)],
            'portal_password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'disable_portal' => ['sometimes', 'boolean'],
        ]);

        if (! empty($data['portal_password']) && empty($data['email'])) {
            return back()
                ->withErrors(['email' => __('An email is required to enable the tenant portal.')])
                ->withInput();
        }

        $mieter->name = $data['name'];
        $mieter->email = $data['email'] ?? null;

        if ($request->boolean('disable_portal')) {
            $mieter->password = null;
            $mieter->remember_token = null;
        } elseif (! empty($data['portal_password'])) {
            $mieter->password = $data['portal_password'];
        }

        $mieter->save();

        return redirect()->route('mieter.index')->with('status', 'mieter-saved');
    }

    public function destroy(Mieter $mieter): RedirectResponse
    {
        $this->authorize('delete', $mieter);

        $mieter->delete();

        return redirect()->route('mieter.index')->with('status', 'mieter-deleted');
    }
}
