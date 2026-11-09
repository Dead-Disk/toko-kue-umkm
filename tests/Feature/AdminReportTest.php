<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_reports(): void
    {
        $this->get(route('admin.reports.index'))->assertRedirect(route('login'));
    }

    public function test_staff_cannot_access_reports(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->get(route('admin.reports.index'))->assertForbidden();
    }

    public function test_admin_can_view_the_sales_report_placeholder_page(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));

        $response->assertOk();
        $response->assertViewIs('admin.reports.index');
        $response->assertSee('<title>Laporan Penjualan - Nak Mbarep</title>', false);
        $response->assertSee('Laporan Penjualan');
        $response->assertSee('Halaman Laporan Penjualan sedang dalam pengembangan.');
    }

    public function test_admin_dashboard_shows_separate_cashier_and_report_links(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee(route('admin.kasir.index'), false);
        $response->assertSee(route('admin.reports.index'), false);
        $response->assertSee('Kasir');
        $response->assertSee('Laporan');
        $response->assertDontSee('Laporan &amp; Kasir', false);
    }
}
