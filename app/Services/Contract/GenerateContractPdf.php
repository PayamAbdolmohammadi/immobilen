<?php

namespace App\Services\Contract;

use App\Domain\Billing\RentAmountCalculator;
use App\Models\ContractEvent;
use App\Models\Mietvertrag;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

final class GenerateContractPdf
{
    public function generate(Mietvertrag $vertrag): string
    {
        $vertrag->load(['mandant', 'mieter', 'einheit.objekt']);

        $calculator = new RentAmountCalculator;
        $snapshot = $calculator->forLease($vertrag);
        $totalEuro = number_format($snapshot['total_cent'] / 100, 2, ',', '.');

        $attrs = $vertrag->getAttributes();

        $einheit = $vertrag->einheit;
        $einheitAttrs = $einheit?->getAttributes() ?? [];
        $zimmeranzahl = isset($einheitAttrs['zimmeranzahl'])
            ? trim((string) $einheit->zimmeranzahl)
            : null;

        $objekt = $einheit?->objekt;
        $objAttrs = $objekt?->getAttributes() ?? [];
        $objekt_adresse = isset($objAttrs['adresse'])
            ? trim((string) $objekt->adresse)
            : null;

        $mieterModel = $vertrag->mieter;
        $mieterAttrs = $mieterModel?->getAttributes() ?? [];
        $mieter_adresse = isset($mieterAttrs['adresse'])
            ? trim((string) $mieterModel->adresse)
            : null;

        $acceptedAuditEvent = ContractEvent::query()
            ->where('mietvertrag_id', $vertrag->id)
            ->where('event_type', 'accepted')
            ->latest()
            ->first();

        $pdf = Pdf::loadView('contracts.pdf', [
            'vertrag' => $vertrag,
            'totalEuro' => $totalEuro,
            'pdfGeneratedAt' => now(),
            'acceptedAuditEvent' => $acceptedAuditEvent,
            'kaution_cent' => isset($attrs['kaution_cent']) ? (int) $attrs['kaution_cent'] : null,
            'additional_notes' => isset($attrs['additional_notes']) ? trim((string) $attrs['additional_notes']) : null,
            'zimmeranzahl' => $zimmeranzahl !== '' ? $zimmeranzahl : null,
            'mieter_adresse' => $mieter_adresse !== '' ? $mieter_adresse : null,
            'objekt_adresse' => $objekt_adresse !== '' ? $objekt_adresse : null,
        ]);

        $relativePath = sprintf('contracts/%d/%d.pdf', $vertrag->mandant_id, $vertrag->id);
        $disk = Storage::disk('local');
        File::ensureDirectoryExists(dirname($disk->path($relativePath)));
        $disk->put($relativePath, $pdf->output());

        $vertrag->update([
            'contract_pdf_path' => $relativePath,
        ]);

        return $relativePath;
    }
}
