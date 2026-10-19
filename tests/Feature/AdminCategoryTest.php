<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengaturan_page_lists_categories(): void
    {
        $seller = User::factory()->create();
        Category::factory()->create(['nama_kategori' => 'Kue Basah']);

        $response = $this->actingAs($seller)->get(route('admin.pengaturan.index'));

        $response->assertOk();
        $response->assertSee('Kelola Kategori Kue');
        $response->assertSee('Kue Basah');
        $response->assertSee('Tambah Kategori');
    }

    public function test_store_creates_a_category_with_generated_slug(): void
    {
        $seller = User::factory()->create();

        $response = $this->actingAs($seller)->post(route('admin.kategori.store'), [
            'nama_kategori' => 'Kue Kering',
        ]);

        $response->assertRedirect(route('admin.pengaturan.index'));

        $category = Category::firstWhere('nama_kategori', 'Kue Kering');

        $this->assertNotNull($category);
        $this->assertSame('kue-kering', $category->slug);
    }

    public function test_store_accepts_the_name_input_as_alias(): void
    {
        $seller = User::factory()->create();

        $this->actingAs($seller)
            ->post(route('admin.kategori.store'), ['name' => 'Tart Ulang Tahun'])
            ->assertRedirect(route('admin.pengaturan.index'));

        $category = Category::firstWhere('nama_kategori', 'Tart Ulang Tahun');

        $this->assertNotNull($category);
        $this->assertSame('Tart Ulang Tahun', $category->name);
    }

    public function test_store_validates_nama_kategori(): void
    {
        $seller = User::factory()->create();

        $this->actingAs($seller)
            ->post(route('admin.kategori.store'), [])
            ->assertSessionHasErrors('nama_kategori');
    }

    public function test_update_changes_the_category_name_and_slug(): void
    {
        $seller = User::factory()->create();
        $category = Category::factory()->create(['nama_kategori' => 'Roti', 'slug' => 'roti']);

        $response = $this->actingAs($seller)->put(route('admin.kategori.update', $category), [
            'nama_kategori' => 'Roti Manis',
        ]);

        $response->assertRedirect(route('admin.pengaturan.index'));

        $category->refresh();
        $this->assertSame('Roti Manis', $category->nama_kategori);
        $this->assertSame('roti-manis', $category->slug);
    }

    public function test_destroy_soft_deletes_an_empty_category(): void
    {
        $seller = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($seller)
            ->delete(route('admin.kategori.destroy', $category))
            ->assertRedirect(route('admin.pengaturan.index'));

        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }

    public function test_destroy_blocks_a_category_that_still_has_products(): void
    {
        $seller = User::factory()->create();
        $category = Category::factory()->create();
        Product::factory()->for($category)->create();

        $response = $this->actingAs($seller)->delete(route('admin.kategori.destroy', $category));

        $response->assertRedirect(route('admin.pengaturan.index'));
        $response->assertSessionHasErrors('kategori');
        $this->assertNotSoftDeleted('categories', ['id' => $category->id]);
    }

    public function test_category_index_redirects_to_the_settings_page(): void
    {
        $seller = User::factory()->create();

        $this->actingAs($seller)
            ->get(route('admin.kategori.index'))
            ->assertRedirect(route('admin.pengaturan.index'));
    }

    public function test_guest_cannot_manage_categories(): void
    {
        $this->get(route('admin.kategori.create'))->assertRedirect(route('login'));
    }
}
