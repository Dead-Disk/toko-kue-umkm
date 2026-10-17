<?php

namespace Tests\Feature;

use App\Enums\MetodePembayaran;
use App\Enums\OpsiPengiriman;
use App\Enums\StatusPesanan;
use App\Enums\TipePesanan;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_order_is_saved_with_uploaded_photo(): void
    {
        Storage::fake('public');

        $response = $this->post(route('customer.custom.store'), [
            'nama_pemesan' => 'Siti',
            'nomor_wa' => '08123456789',
            'alamat_lengkap' => 'Jl. Mawar No. 2',
            'deskripsi_custom' => 'Tulisan Happy Birthday',
            'ukuran_kue' => '20 cm',
            'jumlah' => 2,
            'opsi_pengiriman' => OpsiPengiriman::KurirToko->value,
            'metode_pembayaran' => MetodePembayaran::TransferBank->value,
            'foto_referensi' => UploadedFile::fake()->create('referensi.jpg', 100),
        ]);

        $order = Order::firstWhere('nama_pemesan', 'Siti');

        $this->assertNotNull($order);
        $response->assertRedirect(route('customer.pesanan.sukses', $order->kode_pesanan));

        $this->assertSame(TipePesanan::Custom, $order->tipe_pesanan);
        $this->assertSame(StatusPesanan::Pending, $order->status);
        $this->assertSame(OpsiPengiriman::KurirToko, $order->opsi_pengiriman);
        $this->assertSame(MetodePembayaran::TransferBank, $order->metode_pembayaran);

        $item = $order->items()->first();

        $this->assertNotNull($item);
        $this->assertNull($item->product_id);
        $this->assertSame(2, $item->jumlah);
        $this->assertSame('20 cm', $item->ukuran_kue);
        $this->assertSame('Tulisan Happy Birthday', $item->deskripsi_custom);
        $this->assertNotNull($item->foto_referensi);
        $this->assertStringStartsWith('custom_cakes/', $item->foto_referensi);
        Storage::disk('public')->assertExists($item->foto_referensi);
    }

    public function test_custom_order_is_saved_without_photo(): void
    {
        Storage::fake('public');

        $this->post(route('customer.custom.store'), [
            'nama_pemesan' => 'Siti',
            'nomor_wa' => '08123456789',
            'alamat_lengkap' => 'Jl. Mawar No. 2',
            'deskripsi_custom' => 'Tanpa foto referensi',
            'ukuran_kue' => '18 cm',
            'jumlah' => 1,
            'opsi_pengiriman' => OpsiPengiriman::AmbilSendiri->value,
            'metode_pembayaran' => MetodePembayaran::Qris->value,
        ]);

        $item = Order::firstOrFail()->items()->first();

        $this->assertNotNull($item);
        $this->assertNull($item->foto_referensi);
        $this->assertSame(1, $item->jumlah);
    }

    public function test_custom_order_validates_required_fields(): void
    {
        $response = $this->post(route('customer.custom.store'), []);

        $response->assertSessionHasErrors([
            'nama_pemesan',
            'nomor_wa',
            'alamat_lengkap',
            'deskripsi_custom',
            'ukuran_kue',
            'jumlah',
            'opsi_pengiriman',
            'metode_pembayaran',
        ]);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_custom_order_appears_on_the_admin_order_page(): void
    {
        Storage::fake('public');
        $seller = User::factory()->create();

        $this->post(route('customer.custom.store'), [
            'nama_pemesan' => 'Siti',
            'nomor_wa' => '08123456789',
            'alamat_lengkap' => 'Jl. Mawar No. 2',
            'deskripsi_custom' => 'Tulisan Happy Birthday',
            'ukuran_kue' => '20 cm',
            'jumlah' => 2,
            'opsi_pengiriman' => OpsiPengiriman::KurirToko->value,
            'metode_pembayaran' => MetodePembayaran::TransferBank->value,
        ]);

        $order = Order::firstOrFail();

        $response = $this->actingAs($seller)->get(route('admin.pesanan.index'));

        $response->assertOk();
        $response->assertSee($order->kode_pesanan);
        $response->assertSee('Siti');
    }
}
