<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_image_is_rendered_from_public_storage_without_a_storage_symlink(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/kue.jpg', 'gambar-kue');
        $product = Product::factory()->create(['foto' => 'products/kue.jpg']);

        $response = $this->get($product->foto_url);

        $response->assertOk();
        $response->assertHeader('content-type', 'image/jpeg');
        $response->assertStreamedContent('gambar-kue');
    }

    public function test_product_image_route_does_not_serve_files_outside_the_products_directory(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('private/secret.jpg', 'rahasia');

        $response = $this->get('/product-images/private/secret.jpg');

        $response->assertNotFound();
    }

    public function test_admin_and_staff_product_pages_render_images_without_a_storage_symlink(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/kue.jpg', 'gambar-kue');
        $product = Product::factory()->create([
            'nama_kue' => 'Kue Gambar',
            'foto' => 'products/kue.jpg',
        ]);
        $admin = User::factory()->create();
        $staff = User::factory()->staff()->create();

        $adminResponse = $this->actingAs($admin)->get(route('admin.produk.index'));
        $staffResponse = $this->actingAs($staff)->get(route('staff.produk.index'));

        $imageUrl = route('product-images.show', ['path' => 'products/kue.jpg']);
        $adminResponse->assertOk();
        $adminResponse->assertSee($imageUrl, false);
        $staffResponse->assertOk();
        $staffResponse->assertSee($imageUrl, false);
        $this->assertSame($imageUrl, $product->foto_url);
    }

    public function test_product_without_photo_returns_null_foto_url_and_renders_svg_fallback(): void
    {
        $category = Category::factory()->create([
            'nama_kategori' => 'Donat Spesial',
            'slug' => 'donat',
        ]);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'nama_kue' => 'Donat Cokelat Polos',
            'foto' => null,
        ]);

        $this->assertNull($product->foto_url);
        $this->assertFalse($product->hasRealFoto());

        $customerResponse = $this->get(route('customer.index'));
        $customerResponse->assertOk();
        $customerResponse->assertSee('Donat Cokelat Polos');
        // SVG illustration icon should be rendered instead of dummy PNG
        $customerResponse->assertDontSee('rec-img-1.png');
        $customerResponse->assertDontSee('cat-donat.png');

        $catalogResponse = $this->get(route('customer.produk.show', $product));
        $catalogResponse->assertOk();
        $catalogResponse->assertSee('Donat Cokelat Polos');
    }

    public function test_category_foto_url_returns_category_svg_from_assets_kategori(): void
    {
        $bentoCat = Category::factory()->create([
            'nama_kategori' => 'Bento Cake',
            'slug' => 'bento-cake',
        ]);
        $donatCat = Category::factory()->create([
            'nama_kategori' => 'Donat Manis',
            'slug' => 'donat',
        ]);
        $tartCat = Category::factory()->create([
            'nama_kategori' => 'Kue Tart Spesial',
            'slug' => 'kue-tart',
        ]);
        $unknownCat = Category::factory()->create([
            'nama_kategori' => 'Kategori Lain',
            'slug' => 'kategori-lain-xyz',
        ]);

        $this->assertStringContainsString('assets/kategori/bento', $bentoCat->foto_url);
        $this->assertStringContainsString('assets/kategori/donat.svg', $donatCat->foto_url);
        $this->assertStringContainsString('assets/kategori/kue-tart.svg', $tartCat->foto_url);
        $this->assertStringContainsString('assets/kategori/default.svg', $unknownCat->foto_url);

        $this->assertFileExists(public_path('assets/kategori/bento.svg'));
        $this->assertFileExists(public_path('assets/kategori/donat.svg'));
        $this->assertFileExists(public_path('assets/kategori/kue-tart.svg'));
        $this->assertFileExists(public_path('assets/kategori/default.svg'));

        Product::factory()->create(['category_id' => $bentoCat->id]);
        Product::factory()->create(['category_id' => $donatCat->id]);
        Product::factory()->create(['category_id' => $tartCat->id]);

        $customerResponse = $this->get(route('customer.index'));
        $customerResponse->assertOk();
        $customerResponse->assertSee('assets/kategori/bento', false);
        $customerResponse->assertSee('assets/kategori/donat.svg', false);
        $customerResponse->assertSee('assets/kategori/kue-tart.svg', false);
    }
}
