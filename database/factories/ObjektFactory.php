<?php

namespace Database\Factories;

use App\Models\Mandant;
use App\Models\Objekt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Objekt>
 */
class ObjektFactory extends Factory
{
    protected $model = Objekt::class;

    public function definition(): array
    {
        return [
            'mandant_id' => Mandant::factory(),
            'name' => fake()->streetName(),
        ];
    }
}
