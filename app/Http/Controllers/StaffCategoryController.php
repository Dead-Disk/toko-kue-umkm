<?php

namespace App\Http\Controllers;

use App\Enums\StatusStok;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class StaffCategoryController extends Controller
{
    /**
     * Tampilkan daftar kategori beserta ringkasan stok produknya (read-only).
     */
    public function index(): View
    {
        $categories = Category::query()
            ->withCount([
                'products',
                'products as produk_habis_count' => fn (Builder $query) => $query
                    ->where(function (Builder $query): void {
                        $query->where('stok', '<=', 0)
                            ->orWhere('status_stok', StatusStok::Habis->value);
                    }),
            ])
            ->withSum('products as total_stok', 'stok')
            ->orderBy('nama_kategori')
            ->get();

        return view('staff.category.index', [
            'categories' => $categories,
            'totalKategori' => $categories->count(),
            'totalProduk' => $categories->sum('products_count'),
        ]);
    }
}
