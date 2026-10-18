<?php

namespace Tests\Feature;

use App\Models\CakeAddon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAddonTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_addons(): void
    {
        $seller = User::factory()->create();
        CakeAddon::factory()->create(['nama_addon' => 'Cokelat Belgium', 'harga_tambahan' => 20_000]);

        $response = $this->actingAs($seller)->get(route('admin.pengaturan.index'));

        $response->assertOk();
        $response->assertSee('Kelola Add-ons');
        $response->assertSee('Cokelat Belgium');
        $response->assertSee('Tambah Add-on');
    }

    public function test_store_creates_an_addon(): void
    {
        $seller = User::factory()->create();

        $response = $this->actingAs($seller)->post(route('admin.pengaturan.addon.store'), [
            'nama_addon' => 'Tepung Gluten-Free',
            'harga_tambahan' => 30_000,
        ]);

        $response->assertRedirect(route('admin.pengaturan.index'));

        $addon = CakeAddon::firstWhere('nama_addon', 'Tepung Gluten-Free');

        $this->assertNotNull($addon);
        $this->assertSame(30_000, $addon->harga_tambahan);
    }

    public function test_store_validates_required_fields(): void
    {
        $seller = User::factory()->create();

        $response = $this->actingAs($seller)->post(route('admin.pengaturan.addon.store'), []);

        $response->assertSessionHasErrors(['nama_addon', 'harga_tambahan']);
    }

    public function test_update_changes_an_addon(): void
    {
        $seller = User::factory()->create();
        $addon = CakeAddon::factory()->create(['nama_addon' => 'Lama', 'harga_tambahan' => 10_000]);

        $response = $this->actingAs($seller)->put(route('admin.pengaturan.addon.update', $addon), [
            'nama_addon' => 'Baru',
            'harga_tambahan' => 25_000,
        ]);

        $response->assertRedirect(route('admin.pengaturan.index'));

        $addon->refresh();
        $this->assertSame('Baru', $addon->nama_addon);
        $this->assertSame(25_000, $addon->harga_tambahan);
    }

    public function test_destroy_deletes_an_addon(): void
    {
        $seller = User::factory()->create();
        $addon = CakeAddon::factory()->create();

        $response = $this->actingAs($seller)->delete(route('admin.pengaturan.addon.destroy', $addon));

        $response->assertRedirect(route('admin.pengaturan.index'));
        $this->assertDatabaseMissing('cake_addons', ['id' => $addon->id]);
    }

    public function test_guest_cannot_manage_addons(): void
    {
        $this->get(route('admin.pengaturan.addon.create'))->assertRedirect(route('login'));
    }
}
