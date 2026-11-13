<?php

namespace Database\Factories;

use App\Enums\MetodePembayaran;
use App\Enums\StatusPembayaran;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
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
            'payment_reference' => 'PAY-'.now()->format('Ymd').'-'.fake()->unique()->numerify('####'),
            'payment_method' => MetodePembayaran::TransferBank->value,
            'amount' => fake()->numberBetween(10_000, 1_000_000),
            'status' => StatusPembayaran::Pending,
            'payment_proof' => null,
            'gateway_response' => null,
            'paid_at' => null,
        ];
    }

    /**
     * Tandai pembayaran sebagai lunas.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => StatusPembayaran::Paid,
            'paid_at' => now(),
        ]);
    }

    /**
     * Tandai pembayaran sebagai gagal.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => StatusPembayaran::Failed,
        ]);
    }
}
