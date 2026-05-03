<?php

namespace Database\Factories;

use App\Models\Mieter;
use App\Models\Mandant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mieter>
 */
class MieterFactory extends Factory
{
    protected $model = Mieter::class;

    public function definition(): array
    {
        return [
            'mandant_id' => Mandant::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
        ];
    }
}
