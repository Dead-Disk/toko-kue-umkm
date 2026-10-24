<?php

namespace Database\Factories;

use App\Enums\StatusStok;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $namaKue = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'nama_kue' => Str::title($namaKue),
            'slug' => Str::slug($namaKue),
            'deskripsi' => fake()->sentence(),
            'harga' => fake()->numberBetween(15_000, 500_000),
            'stok' => fake()->numberBetween(10, 50),
            'max_per_order' => 5,
            'status_stok' => StatusStok::Ready,
            'foto' => null,
        ];
    }

    /**
     * Tandai produk sedang habis (stok 0).
     */
    public function habis(): static
    {
        return $this->state(fn (array $attributes): array => [
            'stok' => 0,
            'status_stok' => StatusStok::Habis,
        ]);
    }
}
