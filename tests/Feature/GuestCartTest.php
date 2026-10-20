<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestCartTest extends TestCase
{
    use RefreshDatabase;

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
        ]);

        $order = Order::firstOrFail();

        $response->assertRedirect(route('customer.pesanan.sukses', $order->kode_pesanan));
        $this->assertSame(200_000, $order->total_harga);
        $this->assertCount(2, $order->items);
        $this->assertEmpty(session('cart', []));
    }
}
