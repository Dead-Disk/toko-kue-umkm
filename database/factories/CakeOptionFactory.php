<?php

namespace Database\Factories;

use App\Enums\BentukKue;
use App\Models\CakeOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CakeOption>
 */
class CakeOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bentuk' => fake()->randomElement(BentukKue::cases()),
            'ukuran' => fake()->randomElement(['16 cm', '18 cm', '20 cm', '22 cm', '24 cm']),
            'harga_dasar' => fake()->numberBetween(100_000, 350_000),
        ];
    }
}
