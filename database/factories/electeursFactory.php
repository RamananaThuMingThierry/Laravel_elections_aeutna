<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class electeursFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero_carte' => fake()->unique()->numberBetween(1, 1000),
            'nom' => fake()->name(),
            'prenom' => fake()->userName(),
            'date_inscription' => Carbon::parse('1999-02-03')
        ];
    }
}
