<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'jumlah' => fake()->numberBetween(1, 5),
            'subtotal' => fake()->numberBetween(15_000, 500_000),
            'deskripsi_custom' => null,
            'foto_referensi' => null,
            'ukuran_kue' => null,
            'rasa_kue' => null,
        ];
    }

    /**
     * Tandai item sebagai bagian dari pesanan custom cake.
     */
    public function custom(): static
    {
        return $this->state(fn (array $attributes): array => [
            'product_id' => null,
            'deskripsi_custom' => fake()->sentence(),
            'ukuran_kue' => fake()->randomElement(['16 cm', '18 cm', '20 cm', '22 cm']),
            'rasa_kue' => fake()->randomElement(['Cokelat', 'Vanilla', 'Red Velvet', 'Taro']),
        ]);
    }
}
