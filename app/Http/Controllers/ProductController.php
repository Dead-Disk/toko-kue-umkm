<?php

namespace App\Http\Controllers;

use App\Enums\StatusStok;
use App\Models\Category;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Katalog produk pembeli: produk ready di atas, habis di bawah (A-Z).
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $categoryId = $request->query('category');
        $readyOnly = $request->boolean('ready_only');
        $priceRange = $request->query('price_range', 'all');

        return view('customer.produk', [
            'products' => $this->sortedProducts($search, $categoryId ? (int) $categoryId : null, $readyOnly, $priceRange),
            'categories' => Category::orderBy('nama_kategori')->get(),
            'currentCategory' => $categoryId ? (int) $categoryId : null,
            'search' => $search,
            'readyOnly' => $readyOnly,
            'priceRange' => $priceRange,
        ]);
    }

    /**
     * Detail satu produk beserta form pemesanan reguler.
     * Draf ringkasan (bila ada) dipakai untuk mengisi ulang form saat pembeli menekan
     * "Kembali / Edit Pesanan".
     */
    public function show(Product $product): View
    {
        $draft = session('order_draft', []);

        return view('customer.produk', [
            'products' => $this->sortedProducts(),
            'categories' => Category::orderBy('nama_kategori')->get(),
            'product' => $product,
            'draft' => ($draft['product_id'] ?? null) === $product->id ? $draft : [],
            'storeSetting' => StoreSetting::current(),
            'readyOnly' => false,
            'priceRange' => 'all',
        ]);
    }

    /**
     * Produk dengan status ready diurutkan lebih dulu, lalu A-Z berdasarkan nama.
     *
     * @return Collection<int, Product>
     */
    private function sortedProducts(?string $search = null, ?int $categoryId = null, bool $readyOnly = false, ?string $priceRange = null): Collection
    {
        return Product::query()
            ->with('category')
            ->when($search, function ($query, $search): void {
                $query->where('nama_kue', 'like', "%{$search}%");
            })
            ->when($categoryId, function ($query, $categoryId): void {
                $query->where('category_id', $categoryId);
            })
            ->when($readyOnly, function ($query): void {
                $query->where('status_stok', StatusStok::Ready->value);
            })
            ->when($priceRange === 'under_25k', function ($query): void {
                $query->where('harga', '<', 25000);
            })
            ->when($priceRange === '25k_50k', function ($query): void {
                $query->whereBetween('harga', [25000, 50000]);
            })
            ->when($priceRange === 'above_50k', function ($query): void {
                $query->where('harga', '>', 50000);
            })
            ->orderByRaw('status_stok = ? desc', [StatusStok::Ready->value])
            ->orderBy('nama_kue')
            ->get();
    }
}
