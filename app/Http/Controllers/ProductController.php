<?php

namespace App\Http\Controllers;

use App\Enums\StatusStok;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Katalog produk pembeli: produk ready di atas, habis di bawah (A-Z).
     */
    public function index(): View
    {
        return view('customer.produk', [
            'products' => $this->sortedProducts(),
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
            'product' => $product,
            'draft' => ($draft['product_id'] ?? null) === $product->id ? $draft : [],
            'storeSetting' => StoreSetting::current(),
        ]);
    }

    /**
     * Produk dengan status ready diurutkan lebih dulu, lalu A-Z berdasarkan nama.
     *
     * @return Collection<int, Product>
     */
    private function sortedProducts(): Collection
    {
        return Product::query()
            ->with('category')
            ->orderByRaw('status_stok = ? desc', [StatusStok::Ready->value])
            ->orderBy('nama_kue')
            ->get();
    }
}
