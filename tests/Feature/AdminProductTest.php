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

    public function test_destroy_deletes_the_product(): void
    {
        $seller = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($seller)->delete(route('admin.produk.destroy', $product));

        $response->assertRedirect(route('admin.produk.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
