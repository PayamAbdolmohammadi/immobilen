<?php

namespace Database\Factories;

use App\Models\Einheit;
use App\Models\Mandant;
use App\Models\Mieter;
use App\Models\Mietvertrag;
use App\Models\Objekt;
use App\Models\Rechnung;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rechnung>
 */
class RechnungFactory extends Factory
{
    protected $model = Rechnung::class;

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
            'mieter_id' => $mieter->id,
            'einheit_id' => $einheit->id,
            'mietvertrag_id' => null,
            'typ' => Rechnung::TYP_MANUAL,
            'billing_period' => null,
            'betrag_cent' => 50_000,
            'source_data' => null,
            'status' => Rechnung::STATUS_OFFEN,
            'faellig_am' => now()->addDays(14)->toDateString(),
            'bezahlt_am' => null,
        ];
    }
}
