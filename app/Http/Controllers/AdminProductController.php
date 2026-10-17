<?php

namespace App\Http\Controllers;

use App\Enums\StatusStok;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminProductController extends Controller
{
    /**
     * Daftar semua produk kue.
     */
    public function index(): View
    {
        $products = Product::with('category')->latest()->get();

        return view('admin.product.index', ['products' => $products]);
    }

    /**
     * Form tambah produk baru.
     */
    public function create(): View
    {
        $categories = Category::all();

        return view('admin.product.create', compact('categories'));
    }

    /**
     * Simpan produk baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        $data['slug'] = $this->uniqueSlug($data['nama_kue']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('products', 'public');
        }

        Product::create($data);

        return redirect()
            ->route('admin.produk.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Form edit produk.
     */
    public function edit(Product $product): View
    {
        $categories = Category::all();

        return view('admin.product.edit', compact('product', 'categories'));
    }

    /**
     * Perbarui produk, termasuk status stok.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate($this->rules());

        if ($data['nama_kue'] !== $product->nama_kue) {
            $data['slug'] = $this->uniqueSlug($data['nama_kue'], $product->id);
        }

        if ($request->hasFile('foto')) {
            $this->deleteFoto($product);
            $data['foto'] = $request->file('foto')->store('products', 'public');
        }

        $product->update($data);

        return redirect()
            ->route('admin.produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Hapus produk beserta fotonya.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteFoto($product);
        $product->delete();

        return redirect()
            ->route('admin.produk.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Aturan validasi yang dipakai store() dan update().
     *
     * @return array<string, array<int, mixed>>
     */
    private function rules(): array
    {
        return [
            'nama_kue' => ['required', 'string', 'max:255'],
            'category_id' => ['required', Rule::exists('categories', 'id')],
            'deskripsi' => ['nullable', 'string'],
            'harga' => ['required', 'integer', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
            'status_stok' => ['required', Rule::enum(StatusStok::class)],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * Buat slug unik dari nama kue (mengabaikan produk tertentu saat update).
     */
    private function uniqueSlug(string $namaKue, ?int $ignoreId = null): string
    {
        $base = Str::slug($namaKue);
        $slug = $base;
        $counter = 1;

        while (Product::where('slug', $slug)
            ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.++$counter;
        }

        return $slug;
    }

    /**
     * Hapus file foto produk dari disk 'public' bila ada.
     */
    private function deleteFoto(Product $product): void
    {
        if ($product->foto) {
            Storage::disk('public')->delete($product->foto);
        }
    }
}
