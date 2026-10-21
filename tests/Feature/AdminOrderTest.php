<?php

namespace Tests\Feature;

use App\Enums\StatusPesanan;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
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

    public function test_index_filters_orders_by_status_param(): void
    {
        $seller = User::factory()->create();
        $pending = Order::factory()->create(['status' => StatusPesanan::Pending]);
        $selesai = Order::factory()->selesai()->create();

        $response = $this->actingAs($seller)->get(route('admin.pesanan.index', ['status' => 'pending']));

        $response->assertOk();
        $response->assertSee($pending->kode_pesanan);
        $response->assertDontSee($selesai->kode_pesanan);
    }

    public function test_index_combines_status_and_date_filters(): void
    {
        $seller = User::factory()->create();
        $masuk = Order::factory()->selesai()->create(['created_at' => '2026-10-05 09:00:00']);
        $terlambat = Order::factory()->selesai()->create(['created_at' => '2026-10-20 09:00:00']);
        $pending = Order::factory()->create(['created_at' => '2026-10-05 09:00:00']);

        $response = $this->actingAs($seller)->get(route('admin.pesanan.index', [
            'status' => 'selesai',
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-10',
        ]));

        $response->assertOk();
        $response->assertSee($masuk->kode_pesanan);
        $response->assertDontSee($terlambat->kode_pesanan);
        $response->assertDontSee($pending->kode_pesanan);
    }

    public function test_destroy_soft_deletes_order(): void
    {
        $seller = User::factory()->create();
        $order = Order::factory()->selesai()->create();

        $response = $this->actingAs($seller)->delete(
            route('admin.pesanan.destroy', $order),
            ['status' => 'selesai', 'start_date' => '2026-10-01'],
        );

        $response->assertRedirect(route('admin.pesanan.index', ['status' => 'selesai', 'start_date' => '2026-10-01']));
        $this->assertSoftDeleted('orders', ['id' => $order->id]);

        // Flush flash 'success' (berisi kode pesanan) supaya GET berikutnya
        // benar-benar menguji tabel, bukan pesan notifikasi.
        $this->flushSession();

        $this->actingAs($seller)
            ->get(route('admin.pesanan.index'))
            ->assertDontSee($order->kode_pesanan);
    }

    public function test_index_filters_orders_by_type_param(): void
    {
        $seller = User::factory()->create();
        $reguler = Order::factory()->create();
        $custom = Order::factory()->custom()->create();

        $response = $this->actingAs($seller)->get(route('admin.pesanan.index', ['type' => 'custom']));

        $response->assertOk();
        $response->assertSee('Custom Cake');
        $response->assertSee($custom->kode_pesanan);
        $response->assertDontSee($reguler->kode_pesanan);
    }

    public function test_index_shows_type_badges_for_reguler_and_custom_orders(): void
    {
        $seller = User::factory()->create();
        Order::factory()->create();
        Order::factory()->custom()->create();

        $response = $this->actingAs($seller)->get(route('admin.pesanan.index'));

        $response->assertOk();
        $response->assertSee('Kue Jadi');
        $response->assertSee('Custom Cake');
    }

    public function test_index_lists_each_order_item_name_and_quantity(): void
    {
        $seller = User::factory()->create();
        $tart = Product::factory()->create(['nama_kue' => 'Kue Water']);
        $bolu = Product::factory()->create(['nama_kue' => 'Bolu Pandan']);
        $order = Order::factory()->create();
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $tart->id, 'jumlah' => 2]);
        OrderItem::factory()->create(['order_id' => $order->id, 'product_id' => $bolu->id, 'jumlah' => 1]);

        $response = $this->actingAs($seller)->get(route('admin.pesanan.index'));

        $response->assertOk();
        $response->assertSee('3 Pcs (2 jenis)');
        $response->assertSee('Kue Water');
        $response->assertSee('Bolu Pandan');
        $response->assertSeeInOrder(['2x', 'Kue Water']);
    }

    public function test_index_shows_the_order_note_when_present(): void
    {
        $seller = User::factory()->create();
        Order::factory()->create(['catatan' => 'Tolong tambahkan lilin angka 18']);

        $response = $this->actingAs($seller)->get(route('admin.pesanan.index'));

        $response->assertOk();
        $response->assertSee('Catatan:');
        $response->assertSee('Tolong tambahkan lilin angka 18');
    }

    public function test_index_shows_a_payment_proof_preview(): void
    {
        $seller = User::factory()->create();
        $order = Order::factory()->create(['bukti_pembayaran' => 'bukti_pembayaran/bukti.png']);

        $response = $this->actingAs($seller)->get(route('admin.pesanan.index'));

        $response->assertOk();
        $response->assertSee('Bukti Pembayaran');
        $response->assertSee(route('admin.orders.bukti', $order), false);
    }

    public function test_admin_can_view_the_private_payment_proof(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('bukti_pembayaran/bukti.png', 'gambar');

        $seller = User::factory()->create();
        $order = Order::factory()->create(['bukti_pembayaran' => 'bukti_pembayaran/bukti.png']);

        $this->actingAs($seller)
            ->get(route('admin.orders.bukti', $order))
            ->assertOk();
    }

    public function test_staff_can_view_the_private_custom_photo(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('custom_cakes/kue.png', 'gambar');

        $staff = User::factory()->staff()->create();
        $order = Order::factory()->custom()->create();
        OrderItem::factory()->custom()->create([
            'order_id' => $order->id,
            'foto_referensi' => 'custom_cakes/kue.png',
        ]);

        $this->actingAs($staff)
            ->get(route('admin.orders.custom_photo', $order))
            ->assertOk();
    }

    public function test_guest_cannot_view_private_uploads(): void
    {
        $order = Order::factory()->create(['bukti_pembayaran' => 'bukti_pembayaran/bukti.png']);

        $this->get(route('admin.orders.bukti', $order))->assertRedirect(route('login'));
        $this->get(route('admin.orders.custom_photo', $order))->assertRedirect(route('login'));
    }

    public function test_private_upload_route_returns_not_found_when_file_is_missing(): void
    {
        Storage::fake('local');

        $seller = User::factory()->create();
        $order = Order::factory()->create(['bukti_pembayaran' => 'bukti_pembayaran/hilang.png']);

        $this->actingAs($seller)
            ->get(route('admin.orders.bukti', $order))
            ->assertNotFound();
    }

    public function test_update_status_changes_the_order_status(): void
    {
        $seller = User::factory()->create();
        $order = Order::factory()->create(['status' => StatusPesanan::Pending]);

        $response = $this->actingAs($seller)->patch(route('admin.pesanan.update-status', $order), [
            'status' => StatusPesanan::Proses->value,
        ]);

        $response->assertRedirect(route('admin.pesanan.index'));
        $this->assertSame(StatusPesanan::Proses, $order->fresh()->status);
    }

    public function test_terima_memotong_stok_dan_batal_mengembalikannya(): void
    {
        $seller = User::factory()->create();
        $product = Product::factory()->create(['stok' => 10]);
        $order = Order::factory()->create(['status' => StatusPesanan::Pending]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'jumlah' => 3,
        ]);

        $this->actingAs($seller)->patch(route('admin.pesanan.update-status', $order), [
            'status' => StatusPesanan::Proses->value,
        ]);

        $this->assertSame(7, $product->fresh()->stok);
        $this->assertSame(StatusPesanan::Proses, $order->fresh()->status);

        $this->actingAs($seller)->patch(route('admin.pesanan.update-status', $order), [
            'status' => StatusPesanan::Batal->value,
        ]);

        $this->assertSame(10, $product->fresh()->stok);
        $this->assertSame(StatusPesanan::Batal, $order->fresh()->status);
    }

    public function test_terima_dengan_stok_kurang_mengembalikan_error(): void
    {
        $seller = User::factory()->create();
        $product = Product::factory()->create(['stok' => 2]);
        $order = Order::factory()->create(['status' => StatusPesanan::Pending]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'jumlah' => 3,
        ]);

        $response = $this->actingAs($seller)->patch(route('admin.pesanan.update-status', $order), [
            'status' => StatusPesanan::Proses->value,
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertSame(StatusPesanan::Pending, $order->fresh()->status);
        $this->assertSame(2, $product->fresh()->stok);
    }

    public function test_tolak_tidak_mengubah_stok(): void
    {
        $seller = User::factory()->create();
        $product = Product::factory()->create(['stok' => 7]);
        $order = Order::factory()->create(['status' => StatusPesanan::Pending]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'jumlah' => 3,
        ]);

        $this->actingAs($seller)->patch(route('admin.pesanan.update-status', $order), [
            'status' => StatusPesanan::Ditolak->value,
        ]);

        $this->assertSame(StatusPesanan::Ditolak, $order->fresh()->status);
        $this->assertSame(7, $product->fresh()->stok);
    }

    public function test_batal_dari_status_ditolak_tidak_mengembalikan_stok_lagi(): void
    {
        $seller = User::factory()->create();
        $product = Product::factory()->create(['stok' => 7]);
        $order = Order::factory()->create(['status' => StatusPesanan::Ditolak]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'jumlah' => 3,
        ]);

        $this->actingAs($seller)->patch(route('admin.pesanan.update-status', $order), [
            'status' => StatusPesanan::Batal->value,
        ]);

        $this->assertSame(7, $product->fresh()->stok);
        $this->assertSame(StatusPesanan::Batal, $order->fresh()->status);
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
