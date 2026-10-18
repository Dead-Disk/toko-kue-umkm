<?php

namespace App\Http\Controllers;

use App\Enums\BentukKue;
use App\Models\CakeAddon;
use App\Models\CakeOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminSettingController extends Controller
{
    /**
     * Daftar opsi custom cake (bentuk, ukuran, harga).
     */
    public function index(): View
    {
        $cakeOptions = CakeOption::orderBy('bentuk')->orderBy('ukuran')->get();
        $cakeAddons = CakeAddon::orderBy('nama_addon')->get();

        return view('admin.setting.index', compact('cakeOptions', 'cakeAddons'));
    }

    /**
     * Form tambah opsi custom cake baru.
     */
    public function create(): View
    {
        $bentukOptions = BentukKue::cases();

        return view('admin.setting.create', compact('bentukOptions'));
    }

    /**
     * Simpan opsi custom cake baru.
     */
    public function store(Request $request): RedirectResponse
    {
        CakeOption::create($request->validate($this->rules()));

        return redirect()
            ->route('admin.pengaturan.index')
            ->with('success', 'Opsi custom cake berhasil ditambahkan.');
    }

    /**
     * Form edit opsi custom cake.
     */
    public function edit(CakeOption $cakeOption): View
    {
        $bentukOptions = BentukKue::cases();

        return view('admin.setting.edit', compact('cakeOption', 'bentukOptions'));
    }

    /**
     * Perbarui opsi custom cake.
     */
    public function update(Request $request, CakeOption $cakeOption): RedirectResponse
    {
        $cakeOption->update($request->validate($this->rules()));

        return redirect()
            ->route('admin.pengaturan.index')
            ->with('success', 'Opsi custom cake berhasil diperbarui.');
    }

    /**
     * Hapus opsi custom cake.
     */
    public function destroy(CakeOption $cakeOption): RedirectResponse
    {
        $cakeOption->delete();

        return redirect()
            ->route('admin.pengaturan.index')
            ->with('success', 'Opsi custom cake berhasil dihapus.');
    }

    /**
     * Aturan validasi yang dipakai store() dan update().
     *
     * @return array<string, array<int, mixed>>
     */
    private function rules(): array
    {
        return [
            'bentuk' => ['required', Rule::enum(BentukKue::class)],
            'ukuran' => ['required', 'string', 'max:50'],
            'harga_dasar' => ['required', 'integer', 'min:0'],
        ];
    }
}
