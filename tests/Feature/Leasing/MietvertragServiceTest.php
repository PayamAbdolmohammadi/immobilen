<?php

namespace Tests\Feature\Leasing;

use App\Domain\Leasing\MietvertragService;
use App\Models\Einheit;
use App\Models\Mandant;
use App\Models\Mieter;
use App\Models\Mietvertrag;
use App\Models\Objekt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MietvertragServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function duplicate_active_contract_for_same_unit_fails(): void
    {
        $mandant = Mandant::factory()->create();
        $objekt = Objekt::factory()->create(['mandant_id' => $mandant->id]);
        $einheit = Einheit::factory()->create([
            'mandant_id' => $mandant->id,
            'objekt_id' => $objekt->id,
        ]);
        $mieter = Mieter::factory()->create(['mandant_id' => $mandant->id]);

        $service = new MietvertragService;

        $base = [
            'mandant_id' => $mandant->id,
            'einheit_id' => $einheit->id,
            'mieter_id' => $mieter->id,
            'starts_on' => '2026-01-01',
            'ends_on' => null,
            'status' => Mietvertrag::STATUS_ACTIVE,
            'kaltmiete_cent' => 50_000,
            'nebenkosten_vorauszahlung_cent' => 10_000,
            'zahlungsintervall' => 'monthly',
            'faelligkeit_tag' => null,
            'next_billing_period' => null,
            'last_billed_at' => null,
        ];

        $service->createLease($base);

        $this->expectException(\InvalidArgumentException::class);

        $otherMieter = Mieter::factory()->create(['mandant_id' => $mandant->id]);
        $service->createLease([...$base, 'mieter_id' => $otherMieter->id]);
    }

    #[Test]
    public function end_contract_marks_lease_ended(): void
    {
        $mandant = Mandant::factory()->create();
        $objekt = Objekt::factory()->create(['mandant_id' => $mandant->id]);
        $einheit = Einheit::factory()->create([
            'mandant_id' => $mandant->id,
            'objekt_id' => $objekt->id,
        ]);
        $mieter = Mieter::factory()->create(['mandant_id' => $mandant->id]);

        $service = new MietvertragService;

        $lease = $service->createLease([
            'mandant_id' => $mandant->id,
            'einheit_id' => $einheit->id,
            'mieter_id' => $mieter->id,
            'starts_on' => '2026-01-01',
            'ends_on' => null,
            'status' => Mietvertrag::STATUS_ACTIVE,
            'kaltmiete_cent' => 50_000,
            'nebenkosten_vorauszahlung_cent' => 10_000,
            'zahlungsintervall' => 'monthly',
            'faelligkeit_tag' => null,
            'next_billing_period' => null,
            'last_billed_at' => null,
        ]);

        $service->endContract($lease, new \DateTimeImmutable('2026-06-30'));

        $lease->refresh();
        $this->assertSame(Mietvertrag::STATUS_ENDED, $lease->status);
        $this->assertSame('2026-06-30', $lease->ends_on->format('Y-m-d'));
    }

    #[Test]
    public function update_contract_changes_amounts_on_active_lease(): void
    {
        $mandant = Mandant::factory()->create();
        $objekt = Objekt::factory()->create(['mandant_id' => $mandant->id]);
        $einheit = Einheit::factory()->create([
            'mandant_id' => $mandant->id,
            'objekt_id' => $objekt->id,
        ]);
        $mieter = Mieter::factory()->create(['mandant_id' => $mandant->id]);

        $service = new MietvertragService;

        $lease = $service->createLease([
            'mandant_id' => $mandant->id,
            'einheit_id' => $einheit->id,
            'mieter_id' => $mieter->id,
            'starts_on' => '2026-01-01',
            'ends_on' => null,
            'status' => Mietvertrag::STATUS_ACTIVE,
            'kaltmiete_cent' => 50_000,
            'nebenkosten_vorauszahlung_cent' => 10_000,
            'zahlungsintervall' => 'monthly',
            'faelligkeit_tag' => null,
            'next_billing_period' => null,
            'last_billed_at' => null,
        ]);

        $service->updateContract($lease, [
            'kaltmiete_cent' => 55_000,
            'nebenkosten_vorauszahlung_cent' => 12_000,
        ]);

        $lease->refresh();
        $this->assertSame(55_000, (int) $lease->kaltmiete_cent);
        $this->assertSame(12_000, (int) $lease->nebenkosten_vorauszahlung_cent);
    }
}
