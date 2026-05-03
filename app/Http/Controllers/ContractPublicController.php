<?php

namespace App\Http\Controllers;

use App\Models\ContractEvent;
use App\Models\Mietvertrag;
use App\Services\Contract\ContractEventService;
use App\Services\Contract\ContractLinkService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ContractPublicController extends Controller
{
    public function show(string $token, ContractLinkService $links): View
    {
        $lease = $links->validateToken($token);
        abort_if($lease === null, 404);

        $lease->load(['mieter', 'einheit.objekt']);

        return view('contracts.public', [
            'token' => $token,
            'vertrag' => $lease,
        ]);
    }

    public function pdf(string $token, ContractLinkService $links): BinaryFileResponse
    {
        $lease = $links->validateToken($token);
        abort_if($lease === null || empty($lease->contract_pdf_path), 404);

        $path = Storage::disk('local')->path($lease->contract_pdf_path);
        abort_if(! is_file($path), 404);

        return response()->download($path, 'mietvertrag-'.$lease->id.'.pdf');
    }

    public function accept(Request $request, string $token, ContractLinkService $links, ContractEventService $events): RedirectResponse
    {
        $lease = $links->validateToken($token);
        abort_if($lease === null, 404);

        if ($lease->accepted_at !== null && $lease->status === Mietvertrag::STATUS_ACTIVE) {
            return redirect()->route('contracts.public.show', ['token' => $token])
                ->with('status', 'contract-already-accepted');
        }

        abort_unless(in_array($lease->status, [Mietvertrag::STATUS_DRAFT, Mietvertrag::STATUS_SENT], true), 403);

        $request->validate([
            'accepted' => ['accepted'],
        ]);

        $lease->update([
            'status' => Mietvertrag::STATUS_ACTIVE,
            'accepted_at' => now(),
            'activated_at' => now(),
        ]);

        $events->log($lease, 'accepted', ContractEvent::ACTOR_TENANT, null, null, $request);

        return redirect()->route('contracts.public.show', ['token' => $token])
            ->with('status', 'contract-accepted');
    }
}
