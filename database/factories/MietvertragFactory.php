<?php

namespace Database\Factories;

use App\Models\Einheit;
use App\Models\Mandant;
use App\Models\Mieter;
use App\Models\Mietvertrag;
use App\Models\Objekt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mietvertrag>
 */
class MietvertragFactory extends Factory
{
    protected $model = Mietvertrag::class;

    public function definition(): array
    {
        $mandant = Mandant::factory()->create();
        $objekt = Objekt::factory()->create(['mandant_id' => $mandant->id]);
        $einheit = Einheit::factory()->create([
            'mandant_id' => $mandant->id,
            'objekt_id' => $objekt->id,
        ]);
        $mieter = Mieter::factory()->create(['mandant_id' => $mandant->id]);

        return [
            'mandant_id' => $mandant->id,
            'einheit_id' => $einheit->id,
            'mieter_id' => $mieter->id,
            'starts_on' => now()->startOfMonth()->toDateString(),
            'ends_on' => null,
            'status' => Mietvertrag::STATUS_ACTIVE,
            'kaltmiete_cent' => 80_000,
            'nebenkosten_vorauszahlung_cent' => 20_000,
            'zahlungsintervall' => 'monthly',
            'faelligkeit_tag' => null,
            'next_billing_period' => null,
            'last_billed_at' => null,
        ];
    }
}
