<?php

namespace Tests\Feature;

use App\Enums\StatusPesanan;
use App\Enums\StatusStok;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
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

    public function test_admin_can_view_the_sales_report_page(): void
    {
        $admin = User::factory()->create();

        $category = Category::create([
            'nama_kategori' => 'Kue Basah',
            'slug' => 'kue-basah',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'nama_kue' => 'Lapis Legit',
            'slug' => 'lapis-legit',
            'deskripsi' => 'Lapis legit lezat',
            'harga' => 50000,
            'stok' => 20,
            'status_stok' => StatusStok::Ready,
        ]);

        $order = Order::create([
            'kode_pesanan' => 'ORD-20261119-0001',
            'tipe_pesanan' => 'reguler',
            'nama_pemesan' => 'Budi Santoso',
            'nomor_wa' => '081234567890',
            'alamat_lengkap' => 'Jl. Mawar No. 10',
            'opsi_pengiriman' => 'ambil_sendiri',
            'metode_pembayaran' => 'cash',
            'total_harga' => 100000,
            'status' => StatusPesanan::Selesai,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'jumlah' => 2,
            'subtotal' => 100000,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));

        $response->assertOk();
        $response->assertViewIs('admin.reports.index');
        $response->assertSee('<title>Laporan Penjualan - Nak Mbarep</title>', false);
        $response->assertSee('Laporan Penjualan');
        $response->assertSee('Total Omzet');
        $response->assertSee('Produk Terjual');
        $response->assertSee('Total Sisa Stok');
        $response->assertSee('Rp100.000');
        $response->assertSee('2 pcs');
        $response->assertSee('Lapis Legit');
        $response->assertSee('ORD-20261119-0001');
    }

    public function test_admin_can_filter_report_by_harian_mingguan_and_bulanan(): void
    {
        $admin = User::factory()->create();

        $category = Category::create([
            'nama_kategori' => 'Kue Tart',
            'slug' => 'kue-tart',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'nama_kue' => 'Blackforest Cake',
            'slug' => 'blackforest-cake',
            'deskripsi' => 'Kue tart blackforest',
            'harga' => 150000,
            'stok' => 10,
            'status_stok' => StatusStok::Ready,
        ]);

        // 1. Pesanan Hari Ini
        $todayOrder = Order::create([
            'kode_pesanan' => 'ORD-TODAY-001',
            'tipe_pesanan' => 'reguler',
            'nama_pemesan' => 'Pelanggan Hari Ini',
            'nomor_wa' => '081234567891',
            'alamat_lengkap' => 'Alamat Hari Ini',
            'opsi_pengiriman' => 'ambil_sendiri',
            'metode_pembayaran' => 'cash',
            'total_harga' => 150000,
            'status' => StatusPesanan::Selesai,
            'created_at' => Carbon::now(),
        ]);

        OrderItem::create([
            'order_id' => $todayOrder->id,
            'product_id' => $product->id,
            'jumlah' => 1,
            'subtotal' => 150000,
            'created_at' => Carbon::now(),
        ]);

        // 2. Pesanan 10 Hari Lalu (masih dalam bulan ini jika awal bulan diset, atau diset di awal bulan ini)
        $tenDaysAgo = Carbon::now()->subDays(10);
        // Pastikan tanggalnya masih dalam bulan berjalan agar terhitung di bulanan
        if ($tenDaysAgo->month !== Carbon::now()->month) {
            $tenDaysAgo = Carbon::now()->startOfMonth()->addDays(1);
        }

        $olderOrder = Order::create([
            'kode_pesanan' => 'ORD-OLDER-002',
            'tipe_pesanan' => 'reguler',
            'nama_pemesan' => 'Pelanggan Lama',
            'nomor_wa' => '081234567892',
            'alamat_lengkap' => 'Alamat Lama',
            'opsi_pengiriman' => 'ambil_sendiri',
            'metode_pembayaran' => 'cash',
            'total_harga' => 300000,
            'status' => StatusPesanan::Selesai,
        ]);
        $olderOrder->created_at = $tenDaysAgo;
        $olderOrder->saveQuietly();

        $olderItem = OrderItem::create([
            'order_id' => $olderOrder->id,
            'product_id' => $product->id,
            'jumlah' => 2,
            'subtotal' => 300000,
        ]);
        $olderItem->created_at = $tenDaysAgo;
        $olderItem->saveQuietly();

        // Filter Harian: hanya pesanan hari ini yang muncul
        $harianResponse = $this->actingAs($admin)->get(route('admin.reports.index', ['periode' => 'harian']));
        $harianResponse->assertOk();
        $harianResponse->assertSee('ORD-TODAY-001');
        $harianResponse->assertSee('Rp150.000');
        $harianResponse->assertDontSee('ORD-OLDER-002');

        // Filter Bulanan: pesanan hari ini dan pesanan lama dalam bulan ini muncul
        $bulananResponse = $this->actingAs($admin)->get(route('admin.reports.index', ['periode' => 'bulanan']));
        $bulananResponse->assertOk();
        $bulananResponse->assertSee('ORD-TODAY-001');
        $bulananResponse->assertSee('ORD-OLDER-002');
        $bulananResponse->assertSee('Rp450.000');
    }

    public function test_admin_can_view_stock_report_tab_with_movements(): void
    {
        $admin = User::factory()->create();

        $category = Category::create([
            'nama_kategori' => 'Donat',
            'slug' => 'donat',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'nama_kue' => 'Donat Cokelat Meses',
            'slug' => 'donat-cokelat-meses',
            'deskripsi' => 'Donat empuk dengan meses',
            'harga' => 8000,
            'stok' => 25,
            'status_stok' => StatusStok::Ready,
        ]);

        // Buat log penambahan stok (restok)
        ActivityLog::record(
            'ubah',
            'Memperbarui produk Donat Cokelat Meses: stok 15 menjadi 25.',
            $product,
            [
                'stok_lama' => 15,
                'stok_baru' => 25,
            ]
        );

        // Buat transaksi penjualan (stok keluar)
        $order = Order::create([
            'kode_pesanan' => 'ORD-DONAT-001',
            'tipe_pesanan' => 'reguler',
            'nama_pemesan' => 'Siti Aminah',
            'nomor_wa' => '081234567893',
            'alamat_lengkap' => 'Jl. Kenanga No. 5',
            'opsi_pengiriman' => 'ambil_sendiri',
            'metode_pembayaran' => 'cash',
            'total_harga' => 40000,
            'status' => StatusPesanan::Selesai,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'jumlah' => 5,
            'subtotal' => 40000,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.index', ['tab' => 'stok']));

        $response->assertOk();
        $response->assertSee('Laporan Pergerakan Stok');
        $response->assertSee('Ringkasan Stok Produk');
        $response->assertSee('Riwayat Log Pergerakan Stok');
        $response->assertSee('Donat Cokelat Meses');
        $response->assertSee('25 pcs');
        $response->assertSee('-5 pcs');
        $response->assertSee('+10 pcs');
        $response->assertSee('Masuk (Restok)');
        $response->assertSee('Keluar (Penjualan)');
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

    public function test_admin_can_filter_report_by_custom_date_range(): void
    {
        $admin = User::factory()->create();

        $category = Category::create([
            'nama_kategori' => 'Roti',
            'slug' => 'roti',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'nama_kue' => 'Roti Sobek Cokelat',
            'slug' => 'roti-sobek-cokelat',
            'deskripsi' => 'Roti sobek isi cokelat',
            'harga' => 20000,
            'stok' => 15,
            'status_stok' => StatusStok::Ready,
        ]);

        $targetDate = Carbon::create(2026, 5, 15, 10, 0, 0);

        $order = Order::create([
            'kode_pesanan' => 'ORD-CUSTOM-DATE-001',
            'tipe_pesanan' => 'reguler',
            'nama_pemesan' => 'Budi Custom Range',
            'nomor_wa' => '081234567894',
            'alamat_lengkap' => 'Jl. Anggrek No. 3',
            'opsi_pengiriman' => 'ambil_sendiri',
            'metode_pembayaran' => 'cash',
            'total_harga' => 40000,
            'status' => StatusPesanan::Selesai,
        ]);
        $order->created_at = $targetDate;
        $order->saveQuietly();

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'jumlah' => 2,
            'subtotal' => 40000,
        ]);
        $item->created_at = $targetDate;
        $item->saveQuietly();

        $response = $this->actingAs($admin)->get(route('admin.reports.index', [
            'periode' => 'kustom',
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-31',
        ]));

        $response->assertOk();
        $response->assertSee('ORD-CUSTOM-DATE-001');
        $response->assertSee('Rp40.000');
        $response->assertSee('Roti Sobek Cokelat');
    }

    public function test_empty_state_is_rendered_cleanly_when_no_transactions_in_period(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.reports.index', [
            'periode' => 'kustom',
            'start_date' => '2020-01-01',
            'end_date' => '2020-01-31',
        ]));

        $response->assertOk();
        $response->assertSee('Belum ada transaksi penjualan pada periode');
        $response->assertSee('Rp0');
        $response->assertSee('0 pcs');
    }
}
