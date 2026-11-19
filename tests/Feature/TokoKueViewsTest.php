<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TokoKueViewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_custom_form_renders_for_guests(): void
    {
        $response = $this->get(route('customer.custom.create'));

        $response->assertOk();
        $response->assertSee('Lanjut ke Ringkasan Pesanan');
        $response->assertSee('enctype="multipart/form-data"', false);
        $response->assertSee('name="foto_referensi"', false);
        $response->assertSee('name="jumlah"', false);
        $response->assertSee('name="opsi_pengiriman"', false);
        $response->assertSee('name="alamat_lengkap"', false);
    }

    public function test_admin_product_create_form_renders_for_authenticated_seller(): void
    {
        $seller = User::factory()->create();
        Category::factory()->create(['nama_kategori' => 'Kue Basah']);

        $response = $this->actingAs($seller)->get(route('admin.produk.create'));

        $response->assertOk();
        $response->assertSee('Simpan Produk');
        $response->assertSee('Kue Basah');
        $response->assertSee('enctype="multipart/form-data"', false);
        $response->assertSee('name="foto"', false);
    }

    public function test_admin_routes_are_protected_by_auth_middleware(): void
    {
        $route = Route::getRoutes()->getByName('admin.produk.create');

        $this->assertContains('auth', $route->middleware());
    }

    public function test_forms_submit_to_the_expected_routes(): void
    {
        $this->assertSame(url('/customer/custom'), route('customer.custom.review'));
        $this->assertSame(url('/customer/custom/store'), route('customer.custom.store'));
        $this->assertSame(url('/pesanan/review'), route('customer.pesanan.review'));
        $this->assertSame(url('/pesanan/checkout'), route('customer.pesanan.checkout'));
        $this->assertSame(url('/admin/produk'), route('admin.produk.store'));
    }

    public function test_customer_landing_page_renders_with_navigation_links(): void
    {
        $response = $this->get(route('customer.index'));

        $response->assertOk();
        $response->assertSee('Selamat Datang di Toko Kue');
        $response->assertSee(route('customer.produk.index'), false);
        $response->assertSee(route('customer.page', 'tentang-kami'), false);
    }

    public function test_customer_landing_page_renders_empty_state_when_no_banners_available(): void
    {
        $response = $this->get(route('customer.index'));

        $response->assertOk();
        $response->assertSee('tidak ada banner');
        $response->assertSee('Saat ini belum ada banner yang ditampilkan.');
    }

    public function test_customer_landing_page_renders_active_banners_when_provided(): void
    {
        $view = $this->withViewErrors([])->view('customer.beranda.index', [
            'categories' => collect(),
            'produks' => collect(),
            'topProducts' => collect(),
            'storeSetting' => StoreSetting::current(),
            'banners' => [
                [
                    'title' => 'Promo Diskon Kemerdekaan',
                    'subtitle' => 'Diskon 25% untuk semua bolu',
                    'image' => 'https://example.com/promo.png',
                    'link' => 'https://example.com/promo',
                    'badge' => 'Spesial',
                ],
            ],
        ]);

        $view->assertSee('https://example.com/promo.png', false);
        $view->assertDontSee('tidak ada banner');
    }

    public function test_navbar_renders_the_uploaded_store_logo(): void
    {
        StoreSetting::factory()->withLogo()->create([
            'store_name' => 'Kue Nyonya',
            'logo_path' => 'logo/logo-toko.png',
        ]);

        $response = $this->get(route('customer.index'));

        $response->assertOk();
        $response->assertSee(
            'src="'.asset('storage/logo/logo-toko.png').'" alt="Logo Kue Nyonya"',
            false
        );
    }

    public function test_navbar_falls_back_to_the_default_logo_when_none_is_uploaded(): void
    {
        StoreSetting::factory()->create([
            'store_name' => 'Kue Nyonya',
            'logo_path' => null,
        ]);

        $response = $this->get(route('customer.index'));

        $response->assertOk();
        $response->assertSee(
            'src="'.asset('images/logo-nav.png').'" alt="Logo Kue Nyonya"',
            false
        );
    }

    public function test_catalog_navbar_renders_the_uploaded_logo_without_explicit_controller_data(): void
    {
        StoreSetting::factory()->withLogo()->create([
            'store_name' => 'Kue Nyonya',
            'logo_path' => 'logo/logo-toko.png',
        ]);

        $response = $this->get(route('customer.produk.index'));

        $response->assertOk();
        $response->assertSee(
            'src="'.asset('storage/logo/logo-toko.png').'" alt="Logo Kue Nyonya"',
            false
        );
    }

    public function test_admin_layout_renders_the_uploaded_store_logo(): void
    {
        StoreSetting::factory()->withLogo()->create([
            'store_name' => 'Kue Nyonya',
            'logo_path' => 'logo/logo-toko.png',
        ]);
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee(
            'src="'.asset('storage/logo/logo-toko.png').'" alt="Logo Kue Nyonya"',
            false
        );
    }

    public function test_admin_layout_falls_back_to_the_default_logo_when_none_is_uploaded(): void
    {
        StoreSetting::factory()->create([
            'store_name' => 'Kue Nyonya',
            'logo_path' => null,
        ]);
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee(
            'src="'.asset('images/logo-nav.png').'" alt="Logo Kue Nyonya"',
            false
        );
    }

    public function test_staff_layout_renders_the_uploaded_store_logo(): void
    {
        StoreSetting::factory()->withLogo()->create([
            'store_name' => 'Kue Nyonya',
            'logo_path' => 'logo/logo-toko.png',
        ]);
        $staff = User::factory()->staff()->create();

        $response = $this->actingAs($staff)->get(route('staff.dashboard'));

        $response->assertOk();
        $response->assertSee(
            'src="'.asset('storage/logo/logo-toko.png').'" alt="Logo Kue Nyonya"',
            false
        );
    }

    public function test_store_logo_has_an_onerror_fallback_to_the_default_image(): void
    {
        StoreSetting::factory()->withLogo()->create([
            'store_name' => 'Kue Nyonya',
            'logo_path' => 'logo/logo-toko.png',
        ]);

        $response = $this->get(route('customer.index'));

        $response->assertOk();
        $response->assertSee(
            "this.onerror=null;this.src='".asset('images/logo-nav.png')."';",
            false
        );
    }

    public function test_landing_page_hides_categories_without_active_products(): void
    {
        $kategoriAktif = Category::factory()->create(['nama_kategori' => 'Kategori Aktif']);
        Product::factory()->create(['category_id' => $kategoriAktif->id]);
        Category::factory()->create(['nama_kategori' => 'Kategori Tanpa Produk']);

        $response = $this->get(route('customer.index'));

        $response->assertOk();
        $response->assertSee('Kategori Aktif');
        $response->assertDontSee('Kategori Tanpa Produk');
    }

    public function test_customer_page_shows_placeholder_when_view_does_not_exist(): void
    {
        $response = $this->get(route('customer.page', 'kontak'));

        $response->assertOk();
        $response->assertSee('Maaf, halaman ini belum tersedia.');
        $response->assertSee('Kembali ke Beranda');
    }

    public function test_customer_page_renders_the_view_when_it_exists(): void
    {
        $location = storage_path('framework/testing/views');
        File::ensureDirectoryExists($location.'/customer/pages');
        File::put($location.'/customer/pages/promo.blade.php', 'Halaman promo {{ $slug }}');

        view()->addLocation($location);

        try {
            $response = $this->get(route('customer.page', 'promo'));

            $response->assertOk();
            $response->assertSee('Halaman promo promo');
        } finally {
            File::deleteDirectory($location);
        }
    }

    public function test_customer_page_rejects_slug_outside_allowed_characters(): void
    {
        $this->get('/p/produk_kue')->assertNotFound();
    }

    public function test_landing_page_does_not_render_social_icons_when_links_are_empty(): void
    {
        StoreSetting::factory()->create([
            'instagram_url' => null,
            'facebook_url' => null,
            'tiktok_url' => null,
            'store_phone' => null,
        ]);

        $response = $this->get(route('customer.index'));

        $response->assertOk();
        $response->assertDontSee('aria-label="Instagram"');
        $response->assertDontSee('aria-label="Facebook"');
        $response->assertDontSee('aria-label="TikTok"');
        $response->assertDontSee('aria-label="WhatsApp"');
    }

    public function test_landing_page_renders_only_configured_social_icons(): void
    {
        StoreSetting::factory()->create([
            'instagram_url' => 'https://instagram.com/mybakery',
            'facebook_url' => null,
            'tiktok_url' => null,
            'store_phone' => '081234567890',
        ]);

        $response = $this->get(route('customer.index'));

        $response->assertOk();
        $response->assertSee('aria-label="Instagram"', false);
        $response->assertSee('https://instagram.com/mybakery', false);
        $response->assertSee('aria-label="WhatsApp"', false);
        $response->assertSee('https://wa.me/081234567890', false);
        $response->assertDontSee('aria-label="Facebook"');
        $response->assertDontSee('aria-label="TikTok"');
    }

    public function test_tentang_kami_page_renders_only_configured_social_icons(): void
    {
        StoreSetting::factory()->create([
            'instagram_url' => null,
            'facebook_url' => 'https://facebook.com/mybakery',
            'tiktok_url' => 'https://tiktok.com/@mybakery',
            'store_phone' => null,
        ]);

        $response = $this->get(route('customer.page', 'tentang-kami'));

        $response->assertOk();
        $response->assertDontSee('aria-label="Instagram"');
        $response->assertDontSee('aria-label="WhatsApp"');
        $response->assertSee('aria-label="Facebook"', false);
        $response->assertSee('https://facebook.com/mybakery', false);
        $response->assertSee('aria-label="TikTok"', false);
        $response->assertSee('https://tiktok.com/@mybakery', false);
    }
}
