<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    /**
     * Tampilkan halaman kelola kategori (dialihkan ke halaman pengaturan).
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.pengaturan.index');
    }

    /**
     * Form tambah kategori baru.
     */
    public function create(): View
    {
        return view('admin.category.create');
    }

    /**
     * Simpan kategori baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_kategori' => ['required_without:name', 'nullable', 'string', 'max:255'],
            'name' => ['required_without:nama_kategori', 'nullable', 'string', 'max:255'],
        ]);

        $nama = (string) ($request->input('nama_kategori') ?: $request->input('name'));
        $slug = $this->uniqueSlug($nama);

        Category::create([
            'nama_kategori' => $nama,
            'slug' => $slug,
        ]);

        return redirect()
            ->route('admin.pengaturan.index')
            ->with('success', 'Kategori kue berhasil ditambahkan.');
    }

    /**
     * Form edit kategori.
     */
    public function edit(Category $category): View
    {
        return view('admin.category.edit', compact('category'));
    }

    /**
     * Perbarui kategori.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $request->validate([
            'nama_kategori' => ['required_without:name', 'nullable', 'string', 'max:255'],
            'name' => ['required_without:nama_kategori', 'nullable', 'string', 'max:255'],
        ]);

        $nama = (string) ($request->input('nama_kategori') ?: $request->input('name'));

        if ($nama !== $category->nama_kategori) {
            $category->slug = $this->uniqueSlug($nama, $category->id);
            $category->nama_kategori = $nama;
        }

        $category->save();

        return redirect()
            ->route('admin.pengaturan.index')
            ->with('success', 'Kategori kue berhasil diperbarui.');
    }

    /**
     * Hapus kategori (soft delete). Ditolak bila masih ada produk yang memakainya.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return redirect()
                ->route('admin.pengaturan.index')
                ->withErrors(['kategori' => 'Kategori "'.$category->nama_kategori.'" masih dipakai produk. Pindahkan produknya dulu sebelum menghapus.']);
        }

        $category->delete();

        return redirect()
            ->route('admin.pengaturan.index')
            ->with('success', 'Kategori kue berhasil dihapus.');
    }

    /**
     * Buat slug unik untuk kategori.
     */
    private function uniqueSlug(string $nama, ?int $ignoreId = null): string
    {
        $base = Str::slug($nama);
        $slug = $base;
        $counter = 1;

        while (Category::withTrashed()->where('slug', $slug)
            ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.++$counter;
        }

        return $slug;
    }
}
