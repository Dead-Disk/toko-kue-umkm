<?php

namespace Tests\Feature;

use App\Enums\StatusPesanan;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_access_activity_log_page(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);

        ActivityLog::factory()->create([
            'user_id' => $owner->id,
            'user_name' => $owner->name,
            'user_role' => 'admin',
            'action' => 'login',
            'description' => 'Masuk ke sistem (login)',
        ]);

        $response = $this->actingAs($owner)->get(route('admin.logs.index'));

        $response->assertOk();
        $response->assertSee('Log Aktivitas');
        $response->assertSee('Khusus Owner Toko');
        $response->assertSee('Masuk ke sistem (login)');
    }

    public function test_staff_cannot_access_activity_log_page(): void
    {
        $staff = User::factory()->staff()->create();

        $response = $this->actingAs($staff)->get(route('admin.logs.index'));

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_when_accessing_logs(): void
    {
        $response = $this->get(route('admin.logs.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_activity_log_button_is_visible_for_owner_and_hidden_for_staff(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->staff()->create();

        $ownerResponse = $this->actingAs($owner)->get(route('admin.dashboard'));
        $ownerResponse->assertOk();
        $ownerResponse->assertSee(route('admin.logs.index'), false);

        $staffResponse = $this->actingAs($staff)->get(route('admin.dashboard'));
        $staffResponse->assertOk();
        $staffResponse->assertDontSee(route('admin.logs.index'), false);
    }

    public function test_login_and_logout_creates_activity_log(): void
    {
        $user = User::factory()->create([
            'username' => 'staffnakmbarep',
            'password' => 'secret123',
            'role' => 'staff',
        ]);

        $loginResponse = $this->post(route('login.store'), [
            'username' => 'staffnakmbarep',
            'password' => 'secret123',
        ]);

        $loginResponse->assertRedirect(route('staff.dashboard'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'user_role' => 'staff',
            'action' => 'login',
        ]);

        $logoutResponse = $this->post(route('logout'));
        $logoutResponse->assertRedirect(route('login'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'user_role' => 'staff',
            'action' => 'logout',
        ]);
    }

    public function test_product_crud_creates_activity_logs(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        $this->actingAs($owner)->post(route('admin.produk.store'), [
            'nama_kue' => 'Bolu Gulung Mawar',
            'category_id' => $category->id,
            'harga' => 35000,
            'stok' => 10,
            'status_stok' => 'ready',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $owner->id,
            'action' => 'tambah',
            'subject_type' => 'Product',
        ]);

        $product = Product::firstOrFail();

        $this->actingAs($owner)->put(route('admin.produk.update', $product), [
            'nama_kue' => 'Bolu Gulung Mawar Super',
            'category_id' => $category->id,
            'harga' => 40000,
            'stok' => 12,
            'status_stok' => 'ready',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $owner->id,
            'action' => 'ubah',
            'subject_type' => 'Product',
        ]);

        $this->actingAs($owner)->delete(route('admin.produk.destroy', $product));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $owner->id,
            'action' => 'hapus',
            'subject_type' => 'Product',
        ]);
    }

    public function test_order_status_update_by_staff_creates_activity_log(): void
    {
        $staff = User::factory()->staff()->create();
        $order = Order::factory()->create(['status' => StatusPesanan::Pending]);

        $response = $this->actingAs($staff)->patch(route('admin.pesanan.update-status', $order), [
            'status' => 'proses',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.pesanan.index'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $staff->id,
            'user_role' => 'staff',
            'action' => 'ubah',
            'subject_type' => 'Order',
        ]);
    }

    public function test_activity_log_filters_work(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->staff()->create();

        ActivityLog::factory()->create([
            'user_id' => $owner->id,
            'user_name' => 'Owner Rudi',
            'user_role' => 'admin',
            'action' => 'tambah',
            'description' => 'Menambahkan produk Brownies',
        ]);

        ActivityLog::factory()->create([
            'user_id' => $staff->id,
            'user_name' => 'Staff Ani',
            'user_role' => 'staff',
            'action' => 'kasir',
            'description' => 'Transaksi kasir tunai #ORD-001',
        ]);

        // Filter by role
        $responseRoleStaff = $this->actingAs($owner)->get(route('admin.logs.index', ['role' => 'staff']));
        $responseRoleStaff->assertOk();
        $responseRoleStaff->assertSee('Staff Ani');
        $responseRoleStaff->assertDontSee('Owner Rudi');

        // Filter by action
        $responseActionTambah = $this->actingAs($owner)->get(route('admin.logs.index', ['action' => 'tambah']));
        $responseActionTambah->assertOk();
        $responseActionTambah->assertSee('Owner Rudi');
        $responseActionTambah->assertDontSee('Staff Ani');

        // Filter by search
        $responseSearch = $this->actingAs($owner)->get(route('admin.logs.index', ['search' => 'Brownies']));
        $responseSearch->assertOk();
        $responseSearch->assertSee('Brownies');
        $responseSearch->assertDontSee('ORD-001');
    }
}
