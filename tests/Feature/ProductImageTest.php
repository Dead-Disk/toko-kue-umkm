<?php

namespace Tests\Feature;

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
}
