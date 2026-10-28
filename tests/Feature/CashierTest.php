<?php

namespace Tests\Feature;

use App\Enums\MetodePembayaran;
use App\Enums\StatusPesanan;
use App\Enums\StatusStok;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashierTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.kasir.index'))->assertRedirect(route('login'));
        $this->post(route('admin.kasir.store'), [])->assertRedirect(route('login'));
    }

    public function test_admin_and_staff_can_open_the_cashier_page(): void
    {
        $admin = User::factory()->create();
        $staff = User::factory()->staff()->create();
        $product = Product::factory()->create(['nama_kue' => 'Kue Lapis Legit', 'stok' => 7]);

        foreach ([$admin, $staff] as $user) {
            $response = $this->actingAs($user)->get(route('admin.kasir.index'));

            $response->assertOk();
            $response->assertSee('Kasir / POS Offline');
            $response->assertSee($product->nama_kue);
            $response->assertSee('+ Tambah Produk');
            $response->assertSee('Total Belanja');
            $response->assertSee('Proses Pembayaran Tunai');
        }
    }

    public function test_cashier_only_lists_ready_products_with_stock(): void
    {
        $admin = User::factory()->create();
        $ready = Product::factory()->create(['nama_kue' => 'Brownies Ready', 'stok' => 4]);
        $empty = Product::factory()->habis()->create(['nama_kue' => 'Kue Habis']);
        $soldOut = Product::factory()->create(['nama_kue' => 'Kue Status Habis', 'stok' => 3, 'status_stok' => StatusStok::Habis]);

        $response = $this->actingAs($admin)->get(route('admin.kasir.index'));

        $response->assertOk();
        $response->assertSee($ready->nama_kue);
        $response->assertDontSee($empty->nama_kue);
        $response->assertDontSee($soldOut->nama_kue);
    }

    public function test_store_records_multi_item_cash_sale_and_reduces_each_stock(): void
    {
        $admin = User::factory()->create(['name' => 'Kasir Utama']);
        $lapis = Product::factory()->create(['nama_kue' => 'Lapis Legit', 'harga' => 25_000, 'stok' => 10]);
        $brownies = Product::factory()->create(['nama_kue' => 'Brownies', 'harga' => 10_000, 'stok' => 5]);

        $response = $this->actingAs($admin)->post(route('admin.kasir.store'), [
            'items' => [
                ['product_id' => $lapis->id, 'qty' => 3],
                ['product_id' => $brownies->id, 'qty' => 2],
            ],
            'nama_pemesan' => 'Walk-in Customer',
            'uang_diterima' => 100_000,
        ]);

        $response->assertRedirect(route('admin.kasir.index'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('receipt');

        $order = Order::query()->with('items')->sole();

        $this->assertSame(StatusPesanan::Selesai, $order->status);
        $this->assertSame(MetodePembayaran::Cash, $order->metode_pembayaran);
        $this->assertSame(95_000, $order->total_harga);
        $this->assertSame('Walk-in Customer', $order->nama_pemesan);
        $this->assertCount(2, $order->items);

        $this->assertSame(7, $lapis->refresh()->stok);
        $this->assertSame(3, $brownies->refresh()->stok);
        $this->assertSame(StatusStok::Ready, $lapis->status_stok);
        $this->assertSame(StatusStok::Ready, $brownies->status_stok);

        $receipt = session('receipt');

        $this->assertSame($order->id, $receipt['order_id']);
        $this->assertSame(5_000, $receipt['kembalian']);
    }

    public function test_store_combines_duplicate_product_rows_before_deducting_stock(): void
    {
        $admin = User::factory()->create();
        $product = Product::factory()->create(['harga' => 20_000, 'stok' => 10]);

        $this->actingAs($admin)
            ->post(route('admin.kasir.store'), [
                'items' => [
                    ['product_id' => $product->id, 'qty' => 2],
                    ['product_id' => $product->id, 'qty' => 1],
                ],
            ])
            ->assertRedirect(route('admin.kasir.index'));

        $order = Order::query()->with('items')->sole();

        $this->assertSame(60_000, $order->total_harga);
        $this->assertCount(1, $order->items);
        $this->assertSame(3, $order->items->first()->jumlah);
        $this->assertSame(7, $product->refresh()->stok);
    }

    public function test_store_marks_product_habis_when_stock_runs_out(): void
    {
        $admin = User::factory()->create();
        $product = Product::factory()->create(['stok' => 2]);

        $this->actingAs($admin)
            ->post(route('admin.kasir.store'), [
                'items' => [
                    ['product_id' => $product->id, 'qty' => 2],
                ],
            ])
            ->assertRedirect(route('admin.kasir.index'));

        $product->refresh();

        $this->assertSame(0, $product->stok);
        $this->assertSame(StatusStok::Habis, $product->status_stok);
    }

    public function test_store_rejects_a_row_above_stock_without_changing_anything(): void
    {
        $admin = User::factory()->create();
        $cukup = Product::factory()->create(['stok' => 5]);
        $kurang = Product::factory()->create(['stok' => 2]);

        $response = $this->actingAs($admin)
            ->from(route('admin.kasir.index'))
            ->post(route('admin.kasir.store'), [
                'items' => [
                    ['product_id' => $cukup->id, 'qty' => 2],
                    ['product_id' => $kurang->id, 'qty' => 5],
                ],
            ]);

        $response->assertRedirect(route('admin.kasir.index'));
        $response->assertSessionHasErrors('items');

        $this->assertSame(5, $cukup->refresh()->stok);
        $this->assertSame(2, $kurang->refresh()->stok);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_store_rejects_product_marked_as_habis(): void
    {
        $admin = User::factory()->create();
        $product = Product::factory()->create(['stok' => 5, 'status_stok' => StatusStok::Habis]);

        $response = $this->actingAs($admin)
            ->from(route('admin.kasir.index'))
            ->post(route('admin.kasir.store'), [
                'items' => [
                    ['product_id' => $product->id, 'qty' => 1],
                ],
            ]);

        $response->assertSessionHasErrors('items');

        $this->assertSame(5, $product->refresh()->stok);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_store_validates_required_fields(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('admin.kasir.index'))
            ->post(route('admin.kasir.store'), []);

        $response->assertSessionHasErrors('items');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_store_validates_each_item_row(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)
            ->from(route('admin.kasir.index'))
            ->post(route('admin.kasir.store'), [
                'items' => [
                    ['product_id' => '', 'qty' => 0],
                ],
            ]);

        $response->assertSessionHasErrors(['items.0.product_id', 'items.0.qty']);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_store_still_accepts_the_previous_single_item_payload(): void
    {
        $admin = User::factory()->create();
        $product = Product::factory()->create(['harga' => 15_000, 'stok' => 4]);

        $this->actingAs($admin)
            ->post(route('admin.kasir.store'), [
                'product_id' => $product->id,
                'jumlah' => 2,
            ])
            ->assertRedirect(route('admin.kasir.index'));

        $order = Order::query()->with('items')->sole();

        $this->assertSame(30_000, $order->total_harga);
        $this->assertSame(2, $order->items->first()->jumlah);
        $this->assertSame(2, $product->refresh()->stok);
    }
}
