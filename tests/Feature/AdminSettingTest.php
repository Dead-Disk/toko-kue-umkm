<?php

namespace Tests\Feature;

use App\Enums\BentukKue;
use App\Models\CakeOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_cake_options(): void
    {
        $seller = User::factory()->create();
        CakeOption::factory()->create([
            'bentuk' => BentukKue::Lingkaran,
            'ukuran' => '20 cm',
            'harga_dasar' => 150_000,
        ]);

        $response = $this->actingAs($seller)->get(route('admin.pengaturan.index'));

        $response->assertOk();
        $response->assertSee('20 cm');
        $response->assertSee('Lingkaran 20 cm - Rp 150.000');
        $response->assertSee('Tambah Opsi');
    }

    public function test_store_creates_a_cake_option(): void
    {
        $seller = User::factory()->create();

        $response = $this->actingAs($seller)->post(route('admin.pengaturan.store'), [
            'bentuk' => BentukKue::Kotak->value,
            'ukuran' => '24 cm',
            'harga_dasar' => 200_000,
        ]);

        $response->assertRedirect(route('admin.pengaturan.index'));

        $option = CakeOption::firstWhere('ukuran', '24 cm');

        $this->assertNotNull($option);
        $this->assertSame(BentukKue::Kotak, $option->bentuk);
        $this->assertSame(200_000, $option->harga_dasar);
    }

    public function test_store_validates_required_fields(): void
    {
        $seller = User::factory()->create();

        $response = $this->actingAs($seller)->post(route('admin.pengaturan.store'), []);

        $response->assertSessionHasErrors(['bentuk', 'ukuran', 'harga_dasar']);
    }

    public function test_update_changes_a_cake_option(): void
    {
        $seller = User::factory()->create();
        $option = CakeOption::factory()->create([
            'bentuk' => BentukKue::Lingkaran,
            'ukuran' => '16 cm',
            'harga_dasar' => 100_000,
        ]);

        $response = $this->actingAs($seller)->put(route('admin.pengaturan.update', $option), [
            'bentuk' => BentukKue::Lainnya->value,
            'ukuran' => '18 cm',
            'harga_dasar' => 175_000,
        ]);

        $response->assertRedirect(route('admin.pengaturan.index'));

        $option->refresh();
        $this->assertSame(BentukKue::Lainnya, $option->bentuk);
        $this->assertSame('18 cm', $option->ukuran);
        $this->assertSame(175_000, $option->harga_dasar);
    }

    public function test_destroy_deletes_a_cake_option(): void
    {
        $seller = User::factory()->create();
        $option = CakeOption::factory()->create();

        $response = $this->actingAs($seller)->delete(route('admin.pengaturan.destroy', $option));

        $response->assertRedirect(route('admin.pengaturan.index'));
        $this->assertDatabaseMissing('cake_options', ['id' => $option->id]);
    }

    public function test_guest_cannot_access_cake_option_settings(): void
    {
        $this->get(route('admin.pengaturan.index'))->assertRedirect(route('login'));
    }
}
