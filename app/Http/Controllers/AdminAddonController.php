<?php

namespace App\Http\Controllers;

use App\Models\CakeAddon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAddonController extends Controller
{
    /**
     * Form tambah add-on baru.
     */
    public function create(): View
    {
        return view('admin.addon.create');
    }

    /**
     * Simpan add-on baru.
     */
    public function store(Request $request): RedirectResponse
    {
        CakeAddon::create($request->validate($this->rules()));

        return redirect()
            ->route('admin.pengaturan.index')
            ->with('success', 'Add-on berhasil ditambahkan.');
    }

    /**
     * Form edit add-on.
     */
    public function edit(CakeAddon $cakeAddon): View
    {
        return view('admin.addon.edit', ['cakeAddon' => $cakeAddon]);
    }

    /**
     * Perbarui add-on.
     */
    public function update(Request $request, CakeAddon $cakeAddon): RedirectResponse
    {
        $cakeAddon->update($request->validate($this->rules()));

        return redirect()
            ->route('admin.pengaturan.index')
            ->with('success', 'Add-on berhasil diperbarui.');
    }

    /**
     * Hapus add-on.
     */
    public function destroy(CakeAddon $cakeAddon): RedirectResponse
    {
        $cakeAddon->delete();

        return redirect()
            ->route('admin.pengaturan.index')
            ->with('success', 'Add-on berhasil dihapus.');
    }

    /**
     * Aturan validasi yang dipakai store() dan update().
     *
     * @return array<string, array<int, mixed>>
     */
    private function rules(): array
    {
        return [
            'nama_addon' => ['required', 'string', 'max:255'],
            'harga_tambahan' => ['required', 'integer', 'min:0'],
        ];
    }
}
