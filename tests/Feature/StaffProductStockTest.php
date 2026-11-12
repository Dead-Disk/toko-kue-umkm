<?php

namespace Tests\Feature;

use App\Enums\StatusStok;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffProductStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_update_product_stock(): void
    {
        $product = Product::factory()->create(['stok' => 5]);

        $this->patchJson(route('staff.produk.stock.update', $product), ['delta' => 1])
            ->assertUnauthorized();
    }

    public function test_admin_cannot_update_product_stock_from_staff_panel(): void
    {
        $admin = User::factory()->create();
        $product = Product::factory()->create(['stok' => 5]);

        $this->actingAs($admin)
            ->patchJson(route('staff.produk.stock.update', $product), ['delta' => 1])
            ->assertForbidden();

        $this->assertSame(5, $product->refresh()->stok);
    }

    public function test_staff_can_increment_and_decrement_stock_without_reloading_page(): void
    {
        $staff = User::factory()->staff()->create();
        $product = Product::factory()->create([
            'stok' => 10,
            'status_stok' => StatusStok::Ready,
        ]);

        $this->actingAs($staff)
            ->patchJson(route('staff.produk.stock.update', $product), ['delta' => 1])
            ->assertOk()
            ->assertJsonPath('product.stok', 11)
            ->assertJsonPath('product.is_ready', true);

        $this->actingAs($staff)
            ->patchJson(route('staff.produk.stock.update', $product), ['delta' => -3])
            ->assertOk()
            ->assertJsonPath('product.stok', 8)
            ->assertJsonPath('product.is_ready', true);

        $this->assertSame(8, $product->refresh()->stok);
    }

    public function test_decrement_clamps_stock_at_zero_and_marks_product_as_habis(): void
    {
        $staff = User::factory()->staff()->create();
        $product = Product::factory()->create([
            'stok' => 1,
            'status_stok' => StatusStok::Ready,
        ]);

        $this->actingAs($staff)
            ->patchJson(route('staff.produk.stock.update', $product), ['delta' => -1])
            ->assertOk()
            ->assertJsonPath('product.stok', 0)
            ->assertJsonPath('product.status_stok', 'habis')
            ->assertJsonPath('product.is_ready', false);

        $product->refresh();

        $this->assertSame(0, $product->stok);
        $this->assertSame(StatusStok::Habis, $product->status_stok);
    }

    public function test_staff_can_set_absolute_stock_and_toggle_status(): void
    {
        $staff = User::factory()->staff()->create();
        $product = Product::factory()->create([
            'stok' => 5,
            'status_stok' => StatusStok::Habis,
        ]);

        $this->actingAs($staff)
            ->patchJson(route('staff.produk.stock.update', $product), ['stok' => 9])
            ->assertOk()
            ->assertJsonPath('product.stok', 9)
            ->assertJsonPath('product.status_stok', 'habis')
            ->assertJsonPath('product.is_ready', false);

        $this->actingAs($staff)
            ->patchJson(route('staff.produk.stock.update', $product), ['status_stok' => 'ready'])
            ->assertOk()
            ->assertJsonPath('product.status_stok', 'ready')
            ->assertJsonPath('product.is_ready', true);

        $this->actingAs($staff)
            ->patchJson(route('staff.produk.stock.update', $product), ['status_stok' => 'habis'])
            ->assertOk()
            ->assertJsonPath('product.stok', 9)
            ->assertJsonPath('product.status_stok', 'habis')
            ->assertJsonPath('product.is_ready', false);
    }

    public function test_ready_status_is_rejected_when_stock_is_empty(): void
    {
        $staff = User::factory()->staff()->create();
        $product = Product::factory()->habis()->create();

        $this->actingAs($staff)
            ->patchJson(route('staff.produk.stock.update', $product), ['status_stok' => 'ready'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status_stok');

        $this->assertSame(StatusStok::Habis, $product->refresh()->status_stok);
    }

    public function test_stock_update_rejects_invalid_payloads(): void
    {
        $staff = User::factory()->staff()->create();
        $product = Product::factory()->create(['stok' => 5]);

        $this->actingAs($staff)
            ->patchJson(route('staff.produk.stock.update', $product), ['stok' => -1])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('stok');

        $this->actingAs($staff)
            ->patchJson(route('staff.produk.stock.update', $product), ['status_stok' => 'hilang'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status_stok');

        $this->actingAs($staff)
            ->patchJson(route('staff.produk.stock.update', $product), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('stok');

        $this->actingAs($staff)
            ->patchJson(route('staff.produk.stock.update', $product), ['stok' => 3, 'delta' => 1])
            ->assertUnprocessable();
    }

    public function test_stock_update_is_recorded_in_activity_log(): void
    {
        $staff = User::factory()->staff()->create(['name' => 'Siti Staff']);
        $product = Product::factory()->create([
            'nama_kue' => 'Lapis Legit',
            'stok' => 5,
        ]);

        $this->actingAs($staff)
            ->patchJson(route('staff.produk.stock.update', $product), ['delta' => -2])
            ->assertOk();

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $staff->id,
            'user_role' => 'staff',
            'action' => 'ubah',
            'subject_type' => 'Product',
            'subject_id' => (string) $product->id,
        ]);
    }

    public function test_staff_product_page_contains_interactive_stock_controls(): void
    {
        $staff = User::factory()->staff()->create();
        $product = Product::factory()->create(['stok' => 7]);

        $response = $this->actingAs($staff)->get(route('staff.produk.index'));

        $response->assertOk();
        $response->assertSee('data-stock-cell', false);
        $response->assertSee('data-stock-increment', false);
        $response->assertSee('data-stock-decrement', false);
        $response->assertSee('data-stock-input', false);
        $response->assertSee('data-stock-toggle', false);
        $response->assertSee(route('staff.produk.stock.update', $product), false);
    }
}
