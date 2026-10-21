<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminStoreSettingController extends Controller
{
    /**
     * Form pengaturan toko: profil, rekening bank, dan gambar QRIS.
     */
    public function index(): View
    {
        return view('admin.settings.index', [
            'setting' => StoreSetting::current(),
        ]);
    }

    /**
     * Simpan pengaturan toko (single-row).
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'store_description' => ['nullable', 'string', 'max:1000'],
            'store_address' => ['nullable', 'string', 'max:255'],
            'store_phone' => ['nullable', 'string', 'max:30'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_holder' => ['nullable', 'string', 'max:100'],
            'qris_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'hapus_qris' => ['nullable', 'boolean'],
        ]);

        $setting = StoreSetting::current();

        if ($request->hasFile('qris_image')) {
            $this->deleteQrisImage($setting);
            $data['qris_image_path'] = $request->file('qris_image')->store('qris', 'public');
        } elseif ($request->boolean('hapus_qris')) {
            $this->deleteQrisImage($setting);
            $data['qris_image_path'] = null;
        }

        unset($data['qris_image'], $data['hapus_qris']);

        $setting->fill($data)->save();

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Pengaturan toko berhasil disimpan.');
    }

    /**
     * Hapus file QRIS lama dari disk 'public' bila ada.
     */
    private function deleteQrisImage(StoreSetting $setting): void
    {
        if ($setting->qris_image_path) {
            Storage::disk('public')->delete($setting->qris_image_path);
        }
    }
}
