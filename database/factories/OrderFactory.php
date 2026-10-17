<?php

namespace Database\Factories;

use App\Enums\MetodePembayaran;
use App\Enums\OpsiPengiriman;
use App\Enums\StatusPesanan;
use App\Enums\TipePesanan;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_pesanan' => 'ORD-'.now()->format('Ymd').'-'.fake()->unique()->numerify('####'),
            'tipe_pesanan' => TipePesanan::Reguler,
            'nama_pemesan' => fake()->name(),
            'nomor_wa' => fake()->numerify('08##########'),
            'alamat_lengkap' => fake()->address(),
            'opsi_pengiriman' => fake()->randomElement(OpsiPengiriman::cases()),
            'metode_pembayaran' => fake()->randomElement(MetodePembayaran::cases()),
            'total_harga' => 0,
            'status' => StatusPesanan::Pending,
            'catatan' => null,
        ];
    }

    /**
     * Tandai pesanan sebagai pesanan custom cake.
     */
    public function custom(): static
    {
        return $this->state(fn (array $attributes): array => [
            'tipe_pesanan' => TipePesanan::Custom,
        ]);
    }

    /**
     * Tandai pesanan berstatus selesai.
     */
    public function selesai(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => StatusPesanan::Selesai,
        ]);
    }
}
