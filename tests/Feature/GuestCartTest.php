<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GuestCartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Default: rekening bank sudah dikonfigurasi supaya alur transfer bank bisa diuji.
        StoreSetting::factory()->create();
        Storage::fake('local');
    }

    /**
     * File bukti pembayaran palsu (GD tidak tersedia di lingkungan test).
     */
    private function paymentProof(string $name = 'bukti.png'): UploadedFile
    {
        return UploadedFile::fake()->create($name, 10, 'image/png');
    }

    public function test_guest_can_add_product_to_cart(): void
    {
        $product = Product::factory()->create(['nama_kue' => 'Tart Cokelat', 'harga' => 75_000]);

        $this->post(route('cart.add', $product))->assertRedirect();

        $this->assertSame([
            $product->id => [
                'id' => $product->id,
                'name' => 'Tart Cokelat',
                'price' => 75_000,
                'quantity' => 1,
                'image_url' => null,
                'options' => [],
            ],
        ], session('cart'));
    }

    public function test_adding_same_product_twice_increments_quantity(): void
    {
        $product = Product::factory()->create(['harga' => 50_000]);

        $this->post(route('cart.add', $product));
        $this->post(route('cart.add', $product), ['quantity' => 2]);

        $this->assertSame(3, session('cart.'.$product->id.'.quantity'));
    }

    public function test_guest_can_update_cart_quantity(): void
    {
        $product = Product::factory()->create();

        $this->post(route('cart.add', $product));

        $this->patch(route('cart.update', $product), ['quantity' => 4])->assertRedirect();

        $this->assertSame(4, session('cart.'.$product->id.'.quantity'));
    }

    public function test_guest_can_remove_item_from_cart(): void
    {
        $product = Product::factory()->create();

        $this->post(route('cart.add', $product));

        $this->delete(route('cart.remove', $product))->assertRedirect();

        $this->assertArrayNotHasKey($product->id, session('cart', []));
    }

    public function test_guest_can_clear_the_cart(): void
    {
        $product = Product::factory()->create();

        $this->post(route('cart.add', $product));

        $this->delete(route('cart.clear'))->assertRedirect();

        $this->assertEmpty(session('cart', []));
    }

    public function test_guest_cannot_access_checkout_if_cart_is_empty(): void
    {
        $response = $this->get(route('customer.pesanan.review-cart'));

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('warning', 'Keranjang belanja Anda masih kosong.');
    }

    public function test_cart_note_is_carried_to_review_and_saved_on_the_order(): void
    {
        $product = Product::factory()->create(['nama_kue' => 'Tart Cokelat', 'harga' => 75_000]);

        $this->post(route('cart.add', $product), [
            'quantity' => 1,
            'catatan' => 'Tolong tulis "Selamat Ulang Tahun"',
        ]);

        $this->get(route('cart.index'))->assertOk()->assertSee('Tolong tulis &quot;Selamat Ulang Tahun&quot;', false);

        $this->get(route('customer.pesanan.review-cart'))
            ->assertOk()
            ->assertSee('Tolong tulis &quot;Selamat Ulang Tahun&quot;', false);

        $this->post(route('customer.pesanan.checkout'), [
            'checkout_mode' => 'cart',
            'nama_pemesan' => 'Budi',
            'alamat_lengkap' => 'Jl. Merdeka No. 1',
            'nomor_wa' => '08123456789',
            'opsi_pengiriman' => 'kurir_toko',
            'metode_pembayaran' => 'transfer_bank',
            'catatan' => 'Tolong tulis "Selamat Ulang Tahun", potong 8 bagian',
            'bukti_pembayaran' => $this->paymentProof(),
        ]);

        $order = Order::firstOrFail();

        $this->assertSame('Tolong tulis "Selamat Ulang Tahun", potong 8 bagian', $order->catatan);
        $this->assertNull(session('cart_note'));
    }

    public function test_clearing_the_cart_also_clears_the_cart_note(): void
    {
        $product = Product::factory()->create();

        $this->post(route('cart.add', $product), ['catatan' => 'Lilin angka 18']);
        $this->delete(route('cart.clear'))->assertRedirect();

        $this->assertNull(session('cart_note'));
    }

    public function test_cart_checkout_rejects_transfer_when_bank_details_are_missing(): void
    {
        StoreSetting::current()->update([
            'bank_name' => null,
            'bank_account_number' => null,
            'bank_account_holder' => null,
        ]);

        $product = Product::factory()->create();
        $this->post(route('cart.add', $product));

        $response = $this->post(route('customer.pesanan.checkout'), [
            'checkout_mode' => 'cart',
            'nama_pemesan' => 'Budi',
            'alamat_lengkap' => 'Jl. Merdeka No. 1',
            'nomor_wa' => '08123456789',
            'opsi_pengiriman' => 'kurir_toko',
            'metode_pembayaran' => 'transfer_bank',
            'bukti_pembayaran' => $this->paymentProof(),
        ]);

        $response->assertRedirect(route('customer.pesanan.review-cart'));
        $response->assertSessionHasErrors([
            'metode_pembayaran' => 'Metode pembayaran belum dikonfigurasi oleh penjual.',
        ]);
        $this->assertDatabaseCount('orders', 0);
        $this->assertNotEmpty(session('cart', []));
    }

    public function test_review_cart_page_marks_unconfigured_payment_methods(): void
    {
        StoreSetting::current()->update([
            'bank_name' => null,
            'bank_account_number' => null,
            'bank_account_holder' => null,
            'qris_image_path' => null,
        ]);

        $product = Product::factory()->create();
        $this->post(route('cart.add', $product));

        $response = $this->get(route('customer.pesanan.review-cart'));

        $response->assertOk();
        $response->assertSee('Transfer Bank belum dikonfigurasi oleh penjual.');
        $response->assertSee('QRIS belum dikonfigurasi oleh penjual.');
        $response->assertSee('Tunai / Bayar di Toko');
    }

    public function test_cart_checkout_creates_one_order_with_all_items(): void
    {
        $tart = Product::factory()->create(['nama_kue' => 'Tart Cokelat', 'harga' => 75_000]);
        $bolu = Product::factory()->create(['nama_kue' => 'Bolu Pandan', 'harga' => 50_000]);

        $this->post(route('cart.add', $tart), ['quantity' => 2]);
        $this->post(route('cart.add', $bolu));

        $this->get(route('customer.pesanan.review-cart'))
            ->assertOk()
            ->assertSee('Tart Cokelat')
            ->assertSee('Bolu Pandan');

        $response = $this->post(route('customer.pesanan.checkout'), [
            'checkout_mode' => 'cart',
            'nama_pemesan' => 'Budi',
            'alamat_lengkap' => 'Jl. Merdeka No. 1',
            'nomor_wa' => '08123456789',
            'opsi_pengiriman' => 'kurir_toko',
            'metode_pembayaran' => 'transfer_bank',
            'bukti_pembayaran' => $this->paymentProof(),
        ]);

        $order = Order::firstOrFail();

        $response->assertRedirect(route('customer.pesanan.sukses', $order->kode_pesanan));
        $this->assertSame(200_000, $order->total_harga);
        $this->assertCount(2, $order->items);
        $this->assertEmpty(session('cart', []));
        $this->assertNotNull($order->bukti_pembayaran);
        Storage::disk('local')->assertExists($order->bukti_pembayaran);
    }

    public function test_cart_checkout_requires_payment_proof_for_transfer(): void
    {
        $product = Product::factory()->create();
        $this->post(route('cart.add', $product));

        $response = $this->from(route('customer.pesanan.review-cart'))
            ->post(route('customer.pesanan.checkout'), [
                'checkout_mode' => 'cart',
                'nama_pemesan' => 'Budi',
                'alamat_lengkap' => 'Jl. Merdeka No. 1',
                'nomor_wa' => '08123456789',
                'opsi_pengiriman' => 'kurir_toko',
                'metode_pembayaran' => 'transfer_bank',
            ]);

        $response->assertRedirect(route('customer.pesanan.review-cart'));
        $response->assertSessionHasErrors('bukti_pembayaran');
        $this->assertDatabaseCount('orders', 0);
        $this->assertNotEmpty(session('cart', []));
    }

    public function test_cart_checkout_with_cash_does_not_need_payment_proof(): void
    {
        $product = Product::factory()->create();
        $this->post(route('cart.add', $product));

        $this->post(route('customer.pesanan.checkout'), [
            'checkout_mode' => 'cart',
            'nama_pemesan' => 'Budi',
            'alamat_lengkap' => 'Jl. Merdeka No. 1',
            'nomor_wa' => '08123456789',
            'opsi_pengiriman' => 'kurir_toko',
            'metode_pembayaran' => 'cash',
        ]);

        $order = Order::firstOrFail();

        $this->assertNull($order->bukti_pembayaran);
    }
}
