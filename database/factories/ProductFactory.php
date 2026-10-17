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
        $stok = fake()->numberBetween(0, 50);

        return [
            'category_id' => Category::factory(),
            'nama_kue' => Str::title($namaKue),
            'slug' => Str::slug($namaKue),
            'deskripsi' => fake()->sentence(),
            'harga' => fake()->numberBetween(15_000, 500_000),
            'stok' => $stok,
            'status_stok' => $stok > 0 ? StatusStok::Ready : StatusStok::Habis,
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
