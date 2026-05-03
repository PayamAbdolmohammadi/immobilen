<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesCurrentMandant;
use App\Models\NkAbrechnung;
use App\Models\Objekt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NkAbrechnungController extends Controller
{
    use AuthorizesRequests;
    use ResolvesCurrentMandant;

    public function index(): View
    {
        $this->authorize('viewAny', NkAbrechnung::class);

        $abrechnungen = NkAbrechnung::query()
            ->where('mandant_id', $this->mandantId())
            ->with('objekt')
            ->orderByDesc('jahr')
            ->orderBy('objekt_id')
            ->paginate(20);

        return view('nk-abrechnungen.index', compact('abrechnungen'));
    }

    public function create(): View
    {
        $this->authorize('create', NkAbrechnung::class);

        $objekte = Objekt::query()
            ->where('mandant_id', $this->mandantId())
            ->orderBy('name')
            ->get();

        return view('nk-abrechnungen.create', compact('objekte'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', NkAbrechnung::class);

        $mandantId = $this->mandantId($request);

        $filteredPositionen = [];
        foreach ($request->input('positionen', []) as $row) {
            if (! is_array($row)) {
                continue;
            }
            $beschreibung = trim((string) ($row['beschreibung'] ?? ''));
            $betragRaw = $row['betrag'] ?? null;
            if ($beschreibung === '' || $betragRaw === null || $betragRaw === '') {
                continue;
            }
            $filteredPositionen[] = ['beschreibung' => $beschreibung, 'betrag' => $betragRaw];
        }
        $request->merge(['positionen' => $filteredPositionen]);

        $data = $request->validate([
            'objekt_id' => ['required', 'integer', Rule::exists('objekte', 'id')->where('mandant_id', $mandantId)],
            'jahr' => ['required', 'integer', 'min:2000', 'max:2100'],
            'positionen' => ['required', 'array', 'min:1'],
            'positionen.*.beschreibung' => ['required', 'string', 'max:255'],
            'positionen.*.betrag' => ['required', 'numeric', 'min:0.01'],
            'status' => ['nullable', Rule::in([NkAbrechnung::STATUS_DRAFT, NkAbrechnung::STATUS_FINAL])],
        ]);

        $exists = NkAbrechnung::query()
            ->where('objekt_id', $data['objekt_id'])
            ->where('jahr', $data['jahr'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['jahr' => __('A settlement for this property and year already exists.')])->withInput();
        }

        $abrechnung = NkAbrechnung::query()->create([
            'mandant_id' => $mandantId,
            'objekt_id' => $data['objekt_id'],
            'jahr' => $data['jahr'],
            'verteilungs_art' => NkAbrechnung::VERTEILUNG_GLEICH_PRO_EINHEIT,
            'status' => $data['status'] ?? NkAbrechnung::STATUS_DRAFT,
        ]);

        foreach ($data['positionen'] as $row) {
            $abrechnung->positionen()->create([
                'beschreibung' => $row['beschreibung'],
                'betrag_cent' => (int) round(((float) $row['betrag']) * 100),
            ]);
        }

        return redirect()->route('nk-abrechnungen.show', $abrechnung)->with('status', 'nk-saved');
    }

    public function show(NkAbrechnung $nk_abrechnung): View
    {
        $this->authorize('view', $nk_abrechnung);

        $nk_abrechnung->load(['objekt.einheiten', 'positionen']);

        return view('nk-abrechnungen.show', ['abrechnung' => $nk_abrechnung]);
    }

    public function pdf(NkAbrechnung $nk_abrechnung): Response
    {
        $this->authorize('exportPdf', $nk_abrechnung);

        $nk_abrechnung->load(['objekt.einheiten', 'positionen']);

        $pdf = Pdf::loadView('pdf.nk-jahresabrechnung', [
            'abrechnung' => $nk_abrechnung,
            'totalCent' => $nk_abrechnung->totalCent(),
            'einheitenCount' => $nk_abrechnung->einheitenCount(),
            'anteilCent' => $nk_abrechnung->anteilProEinheitCent(),
        ]);

        $filename = sprintf('jahresabrechnung-%s-%d.pdf', $nk_abrechnung->objekt->name, $nk_abrechnung->jahr);
        $filename = preg_replace('/[^\p{L}\p{N}\.\-\_]/u', '_', $filename) ?: 'jahresabrechnung.pdf';

        return $pdf->download($filename);
    }
}
