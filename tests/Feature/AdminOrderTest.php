<?php

namespace Tests\Feature;

use App\Enums\StatusPesanan;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_incoming_orders_with_status(): void
    {
        $seller = User::factory()->create();
        $order = Order::factory()->create(['nama_pemesan' => 'Budi Santoso']);

        $response = $this->actingAs($seller)->get(route('admin.pesanan.index'));

        $response->assertOk();
        $response->assertSee('Budi Santoso');
        $response->assertSee($order->kode_pesanan);
        $response->assertSee(StatusPesanan::Pending->value);
    }

    public function test_update_status_changes_the_order_status(): void
    {
        $seller = User::factory()->create();
        $order = Order::factory()->create(['status' => StatusPesanan::Pending]);

        $response = $this->actingAs($seller)->patch(route('admin.pesanan.update-status', $order), [
            'status' => StatusPesanan::Diproses->value,
        ]);

        $response->assertRedirect(route('admin.pesanan.index'));
        $this->assertSame(StatusPesanan::Diproses, $order->fresh()->status);
    }

    public function test_update_status_rejects_invalid_value(): void
    {
        $seller = User::factory()->create();
        $order = Order::factory()->create();

        $response = $this->actingAs($seller)->patch(route('admin.pesanan.update-status', $order), [
            'status' => 'status-ngawur',
        ]);

        $response->assertSessionHasErrors('status');
    }
}
