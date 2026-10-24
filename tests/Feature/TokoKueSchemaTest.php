<?php

namespace Tests\Feature;

use App\Enums\MetodePembayaran;
use App\Enums\OpsiPengiriman;
use App\Enums\StatusPesanan;
use App\Enums\StatusStok;
use App\Enums\TipePesanan;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TokoKueSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_schema_has_expected_tables_and_columns(): void
    {
        $this->assertTrue(Schema::hasTable('categories'));
        $this->assertTrue(Schema::hasTable('products'));
        $this->assertTrue(Schema::hasTable('orders'));
        $this->assertTrue(Schema::hasTable('order_items'));
        $this->assertFalse(Schema::hasColumn('users', 'whatsapp_toko'));
        $this->assertTrue(Schema::hasColumn('categories', 'deleted_at'));

        $this->assertTrue(Schema::hasColumns('categories', ['nama_kategori', 'slug']));

        $this->assertTrue(Schema::hasColumns('products', [
            'category_id', 'nama_kue', 'slug', 'deskripsi', 'harga', 'stok', 'max_per_order', 'status_stok', 'foto',
        ]));

        $this->assertTrue(Schema::hasColumns('orders', [
            'kode_pesanan', 'tipe_pesanan', 'nama_pemesan', 'nomor_wa', 'alamat_lengkap',
            'opsi_pengiriman', 'metode_pembayaran', 'total_harga', 'status', 'catatan',
            'bukti_pembayaran',
        ]));

        $this->assertTrue(Schema::hasColumns('order_items', [
            'order_id', 'product_id', 'jumlah', 'subtotal',
            'deskripsi_custom', 'foto_referensi', 'ukuran_kue', 'rasa_kue', 'addons',
        ]));

        $this->assertTrue(Schema::hasColumns('store_settings', [
            'store_name', 'store_description', 'store_address', 'store_phone',
            'bank_name', 'bank_account_number', 'bank_account_holder', 'qris_image_path',
        ]));
    }

    public function test_category_has_many_products(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(2)->for($category)->create();

        $this->assertCount(2, $category->products);
        $this->assertInstanceOf(Product::class, $category->products->first());
    }

    public function test_product_belongs_to_a_category(): void
    {
        $product = Product::factory()->create();

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertSame($product->category_id, $product->category->id);
    }

    public function test_order_has_many_items(): void
    {
        $order = Order::factory()->create();
        OrderItem::factory()->count(3)->for($order)->create();

        $this->assertCount(3, $order->items);
        $this->assertInstanceOf(OrderItem::class, $order->items->first());
    }

    public function test_order_item_belongs_to_order_and_product(): void
    {
        $item = OrderItem::factory()->create();

        $this->assertInstanceOf(Order::class, $item->order);
        $this->assertInstanceOf(Product::class, $item->product);
    }

    public function test_order_and_product_are_related_through_order_items(): void
    {
        $order = Order::factory()->create();
        $product = Product::factory()->create();

        $order->products()->attach($product->id, [
            'jumlah' => 2,
            'subtotal' => $product->harga * 2,
        ]);

        $this->assertTrue($order->products->contains($product));
        $this->assertSame(2, $order->products->first()->pivot->jumlah);
        $this->assertSame(1, $product->fresh()->orders()->count());
    }

    public function test_custom_order_item_has_no_product(): void
    {
        $order = Order::factory()->custom()->create();
        $item = OrderItem::factory()->custom()->for($order)->create();

        $this->assertNull($item->product_id);
        $this->assertNull($item->product);
        $this->assertNotNull($item->deskripsi_custom);
        $this->assertNotNull($item->ukuran_kue);
        $this->assertNotNull($item->rasa_kue);
    }

    public function test_enum_columns_are_cast_to_enum_instances(): void
    {
        $product = Product::factory()->create(['stok' => 5, 'status_stok' => 'ready']);
        $order = Order::factory()->create([
            'tipe_pesanan' => 'custom',
            'status' => 'proses',
        ]);

        $this->assertSame(StatusStok::Ready, $product->status_stok);
        $this->assertSame(TipePesanan::Custom, $order->tipe_pesanan);
        $this->assertSame(StatusPesanan::Proses, $order->status);
        $this->assertContains($order->opsi_pengiriman->value, ['kurir_toko', 'ambil_sendiri']);
        $this->assertContains($order->metode_pembayaran->value, ['transfer_bank', 'qris', 'cash']);
        $this->assertNotNull(MetodePembayaran::tryFrom($order->metode_pembayaran->value));
        $this->assertNotNull(OpsiPengiriman::tryFrom($order->opsi_pengiriman->value));
    }

    public function test_soft_deleting_category_keeps_its_products(): void
    {
        $category = Category::factory()->create();
        Product::factory()->for($category)->create();

        $category->delete();

        $this->assertSoftDeleted('categories', ['id' => $category->id]);
        $this->assertDatabaseCount('products', 1);
    }

    public function test_force_deleting_category_cascades_to_products(): void
    {
        $category = Category::factory()->create();
        Product::factory()->for($category)->create();

        $category->forceDelete();

        $this->assertDatabaseCount('products', 0);
    }

    public function test_deleting_product_sets_order_item_product_to_null(): void
    {
        $item = OrderItem::factory()->create();

        $item->product->forceDelete();

        $this->assertNull($item->fresh()->product_id);
    }

    public function test_store_contact_is_stored_in_store_settings(): void
    {
        $setting = StoreSetting::factory()->create(['store_phone' => '081234567890']);

        $this->assertSame('081234567890', $setting->fresh()->store_phone);
    }

    public function test_kode_pesanan_has_a_unique_index(): void
    {
        $this->assertTrue(Schema::hasIndex('orders', ['kode_pesanan'], 'unique'));
    }

    public function test_order_items_have_expected_foreign_keys_and_nullable_product(): void
    {
        $foreignKeys = collect(Schema::getForeignKeys('order_items'));

        $order = $foreignKeys->first(fn (array $foreignKey): bool => $foreignKey['columns'] === ['order_id']);
        $product = $foreignKeys->first(fn (array $foreignKey): bool => $foreignKey['columns'] === ['product_id']);

        $this->assertNotNull($order);
        $this->assertSame('orders', $order['foreign_table']);
        $this->assertSame('cascade', $order['on_delete']);

        $this->assertNotNull($product);
        $this->assertSame('products', $product['foreign_table']);
        $this->assertSame('set null', $product['on_delete']);

        $columns = collect(Schema::getColumns('order_items'))->keyBy('name');
        $this->assertTrue($columns['product_id']['nullable']);
    }

    public function test_order_item_casts_addons_to_array(): void
    {
        $item = OrderItem::factory()->create(['addons' => ['lilin', 'topper']]);

        $this->assertSame(['lilin', 'topper'], $item->fresh()->addons);
    }
}
