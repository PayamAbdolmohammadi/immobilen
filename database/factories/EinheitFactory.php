<?php

namespace Database\Factories;

use App\Models\Einheit;
use App\Models\Mandant;
use App\Models\Objekt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Einheit>
 */
class EinheitFactory extends Factory
{
    protected $model = Einheit::class;

    public function definition(): array
    {
        return [
            'mandant_id' => Mandant::factory(),
            'objekt_id' => Objekt::factory(),
            'name' => 'WE '.fake()->numberBetween(1, 48),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Einheit $einheit): void {
            $objekt = Objekt::query()->find($einheit->objekt_id);
            if ($objekt && (int) $objekt->mandant_id !== (int) $einheit->mandant_id) {
                $objekt->mandant_id = $einheit->mandant_id;
                $objekt->save();
            }
        });
    }
}
