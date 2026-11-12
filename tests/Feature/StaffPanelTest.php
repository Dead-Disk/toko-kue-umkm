<?php

namespace Tests\Feature;

use App\Enums\StatusPesanan;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_opening_staff_panel(): void
    {
        $this->get(route('staff.dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_cannot_access_staff_panel(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('staff.dashboard'))
            ->assertForbidden();
    }

    public function test_staff_dashboard_uses_staff_layout_and_panel_branding(): void
    {
        $staff = User::factory()->staff()->create([
            'name' => 'Siti Staff',
        ]);

        $response = $this->actingAs($staff)->get(route('staff.dashboard'));

        $response->assertOk();
        $response->assertViewIs('staff.dashboard');
        $response->assertSee('PANEL STAF');
        $response->assertSee('Selamat datang, Siti Staff');
        $response->assertSee(route('staff.pesanan.index'), false);
        $response->assertSee(route('staff.produk.index'), false);
        $response->assertSee(route('staff.kategori.index'), false);
        $response->assertSee(route('staff.kasir.index'), false);
        $response->assertDontSee(route('admin.pesanan.index'), false);
    }

    public function test_staff_sidebar_hides_admin_only_menus(): void
    {
        $staff = User::factory()->staff()->create();

        $response = $this->actingAs($staff)->get(route('staff.dashboard'));

        $response->assertOk();
        $response->assertDontSee('Kelola User');
        $response->assertDontSee('Laporan');
        $response->assertDontSee('Pengaturan');
    }

    public function test_staff_can_open_operational_pages(): void
    {
        $staff = User::factory()->staff()->create();
        $category = Category::factory()->create(['nama_kategori' => 'Kue Basah']);
        Product::factory()->create([
            'category_id' => $category->id,
            'nama_kue' => 'Lapis Legit',
            'stok' => 12,
        ]);
        Order::factory()->create(['nama_pemesan' => 'Budi Santoso']);

        $this->actingAs($staff)
            ->get(route('staff.pesanan.index'))
            ->assertOk()
            ->assertSee('Budi Santoso');

        $this->actingAs($staff)
            ->get(route('staff.produk.index'))
            ->assertOk()
            ->assertSee('Lapis Legit')
            ->assertDontSee('Tambah Produk');

        $this->actingAs($staff)
            ->get(route('staff.kategori.index'))
            ->assertOk()
            ->assertSee('Kue Basah');

        $this->actingAs($staff)
            ->get(route('staff.kasir.index'))
            ->assertOk()
            ->assertSee('Kasir / POS Offline');
    }

    public function test_staff_can_update_order_status_from_staff_panel(): void
    {
        $staff = User::factory()->staff()->create();
        $product = Product::factory()->create(['stok' => 10]);
        $order = Order::factory()->create();
        $order->items()->create([
            'product_id' => $product->id,
            'jumlah' => 2,
            'subtotal' => 20_000,
        ]);

        $response = $this->actingAs($staff)->patch(
            route('staff.pesanan.update-status', $order),
            ['status' => 'proses'],
        );

        $response->assertRedirect(route('staff.pesanan.index'));
        $this->assertSame(StatusPesanan::Proses, $order->refresh()->status);
    }

    public function test_staff_can_view_own_profile_in_staff_panel(): void
    {
        $staff = User::factory()->staff()->create([
            'name' => 'Dewi Staff',
        ]);

        $response = $this->actingAs($staff)->get(route('staff.profile.edit'));

        $response->assertOk();
        $response->assertSee('PANEL STAF');
        $response->assertSee('Profil Akun');
        $response->assertSee('Dewi Staff');
    }

    public function test_staff_login_redirects_to_staff_dashboard(): void
    {
        $staff = User::factory()->staff()->create([
            'username' => 'staf.toko',
            'password' => 'rahasia123',
        ]);

        $response = $this->post(route('login.store'), [
            'username' => 'staf.toko',
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('staff.dashboard'));
        $this->assertAuthenticatedAs($staff);
    }
}
