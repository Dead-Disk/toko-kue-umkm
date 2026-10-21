<?php

namespace Database\Factories;

use App\Models\StoreSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StoreSetting>
 */
class StoreSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_name' => 'Toko Kue',
            'store_description' => 'Kue rumahan dibuat segar setiap hari.',
            'store_address' => fake()->address(),
            'store_phone' => fake()->numerify('08##########'),
            'bank_name' => 'BNI',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Toko Kue',
            'qris_image_path' => null,
        ];
    }

    /**
     * Toko dengan gambar QRIS siap dipakai.
     */
    public function withQris(): static
    {
        return $this->state(fn (array $attributes): array => [
            'qris_image_path' => 'qris/qris-toko.png',
        ]);
    }

    /**
     * Toko yang belum mengonfigurasi pembayaran transfer maupun QRIS.
     */
    public function unconfigured(): static
    {
        return $this->state(fn (array $attributes): array => [
            'bank_name' => null,
            'bank_account_number' => null,
            'bank_account_holder' => null,
            'qris_image_path' => null,
        ]);
    }
}
