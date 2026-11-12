<?php

namespace App\Http\Controllers;

use App\Enums\StatusPesanan;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;

class AdminDashboardController extends PanelController
{
    /**
     * Tampilkan halaman dashboard/beranda admin.
     */
    public function index(): View
    {
        $totalProduk = Product::count();

        $pesananMasuk = Order::whereIn('status', [StatusPesanan::Pending, StatusPesanan::Proses])->count();
        if ($pesananMasuk === 0) {
            $pesananMasuk = Order::count();
        }

        $pelangganAktif = Order::whereNotNull('nomor_wa')
            ->distinct('nomor_wa')
            ->count('nomor_wa');

        if ($pelangganAktif === 0) {
            $pelangganAktif = User::count();
        }

        $pendapatanHariIni = Order::whereDate('created_at', today())
            ->whereIn('status', [StatusPesanan::Proses, StatusPesanan::Selesai])
            ->sum('total_harga');

        $bestSellers = $this->bestSellerProducts();
        $pesananTerbaru = Order::latest()->take(5)->get();

        return $this->panelView('dashboard', compact(
            'totalProduk',
            'pesananMasuk',
            'pelangganAktif',
            'pendapatanHariIni',
            'bestSellers',
            'pesananTerbaru',
        ));
    }

    /**
     * Ambil 4 produk terlaris atau produk terbaru.
     *
     * @return Collection<int, Product>
     */
    private function bestSellerProducts(): Collection
    {
        $bestSellers = Product::query()
            ->with('category')
            ->withSum('orderItems as total_terjual', 'jumlah')
            ->orderByDesc('total_terjual')
            ->limit(4)
            ->get()
            ->filter(fn (Product $product): bool => ($product->total_terjual ?? 0) > 0)
            ->values();

        if ($bestSellers->isEmpty()) {
            $bestSellers = Product::query()
                ->with('category')
                ->latest()
                ->limit(4)
                ->get();
        }

        return $bestSellers;
    }
}
