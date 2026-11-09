<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_access_admin_dashboard_and_see_summary(): void
    {
        $admin = User::factory()->create([
            'name' => 'Budi Santoso',
            'role' => 'admin',
        ]);

        $category = Category::factory()->create();
        Product::factory()->count(3)->create(['category_id' => $category->id]);
        Order::factory()->count(2)->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHasAll([
            'totalProduk',
            'pesananMasuk',
            'pelangganAktif',
            'pendapatanHariIni',
            'bestSellers',
            'pesananTerbaru',
        ]);
        $response->assertSee('Selamat datang, Budi Santoso');
        $response->assertSee('Total Produk');
        $response->assertSee('Pesanan Masuk');
        $response->assertSee('Pelanggan Aktif');
        $response->assertSee('Pendapatan Hari Ini');
        $response->assertSee('Produk Best Seller');
        $response->assertSee('Pesanan Terbaru');
        $response->assertSee('Pintasan Cepat');
    }

    public function test_staff_can_also_access_admin_dashboard(): void
    {
        $staff = User::factory()->staff()->create([
            'name' => 'Siti Staff',
        ]);

        $response = $this->actingAs($staff)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Selamat datang, Siti Staff');
    }
}
