<?php

namespace App\Http\Controllers;

use App\Domain\Leasing\MietvertragService;
use App\Http\Controllers\Concerns\ResolvesCurrentMandant;
use App\Models\ContractEvent;
use App\Models\Einheit;
use App\Models\Mietvertrag;
use App\Models\Mieter;
use App\Services\Contract\ContractEventService;
use App\Services\Contract\ContractLinkService;
use App\Services\Contract\GenerateContractPdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MietvertragController extends Controller
{
    use AuthorizesRequests;
    use ResolvesCurrentMandant;

    public function index(): View
    {
        $this->authorize('viewAny', Mietvertrag::class);

        $mandantId = $this->mandantId();

        $mietvertraege = Mietvertrag::query()
            ->where('mandant_id', $mandantId)
            ->with(['einheit.objekt', 'mieter'])
            ->orderByDesc('starts_on')
            ->paginate(20);

        return view('mietvertraege.index', compact('mietvertraege'));
    }

    public function create(): View
    {
        $this->authorize('create', Mietvertrag::class);

        $mandantId = $this->mandantId();

        $busyEinheitIds = Mietvertrag::query()
            ->where('mandant_id', $mandantId)
            ->whereIn('status', [
                Mietvertrag::STATUS_ACTIVE,
                Mietvertrag::STATUS_DRAFT,
                Mietvertrag::STATUS_SENT,
            ])
            ->pluck('einheit_id');

        $einheiten = Einheit::query()
            ->where('mandant_id', $mandantId)
            ->whereNotIn('id', $busyEinheitIds)
            ->with('objekt')
            ->orderBy('name')
            ->get();

        $mieter = Mieter::query()->where('mandant_id', $mandantId)->orderBy('name')->get();

        return view('mietvertraege.create', compact('einheiten', 'mieter'));
    }

    public function store(Request $request, MietvertragService $leases): RedirectResponse
    {
        $this->authorize('create', Mietvertrag::class);

        $mandantId = $this->mandantId($request);

        $data = $request->validate([
            'einheit_id' => ['required', 'integer', Rule::exists('einheiten', 'id')->where('mandant_id', $mandantId)],
            'mieter_id' => ['required', 'integer', Rule::exists('mieter', 'id')->where('mandant_id', $mandantId)],
            'starts_on' => ['required', 'date'],
            'ends_on' => ['nullable', 'date'],
            'kaltmiete' => ['required', 'numeric', 'min:0'],
            'nk_vorauszahlung' => ['required', 'numeric', 'min:0'],
        ]);

        if (! empty($data['ends_on']) && Carbon::parse($data['ends_on'])->lt(Carbon::parse($data['starts_on']))) {
            return back()->withErrors(['ends_on' => __('End date must be on or after start date.')])->withInput();
        }

        try {
            $leases->createLease([
                'mandant_id' => $mandantId,
                'einheit_id' => $data['einheit_id'],
                'mieter_id' => $data['mieter_id'],
                'starts_on' => $data['starts_on'],
                'ends_on' => $data['ends_on'] ?? null,
                'status' => Mietvertrag::STATUS_DRAFT,
                'kaltmiete_cent' => (int) round(((float) $data['kaltmiete']) * 100),
                'nebenkosten_vorauszahlung_cent' => (int) round(((float) $data['nk_vorauszahlung']) * 100),
                'zahlungsintervall' => 'monthly',
                'faelligkeit_tag' => null,
                'next_billing_period' => null,
                'last_billed_at' => null,
            ]);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['einheit_id' => $e->getMessage()])->withInput();
        }

        return redirect()->route('mietvertraege.index')->with('status', 'lease-saved');
    }

    public function show(Mietvertrag $mietvertrag): View
    {
        $this->authorize('view', $mietvertrag);

        $mietvertrag->load(['mieter', 'einheit.objekt', 'contractEvents']);

        return view('mietvertraege.show', ['mietvertrag' => $mietvertrag]);
    }

    public function generatePdf(
        Request $request,
        Mietvertrag $mietvertrag,
        GenerateContractPdf $generator,
        ContractEventService $events,
    ): RedirectResponse {
        $this->authorize('view', $mietvertrag);
        abort_if($mietvertrag->status === Mietvertrag::STATUS_ENDED, 403);

        $generator->generate($mietvertrag);
        $mietvertrag->refresh();

        $events->log($mietvertrag, 'pdf_generated', ContractEvent::ACTOR_OWNER, $request->user()->id, null, $request);

        return redirect()
            ->route('mietvertraege.show', $mietvertrag)
            ->with('status', 'contract-pdf-generated');
    }

    public function createContractLink(
        Request $request,
        Mietvertrag $mietvertrag,
        ContractLinkService $links,
        ContractEventService $events,
    ): RedirectResponse {
        $this->authorize('view', $mietvertrag);
        abort_if($mietvertrag->status === Mietvertrag::STATUS_ENDED, 403);

        $plain = $links->createToken($mietvertrag);
        $mietvertrag->refresh();
        $events->log($mietvertrag, 'link_created', ContractEvent::ACTOR_OWNER, $request->user()->id, null, $request);

        return redirect()
            ->route('mietvertraege.show', $mietvertrag)
            ->with('contract_public_url', route('contracts.public.show', ['token' => $plain], absolute: true))
            ->with('status', 'contract-link-created');
    }

    public function activateContract(
        Request $request,
        Mietvertrag $mietvertrag,
        ContractEventService $events,
    ): RedirectResponse {
        $this->authorize('view', $mietvertrag);

        abort_unless(in_array($mietvertrag->status, [Mietvertrag::STATUS_DRAFT, Mietvertrag::STATUS_SENT], true), 403);

        $mietvertrag->update([
            'status' => Mietvertrag::STATUS_ACTIVE,
            'activated_at' => now(),
        ]);
        $mietvertrag->refresh();

        $events->log($mietvertrag, 'activated_manual', ContractEvent::ACTOR_OWNER, $request->user()->id, null, $request);

        return redirect()
            ->route('mietvertraege.show', $mietvertrag)
            ->with('status', 'contract-activated-manual');
    }

    public function edit(Mietvertrag $mietvertrag): View
    {
        $this->authorize('update', $mietvertrag);

        return view('mietvertraege.edit', [
            'mietvertrag' => $mietvertrag,
            'kaltmiete_euro' => $mietvertrag->kaltmiete_cent / 100,
            'nk_euro' => $mietvertrag->nebenkosten_vorauszahlung_cent / 100,
        ]);
    }

    public function update(Request $request, Mietvertrag $mietvertrag, MietvertragService $leases): RedirectResponse
    {
        $this->authorize('update', $mietvertrag);

        $data = $request->validate([
            'kaltmiete' => ['required', 'numeric', 'min:0'],
            'nk_vorauszahlung' => ['required', 'numeric', 'min:0'],
            'ends_on' => ['nullable', 'date'],
            'faelligkeit_tag' => ['nullable', 'integer', 'min:1', 'max:31'],
            'zahlungsintervall' => ['nullable', 'string', 'max:32'],
        ]);

        try {
            $leases->updateContract($mietvertrag, [
                'kaltmiete_cent' => (int) round(((float) $data['kaltmiete']) * 100),
                'nebenkosten_vorauszahlung_cent' => (int) round(((float) $data['nk_vorauszahlung']) * 100),
                'ends_on' => $data['ends_on'] ?? null,
                'faelligkeit_tag' => $data['faelligkeit_tag'] ?? null,
                'zahlungsintervall' => $data['zahlungsintervall'] ?? $mietvertrag->zahlungsintervall,
            ]);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['kaltmiete' => $e->getMessage()])->withInput();
        }

        return redirect()->route('mietvertraege.index')->with('status', 'lease-updated');
    }

    public function end(Mietvertrag $mietvertrag): View
    {
        $this->authorize('end', $mietvertrag);

        $mietvertrag->load(['einheit', 'mieter']);

        return view('mietvertraege.end', ['mietvertrag' => $mietvertrag]);
    }

    public function endStore(Request $request, Mietvertrag $mietvertrag, MietvertragService $leases): RedirectResponse
    {
        $this->authorize('end', $mietvertrag);

        $data = $request->validate([
            'ended_on' => ['required', 'date'],
        ]);

        try {
            $leases->endContract($mietvertrag, Carbon::parse($data['ended_on']));
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['ended_on' => $e->getMessage()])->withInput();
        }

        return redirect()->route('mietvertraege.index')->with('status', 'lease-ended');
    }
}
