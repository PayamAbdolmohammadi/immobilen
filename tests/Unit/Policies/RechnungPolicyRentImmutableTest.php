<?php

namespace Tests\Unit\Policies;

use App\Domain\Leasing\MietvertragService;
use App\Models\Einheit;
use App\Models\Mandant;
use App\Models\Mieter;
use App\Models\Mietvertrag;
use App\Models\Objekt;
use App\Models\Rechnung;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RechnungPolicyRentImmutableTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function rent_invoice_cannot_be_updated_via_policy(): void
    {
        $mandant = Mandant::factory()->create();
        $user = User::factory()->create(['mandant_id' => $mandant->id]);
        $objekt = Objekt::factory()->create(['mandant_id' => $mandant->id]);
        $einheit = Einheit::factory()->create([
            'mandant_id' => $mandant->id,
            'objekt_id' => $objekt->id,
        ]);
        $mieter = Mieter::factory()->create(['mandant_id' => $mandant->id]);

        $leases = new MietvertragService;
        $lease = $leases->createLease([
            'mandant_id' => $mandant->id,
            'einheit_id' => $einheit->id,
            'mieter_id' => $mieter->id,
            'starts_on' => '2026-01-01',
            'ends_on' => null,
            'status' => Mietvertrag::STATUS_ACTIVE,
            'kaltmiete_cent' => 80_000,
            'nebenkosten_vorauszahlung_cent' => 20_000,
            'zahlungsintervall' => 'monthly',
            'faelligkeit_tag' => null,
            'next_billing_period' => null,
            'last_billed_at' => null,
        ]);

        $rechnung = Rechnung::query()->create([
            'mandant_id' => $mandant->id,
            'mieter_id' => $mieter->id,
            'einheit_id' => $einheit->id,
            'mietvertrag_id' => $lease->id,
            'typ' => Rechnung::TYP_RENT,
            'billing_period' => '2026-05',
            'betrag_cent' => 100_000,
            'source_data' => ['total_cent' => 100_000],
            'status' => Rechnung::STATUS_OFFEN,
            'faellig_am' => '2026-05-31',
            'bezahlt_am' => null,
        ]);

        $this->assertFalse($user->can('update', $rechnung));
    }
}
