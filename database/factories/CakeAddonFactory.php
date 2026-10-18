<?php

namespace Database\Factories;

use App\Models\CakeAddon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CakeAddon>
 */
class CakeAddonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_addon' => fake()->unique()->words(2, true),
            'harga_tambahan' => fake()->numberBetween(10_000, 50_000),
        ];
    }
}
