<?php

namespace App\Http\Controllers;

use App\Enums\StatusStok;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminProductController extends Controller
{
    /**
     * Daftar semua produk kue.
     */
    public function index(Request $request): View|StreamedResponse
    {
        if ($request->get('export') === 'csv') {
            $filename = 'produk-nak-mbarep-'.date('Y-m-d').'.csv';

            return response()->streamDownload(function (): void {
                $handle = fopen('php://output', 'w');
                if ($handle === false) {
                    return;
                }
                fputcsv($handle, ['No', 'Nama Produk', 'Kategori', 'Harga', 'Stok', 'Status Stok']);
                $all = Product::with('category')->get();
                foreach ($all as $index => $p) {
                    fputcsv($handle, [
                        $index + 1,
                        $p->nama_kue,
                        $p->category?->nama_kategori ?? '-',
                        $p->harga,
                        $p->stok,
                        $p->status_stok->value ?? 'ready',
                    ]);
                }
                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
        }

        $query = Product::with('category')->latest();

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('nama_kue', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhere('harga', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($cq) use ($search): void {
                        $cq->where('nama_kategori', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $products = $query->paginate(8)->withQueryString();
        $categories = Category::withCount('products')->get();

        return view('admin.product.index', compact('products', 'categories'));
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

        $data['max_per_order'] = $data['max_per_order'] ?? 5;
        $data['slug'] = $this->uniqueSlug($data['nama_kue']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('products', 'public');
        }

        $product = Product::create($data);

        ActivityLog::record('tambah', 'Menambahkan produk baru: '.$product->nama_kue.' (Stok: '.$product->stok.')', $product);

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

        $data['max_per_order'] = $data['max_per_order'] ?? 5;

        if ($data['nama_kue'] !== $product->nama_kue) {
            $data['slug'] = $this->uniqueSlug($data['nama_kue'], $product->id);
        }

        if ($request->hasFile('foto')) {
            $this->deleteFoto($product);
            $data['foto'] = $request->file('foto')->store('products', 'public');
        }

        $product->update($data);

        ActivityLog::record('ubah', 'Memperbarui produk: '.$product->nama_kue, $product);

        return redirect()
            ->route('admin.produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Soft delete produk. File fotonya baru dihapus saat force delete (lihat model Product).
     */
    public function destroy(Product $product): RedirectResponse
    {
        ActivityLog::record('hapus', 'Menghapus produk: '.$product->nama_kue, $product);

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
            'max_per_order' => ['nullable', 'integer', 'min:1'],
            'status_stok' => ['required', Rule::enum(StatusStok::class)],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * Buat slug unik dari nama kue (mengabaikan produk tertentu saat update).
     * Produk soft-deleted ikut diperiksa karena barisnya masih menempati unique index.
     */
    private function uniqueSlug(string $namaKue, ?int $ignoreId = null): string
    {
        $base = Str::slug($namaKue);
        $slug = $base;
        $counter = 1;

        while (Product::withTrashed()->where('slug', $slug)
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
