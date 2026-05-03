<?php

namespace Database\Factories;

use App\Models\Mandant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mandant>
 */
class MandantFactory extends Factory
{
    protected $model = Mandant::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
        ];
    }
}
