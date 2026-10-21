<?php

namespace Tests\Feature;

use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminStoreSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_the_store_settings_form(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.settings.index'));

        $response->assertOk();
        $response->assertSee('Pengaturan Toko');
        $response->assertSee('Nama Bank');
        $response->assertSee('Nomor Rekening');
        $response->assertSee('Nama Pemilik Rekening');
        $response->assertSee('Gambar QRIS');
        $response->assertSee('enctype="multipart/form-data"', false);
        $response->assertSee('name="qris_image"', false);
    }

    public function test_admin_can_update_store_profile_and_bank_details(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'store_name' => 'Kue Nyonya',
            'store_description' => 'Kue rumahan sejak 2010.',
            'store_address' => 'Jl. Mawar No. 5',
            'store_phone' => '081200001111',
            'bank_name' => 'BCA',
            'bank_account_number' => '9876543210',
            'bank_account_holder' => 'Nyonya Lim',
        ]);

        $response->assertRedirect(route('admin.settings.index'));

        $setting = StoreSetting::firstOrFail();

        $this->assertSame('Kue Nyonya', $setting->store_name);
        $this->assertSame('Kue rumahan sejak 2010.', $setting->store_description);
        $this->assertSame('BCA', $setting->bank_name);
        $this->assertSame('9876543210', $setting->bank_account_number);
        $this->assertSame('Nyonya Lim', $setting->bank_account_holder);
        $this->assertTrue($setting->hasBankDetails());
        $this->assertFalse($setting->hasQrisImage());
    }

    public function test_updating_settings_twice_keeps_a_single_row(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'store_name' => 'Toko Pertama',
            'bank_name' => 'BNI',
        ]);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'store_name' => 'Toko Kedua',
            'bank_name' => 'BRI',
        ]);

        $this->assertDatabaseCount('store_settings', 1);
        $this->assertSame('Toko Kedua', StoreSetting::firstOrFail()->store_name);
    }

    public function test_admin_can_upload_a_qris_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'store_name' => 'Toko Kue',
            'qris_image' => UploadedFile::fake()->create('qris.png', 10, 'image/png'),
        ]);

        $response->assertRedirect(route('admin.settings.index'));

        $setting = StoreSetting::firstOrFail();

        $this->assertNotNull($setting->qris_image_path);
        $this->assertStringStartsWith('qris/', $setting->qris_image_path);
        Storage::disk('public')->assertExists($setting->qris_image_path);
    }

    public function test_admin_can_remove_the_qris_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('qris/lama.png', 'gambar');
        $admin = User::factory()->create();
        StoreSetting::factory()->withQris()->create(['qris_image_path' => 'qris/lama.png']);

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'store_name' => 'Toko Kue',
            'hapus_qris' => '1',
        ]);

        $response->assertRedirect(route('admin.settings.index'));

        $this->assertNull(StoreSetting::firstOrFail()->qris_image_path);
        Storage::disk('public')->assertMissing('qris/lama.png');
    }

    public function test_update_requires_a_store_name(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'bank_name' => 'BNI',
        ]);

        $response->assertSessionHasErrors('store_name');
        $this->assertDatabaseCount('store_settings', 0);
    }

    public function test_staff_cannot_access_store_settings(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->get(route('admin.settings.index'))->assertForbidden();
        $this->actingAs($staff)
            ->put(route('admin.settings.update'), ['store_name' => 'Toko'])
            ->assertForbidden();
    }
}
