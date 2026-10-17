<?php

namespace Tests\Feature;

use App\Enums\MetodePembayaran;
use App\Enums\OpsiPengiriman;
use App\Enums\StatusPesanan;
use App\Enums\TipePesanan;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_orders_ready_products_first_then_habis_alphabetically(): void
    {
        Product::factory()->habis()->create(['nama_kue' => 'Brownies Habis']);
        Product::factory()->create(['nama_kue' => 'Zebra Ready']);
        Product::factory()->habis()->create(['nama_kue' => 'Apel Habis']);
        Product::factory()->create(['nama_kue' => 'Apel Ready']);

        $response = $this->get(route('customer.produk.index'));

        $response->assertOk();
        $response->assertSeeInOrder([
            'Apel Ready',
            'Zebra Ready',
            'Apel Habis',
            'Brownies Habis',
        ]);
    }

    public function test_catalog_disables_order_button_for_out_of_stock_products(): void
    {
        Product::factory()->create(['nama_kue' => 'Kue Ready']);
        $habis = Product::factory()->habis()->create(['nama_kue' => 'Kue Habis']);

        $response = $this->get(route('customer.produk.index'));

        $response->assertOk();
        $response->assertSee('Pesan (Habis)');
        $response->assertSee('disabled', false);
        $response->assertDontSee(route('customer.produk.show', $habis), false);
    }

    public function test_show_displays_product_detail_and_order_form(): void
    {
        $product = Product::factory()->create([
            'nama_kue' => 'Tart Cokelat',
            'harga' => 75_000,
        ]);

        $response = $this->get(route('customer.produk.show', $product));

        $response->assertOk();
        $response->assertSee('Tart Cokelat');
        $response->assertSee('Rp75.000');
        $response->assertSee('Form Pesanan');
        $response->assertSee('name="nama_pemesan"', false);
        $response->assertSee('name="alamat_lengkap"', false);
        $response->assertSee('name="nomor_wa"', false);
        $response->assertSee('name="jumlah"', false);
        $response->assertSee('name="opsi_pengiriman"', false);
        $response->assertSee('name="metode_pembayaran"', false);
    }

    public function test_checkout_saves_order_and_item_then_redirects_to_success_page(): void
    {
        $product = Product::factory()->create([
            'nama_kue' => 'Tart Cokelat',
            'harga' => 75_000,
        ]);

        $response = $this->post(route('customer.pesanan.checkout'), [
            'product_id' => $product->id,
            'nama_pemesan' => 'Budi',
            'alamat_lengkap' => 'Jl. Merdeka No. 1',
            'nomor_wa' => '08123456789',
            'jumlah' => 3,
            'opsi_pengiriman' => OpsiPengiriman::KurirToko->value,
            'metode_pembayaran' => MetodePembayaran::TransferBank->value,
        ]);

        $order = Order::firstWhere('nama_pemesan', 'Budi');

        $this->assertNotNull($order);
        $response->assertRedirect(route('customer.pesanan.sukses', $order->kode_pesanan));

        $this->assertSame(TipePesanan::Reguler, $order->tipe_pesanan);
        $this->assertSame(StatusPesanan::Pending, $order->status);
        $this->assertSame(OpsiPengiriman::KurirToko, $order->opsi_pengiriman);
        $this->assertSame(MetodePembayaran::TransferBank, $order->metode_pembayaran);
        $this->assertSame('Jl. Merdeka No. 1', $order->alamat_lengkap);
        $this->assertSame(225_000, $order->total_harga);

        $item = $order->items()->first();

        $this->assertNotNull($item);
        $this->assertSame($product->id, $item->product_id);
        $this->assertSame(3, $item->jumlah);
        $this->assertSame(225_000, $item->subtotal);
    }

    public function test_checkout_generates_order_code_with_expected_format(): void
    {
        $product = Product::factory()->create();

        $this->post(route('customer.pesanan.checkout'), [
            'product_id' => $product->id,
            'nama_pemesan' => 'Budi',
            'alamat_lengkap' => 'Jl. Merdeka No. 1',
            'nomor_wa' => '08123456789',
            'jumlah' => 1,
            'opsi_pengiriman' => OpsiPengiriman::AmbilSendiri->value,
            'metode_pembayaran' => MetodePembayaran::Qris->value,
        ]);

        $order = Order::firstOrFail();

        $this->assertMatchesRegularExpression('/^ORD-\d{8}-\d{4}$/', $order->kode_pesanan);
    }

    public function test_checkout_rejects_products_that_are_out_of_stock(): void
    {
        $product = Product::factory()->habis()->create();

        $response = $this->from(route('customer.produk.show', $product))->post(route('customer.pesanan.checkout'), [
            'product_id' => $product->id,
            'nama_pemesan' => 'Budi',
            'alamat_lengkap' => 'Jl. Merdeka No. 1',
            'nomor_wa' => '08123456789',
            'jumlah' => 1,
            'opsi_pengiriman' => OpsiPengiriman::KurirToko->value,
            'metode_pembayaran' => MetodePembayaran::TransferBank->value,
        ]);

        $response->assertRedirect(route('customer.produk.show', $product));
        $response->assertSessionHasErrors('product_id');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_checkout_validates_required_fields(): void
    {
        $response = $this->post(route('customer.pesanan.checkout'), []);

        $response->assertSessionHasErrors([
            'product_id',
            'nama_pemesan',
            'alamat_lengkap',
            'nomor_wa',
            'jumlah',
            'opsi_pengiriman',
            'metode_pembayaran',
        ]);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_success_page_shows_order_summary(): void
    {
        $order = Order::factory()->create([
            'kode_pesanan' => 'ORD-20261014-0001',
            'nama_pemesan' => 'Siti',
            'total_harga' => 150_000,
        ]);
        $product = Product::factory()->create(['nama_kue' => 'Bolu Pandan']);
        $order->items()->create([
            'product_id' => $product->id,
            'jumlah' => 2,
            'subtotal' => 150_000,
        ]);

        $response = $this->get(route('customer.pesanan.sukses', $order->kode_pesanan));

        $response->assertOk();
        $response->assertSee('ORD-20261014-0001');
        $response->assertSee('menunggu konfirmasi penjual');
        $response->assertSee('Bolu Pandan');
        $response->assertSee('Rp150.000');
    }

    public function test_success_page_returns_not_found_for_unknown_order_code(): void
    {
        $this->get(route('customer.pesanan.sukses', 'ORD-20261014-9999'))->assertNotFound();
    }

    public function test_new_order_appears_on_the_admin_order_page(): void
    {
        $seller = User::factory()->create();
        $product = Product::factory()->create(['harga' => 50_000]);

        $this->post(route('customer.pesanan.checkout'), [
            'product_id' => $product->id,
            'nama_pemesan' => 'Budi',
            'alamat_lengkap' => 'Jl. Merdeka No. 1',
            'nomor_wa' => '08123456789',
            'jumlah' => 2,
            'opsi_pengiriman' => OpsiPengiriman::KurirToko->value,
            'metode_pembayaran' => MetodePembayaran::TransferBank->value,
        ]);

        $order = Order::firstOrFail();

        $response = $this->actingAs($seller)->get(route('admin.pesanan.index'));

        $response->assertOk();
        $response->assertSee($order->kode_pesanan);
        $response->assertSee('Budi');
    }
}
