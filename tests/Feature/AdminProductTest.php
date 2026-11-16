<?php

namespace Tests\Feature;

use App\Enums\StatusStok;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_all_products(): void
    {
        $seller = User::factory()->create();
        Product::factory()->create(['nama_kue' => 'Kue Lapis Legit']);

        $response = $this->actingAs($seller)->get(route('admin.produk.index'));

        $response->assertOk();
        $response->assertSee('Kue Lapis Legit');
        $response->assertSee('Tambah Produk');
    }

    public function test_index_filters_products_by_category(): void
    {
        $seller = User::factory()->create();
        $kategoriTart = Category::factory()->create();
        $kategoriDonat = Category::factory()->create();

        Product::factory()->create(['nama_kue' => 'Tart Cokelat', 'category_id' => $kategoriTart->id]);
        Product::factory()->create(['nama_kue' => 'Donat Gula', 'category_id' => $kategoriDonat->id]);

        $response = $this->actingAs($seller)
            ->get(route('admin.produk.index', ['category_id' => $kategoriTart->id]));

        $response->assertOk();
        $response->assertSee('Tart Cokelat');
        $response->assertDontSee('Donat Gula');
    }

    public function test_create_page_shows_form_with_categories(): void
    {
        $seller = User::factory()->create();
        Category::factory()->create(['nama_kategori' => 'Kue Basah']);

        $response = $this->actingAs($seller)->get(route('admin.produk.create'));

        $response->assertOk();
        $response->assertSee('Simpan Produk');
        $response->assertSee('Kue Basah');
    }

    public function test_store_creates_product_with_uploaded_photo(): void
    {
        Storage::fake('public');
        $seller = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($seller)->post(route('admin.produk.store'), [
            'nama_kue' => 'Brownies Cokelat',
            'category_id' => $category->id,
            'deskripsi' => 'Brownies lembut',
            'harga' => 55_000,
            'stok' => 10,
            'status_stok' => StatusStok::Ready->value,
            // Pakai create() agar tidak butuh ekstensi GD seperti pada fake()->image().
            'foto' => UploadedFile::fake()->create('brownies.jpg', 100),
        ]);

        $response->assertRedirect(route('admin.produk.index'));

        $product = Product::firstWhere('nama_kue', 'Brownies Cokelat');

        $this->assertNotNull($product);
        $this->assertSame('brownies-cokelat', $product->slug);
        $this->assertSame(StatusStok::Ready, $product->status_stok);
        $this->assertSame($category->id, $product->category_id);
        Storage::disk('public')->assertExists($product->foto);
    }

    public function test_store_validates_required_fields(): void
    {
        $seller = User::factory()->create();

        $response = $this->actingAs($seller)->post(route('admin.produk.store'), []);

        $response->assertSessionHasErrors(['nama_kue', 'category_id', 'harga', 'stok', 'status_stok']);
    }

    public function test_store_saves_max_per_order(): void
    {
        $seller = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($seller)->post(route('admin.produk.store'), [
            'nama_kue' => 'Kue Batas',
            'category_id' => $category->id,
            'harga' => 25_000,
            'stok' => 20,
            'max_per_order' => 3,
            'status_stok' => StatusStok::Ready->value,
        ]);

        $this->assertSame(3, Product::firstWhere('nama_kue', 'Kue Batas')->max_per_order);
    }

    public function test_store_defaults_max_per_order_to_five_when_left_empty(): void
    {
        $seller = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($seller)->post(route('admin.produk.store'), [
            'nama_kue' => 'Kue Tanpa Batas',
            'category_id' => $category->id,
            'harga' => 25_000,
            'stok' => 20,
            'max_per_order' => '',
            'status_stok' => StatusStok::Ready->value,
        ]);

        $this->assertSame(5, Product::firstWhere('nama_kue', 'Kue Tanpa Batas')->max_per_order);
    }

    public function test_store_rejects_max_per_order_below_one(): void
    {
        $seller = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($seller)->post(route('admin.produk.store'), [
            'nama_kue' => 'Kue Tidak Valid',
            'category_id' => $category->id,
            'harga' => 25_000,
            'stok' => 20,
            'max_per_order' => 0,
            'status_stok' => StatusStok::Ready->value,
        ]);

        $response->assertSessionHasErrors('max_per_order');
        $this->assertDatabaseMissing('products', ['nama_kue' => 'Kue Tidak Valid']);
    }

    public function test_edit_page_shows_existing_product(): void
    {
        $seller = User::factory()->create();
        $product = Product::factory()->create(['nama_kue' => 'Tart Buah']);

        $response = $this->actingAs($seller)->get(route('admin.produk.edit', $product));

        $response->assertOk();
        $response->assertSee('Tart Buah');
        $response->assertSee('Perbarui Produk');
    }

    public function test_update_changes_product_data_and_stock_status(): void
    {
        $seller = User::factory()->create();
        $product = Product::factory()->create([
            'nama_kue' => 'Bolu Pandan',
            'status_stok' => StatusStok::Ready,
        ]);
        $category = Category::factory()->create();

        $response = $this->actingAs($seller)->put(route('admin.produk.update', $product), [
            'nama_kue' => 'Bolu Pandan Spesial',
            'category_id' => $category->id,
            'deskripsi' => 'Bolu pandan versi baru',
            'harga' => 60_000,
            'stok' => 0,
            'status_stok' => StatusStok::Habis->value,
        ]);

        $response->assertRedirect(route('admin.produk.index'));

        $product->refresh();
        $this->assertSame('Bolu Pandan Spesial', $product->nama_kue);
        $this->assertSame(StatusStok::Habis, $product->status_stok);
        $this->assertSame('bolu-pandan-spesial', $product->slug);
    }

    public function test_update_saves_max_per_order(): void
    {
        $seller = User::factory()->create();
        $product = Product::factory()->create(['max_per_order' => 5]);
        $category = Category::factory()->create();

        $this->actingAs($seller)->put(route('admin.produk.update', $product), [
            'nama_kue' => $product->nama_kue,
            'category_id' => $category->id,
            'harga' => 10_000,
            'stok' => 10,
            'max_per_order' => 8,
            'status_stok' => StatusStok::Ready->value,
        ]);

        $this->assertSame(8, $product->fresh()->max_per_order);
    }

    public function test_update_replaces_the_old_photo(): void
    {
        Storage::fake('public');
        $seller = User::factory()->create();
        $product = Product::factory()->create(['foto' => 'products/lama.jpg']);
        Storage::disk('public')->put('products/lama.jpg', 'foto-lama');
        $category = Category::factory()->create();

        $this->actingAs($seller)->put(route('admin.produk.update', $product), [
            'nama_kue' => $product->nama_kue,
            'category_id' => $category->id,
            'harga' => 10_000,
            'stok' => 1,
            'status_stok' => StatusStok::Ready->value,
            'foto' => UploadedFile::fake()->create('baru.jpg', 100),
        ]);

        Storage::disk('public')->assertMissing('products/lama.jpg');
        Storage::disk('public')->assertExists($product->fresh()->foto);
    }

    public function test_update_retains_existing_photo_when_no_new_photo_is_uploaded(): void
    {
        Storage::fake('public');
        $seller = User::factory()->create();
        $product = Product::factory()->create(['foto' => 'products/kue-asli.jpg']);
        Storage::disk('public')->put('products/kue-asli.jpg', 'foto-asli');
        $category = Category::factory()->create();

        // Mengirim update tanpa file foto baru (seperti ketika admin hanya mengedit harga/stok)
        $this->actingAs($seller)->put(route('admin.produk.update', $product), [
            'nama_kue' => 'Nama Kue Diperbarui',
            'category_id' => $category->id,
            'harga' => 80_000,
            'stok' => 15,
            'status_stok' => StatusStok::Ready->value,
            'foto' => null,
        ]);

        $this->assertSame('products/kue-asli.jpg', $product->fresh()->foto);
        Storage::disk('public')->assertExists('products/kue-asli.jpg');
    }

    public function test_store_creates_product_with_cropped_base64_photo(): void
    {
        Storage::fake('public');
        $seller = User::factory()->create();
        $category = Category::factory()->create();

        // 1x1 transparent png data URL
        $base64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

        $response = $this->actingAs($seller)->post(route('admin.produk.store'), [
            'nama_kue' => 'Kue Cropped',
            'category_id' => $category->id,
            'harga' => 45_000,
            'stok' => 5,
            'status_stok' => StatusStok::Ready->value,
            'cropped_foto_base64' => $base64,
        ]);

        $response->assertRedirect(route('admin.produk.index'));
        $product = Product::firstWhere('nama_kue', 'Kue Cropped');
        $this->assertNotNull($product);
        $this->assertNotNull($product->foto);
        Storage::disk('public')->assertExists($product->foto);
    }

    public function test_update_replaces_old_photo_with_cropped_base64_photo(): void
    {
        Storage::fake('public');
        $seller = User::factory()->create();
        $product = Product::factory()->create(['foto' => 'products/kue-sebelumnya.jpg']);
        Storage::disk('public')->put('products/kue-sebelumnya.jpg', 'data-lama');
        $category = Category::factory()->create();

        $base64 = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=';

        $this->actingAs($seller)->put(route('admin.produk.update', $product), [
            'nama_kue' => $product->nama_kue,
            'category_id' => $category->id,
            'harga' => 50_000,
            'stok' => 5,
            'status_stok' => StatusStok::Ready->value,
            'cropped_foto_base64' => $base64,
        ]);

        Storage::disk('public')->assertMissing('products/kue-sebelumnya.jpg');
        $this->assertNotNull($product->fresh()->foto);
        Storage::disk('public')->assertExists($product->fresh()->foto);
    }

    public function test_destroy_deletes_the_product(): void
    {
        $seller = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($seller)->delete(route('admin.produk.destroy', $product));

        $response->assertRedirect(route('admin.produk.index'));
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_index_can_show_soft_deleted_products_for_a_category(): void
    {
        $seller = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'nama_kue' => 'Kue Dihapus',
            'category_id' => $category->id,
        ]);
        $product->delete();

        $activeResponse = $this->actingAs($seller)
            ->get(route('admin.produk.index', ['category_id' => $category->id]));

        $activeResponse->assertOk();
        $activeResponse->assertDontSee('Kue Dihapus');
        $activeResponse->assertSee('1 produk di kategori ini telah dihapus.');

        $trashedResponse = $this->actingAs($seller)
            ->get(route('admin.produk.index', ['category_id' => $category->id, 'trashed' => 1]));

        $trashedResponse->assertOk();
        $trashedResponse->assertSee('Kue Dihapus');
        $trashedResponse->assertSee('Pulihkan');
    }

    public function test_restore_brings_back_a_soft_deleted_product(): void
    {
        $seller = User::factory()->create();
        $product = Product::factory()->create(['nama_kue' => 'Kue Akan Dipulihkan']);
        $product->delete();

        $response = $this->actingAs($seller)
            ->patch(route('admin.produk.restore', $product));

        $response->assertRedirect(route('admin.produk.index'));
        $this->assertNotSoftDeleted('products', ['id' => $product->id]);
    }
}
