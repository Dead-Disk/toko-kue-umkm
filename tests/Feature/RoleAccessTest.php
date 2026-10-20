<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_defaults_to_admin_role(): void
    {
        $user = User::factory()->create();

        $this->assertSame('admin', $user->role);
        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isStaff());
    }

    public function test_staff_factory_sets_staff_role(): void
    {
        $staff = User::factory()->staff()->create();

        $this->assertTrue($staff->isStaff());
        $this->assertFalse($staff->isAdmin());
    }

    public function test_staff_can_view_orders_and_update_status(): void
    {
        $staff = User::factory()->staff()->create();
        $order = Order::factory()->create();

        $this->actingAs($staff)->get(route('admin.pesanan.index'))->assertOk();

        $this->actingAs($staff)
            ->patch(route('admin.pesanan.update-status', $order), ['status' => 'proses'])
            ->assertRedirect(route('admin.pesanan.index'));
    }

    public function test_staff_can_view_stock_but_not_manage_products(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->get(route('admin.produk.index'))->assertOk();

        $this->actingAs($staff)->get(route('admin.produk.create'))->assertForbidden();
        $this->actingAs($staff)
            ->post(route('admin.produk.store'), ['nama_kue' => 'Brownies'])
            ->assertForbidden();
    }

    public function test_staff_cannot_access_categories_and_settings(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->get(route('admin.pengaturan.index'))->assertForbidden();
        $this->actingAs($staff)->get(route('admin.kategori.create'))->assertForbidden();
    }

    public function test_staff_cannot_delete_orders(): void
    {
        $staff = User::factory()->staff()->create();
        $order = Order::factory()->create();

        $this->actingAs($staff)
            ->delete(route('admin.pesanan.destroy', $order))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.produk.index'))->assertRedirect(route('login'));
    }
}
