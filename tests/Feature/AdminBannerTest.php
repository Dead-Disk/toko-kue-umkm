<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminBannerTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_access_banner_settings_page(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($owner)->get(route('admin.banners.index'));

        $response->assertOk();
        $response->assertSee('Pengaturan Banner Promo');
        $response->assertSee('Khusus Owner Toko');
    }

    public function test_user_with_explicit_owner_role_can_access_banner_settings(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->get(route('admin.banners.index'));

        $response->assertOk();
        $response->assertSee('Pengaturan Banner Promo');
    }

    public function test_staff_cannot_access_banner_settings_page(): void
    {
        $staff = User::factory()->staff()->create();

        $response = $this->actingAs($staff)->get(route('admin.banners.index'));

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_when_accessing_banner_settings(): void
    {
        $response = $this->get(route('admin.banners.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_owner_can_update_banner_settings_scale_and_size_and_duration(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($owner)->put(route('admin.banners.settings'), [
            'banner_scale' => 'contain',
            'banner_size' => '16:9',
            'banner_slide_duration' => 5,
            'banner_auto_slide' => 1,
        ]);

        $response->assertRedirect(route('admin.banners.index'));
        $response->assertSessionHas('success');

        $setting = StoreSetting::current();
        $this->assertSame('contain', $setting->banner_scale);
        $this->assertSame('16:9', $setting->banner_size);
        $this->assertSame(5, $setting->banner_slide_duration);
        $this->assertTrue($setting->banner_auto_slide);
    }

    public function test_owner_can_create_new_banner_with_uploaded_image(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create(['role' => 'admin']);

        $file = UploadedFile::fake()->create('promo-ramadhan.png', 100, 'image/png');

        $response = $this->actingAs($owner)->post(route('admin.banners.store'), [
            'judul' => 'Promo Ramadhan Spesial',
            'subjudul' => 'Diskon 30% untuk semua cake',
            'link' => 'https://example.com/promo',
            'badge' => 'Diskon 30%',
            'scale' => 'cover',
            'urutan' => 1,
            'is_active' => 1,
            'gambar' => $file,
        ]);

        $response->assertRedirect(route('admin.banners.index'));
        $this->assertDatabaseHas('banners', [
            'judul' => 'Promo Ramadhan Spesial',
            'subjudul' => 'Diskon 30% untuk semua cake',
            'link' => 'https://example.com/promo',
            'badge' => 'Diskon 30%',
            'scale' => 'cover',
            'urutan' => 1,
            'is_active' => true,
        ]);

        $banner = Banner::firstOrFail();
        Storage::disk('public')->assertExists($banner->gambar);
    }

    public function test_owner_can_update_existing_banner(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create(['role' => 'admin']);
        $banner = Banner::factory()->create([
            'judul' => 'Judul Lama',
            'subjudul' => 'Deskripsi Lama',
            'urutan' => 0,
            'is_active' => true,
        ]);

        $response = $this->actingAs($owner)->put(route('admin.banners.update', $banner), [
            'judul' => 'Judul Baru Diperbarui',
            'subjudul' => 'Deskripsi Baru',
            'urutan' => 2,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.banners.index'));
        $this->assertDatabaseHas('banners', [
            'id' => $banner->id,
            'judul' => 'Judul Baru Diperbarui',
            'subjudul' => 'Deskripsi Baru',
            'urutan' => 2,
        ]);
    }

    public function test_owner_can_toggle_banner_active_status(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);
        $banner = Banner::factory()->create(['is_active' => true]);

        $response = $this->actingAs($owner)->patch(route('admin.banners.toggle', $banner));

        $response->assertRedirect(route('admin.banners.index'));
        $this->assertFalse($banner->fresh()->is_active);

        $this->actingAs($owner)->patch(route('admin.banners.toggle', $banner));
        $this->assertTrue($banner->fresh()->is_active);
    }

    public function test_owner_can_delete_banner(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create(['role' => 'admin']);
        $file = UploadedFile::fake()->create('banner-to-delete.png', 100, 'image/png');
        $path = $file->store('banners', 'public');

        $banner = Banner::factory()->create(['gambar' => $path]);

        $response = $this->actingAs($owner)->delete(route('admin.banners.destroy', $banner));

        $response->assertRedirect(route('admin.banners.index'));
        $this->assertDatabaseMissing('banners', ['id' => $banner->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_customer_beranda_renders_banner_carousel_with_configured_size_and_duration(): void
    {
        $setting = StoreSetting::current();
        $setting->banner_size = '16:9';
        $setting->banner_scale = 'cover';
        $setting->banner_slide_duration = 5;
        $setting->banner_auto_slide = true;
        $setting->save();

        $banner1 = Banner::factory()->create([
            'judul' => 'Slide Pertama 5 Detik',
            'urutan' => 1,
            'is_active' => true,
        ]);
        $banner2 = Banner::factory()->create([
            'judul' => 'Slide Kedua 5 Detik',
            'urutan' => 2,
            'is_active' => true,
        ]);

        $response = $this->get(route('customer.index'));

        $response->assertOk();
        $response->assertSee($banner1->gambar_url, false);
        $response->assertSee($banner2->gambar_url, false);
        $response->assertSee('aspect-[16/9]');
        $response->assertSee('data-interval="5000"', false);
        $response->assertDontSee('tidak ada banner');
    }

    public function test_inactive_banners_are_not_rendered_on_customer_beranda(): void
    {
        Banner::factory()->inactive()->create([
            'judul' => 'Banner Rahasia Nonaktif',
        ]);

        $response = $this->get(route('customer.index'));

        $response->assertOk();
        $response->assertDontSee('Banner Rahasia Nonaktif');
        $response->assertSee('tidak ada banner');
    }
}
