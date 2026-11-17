<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\StoresBase64Images;
use App\Models\ActivityLog;
use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminStoreSettingController extends Controller
{
    use StoresBase64Images;

    /**
     * Form pengaturan toko: profil, logo, rekening bank, dan gambar QRIS.
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
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'cropped_logo_base64' => ['nullable', 'string'],
            'hapus_logo' => ['nullable', 'boolean'],
            'store_email' => ['nullable', 'string', 'email', 'max:255'],
            'instagram_url' => ['nullable', 'string', 'max:255'],
            'facebook_url' => ['nullable', 'string', 'max:255'],
            'tiktok_url' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_holder' => ['nullable', 'string', 'max:100'],
            'qris_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'hapus_qris' => ['nullable', 'boolean'],
        ]);

        $setting = StoreSetting::current();

        if ($request->hasFile('logo')) {
            $this->deleteLogo($setting);
            $data['logo_path'] = $request->file('logo')->store('logo', 'public');
        } elseif ($request->filled('cropped_logo_base64')) {
            $savedLogo = $this->storeBase64Image((string) $request->input('cropped_logo_base64'), 'logo');

            if ($savedLogo) {
                $this->deleteLogo($setting);
                $data['logo_path'] = $savedLogo;
            }
        } elseif ($request->boolean('hapus_logo')) {
            $this->deleteLogo($setting);
            $data['logo_path'] = null;
        }

        if ($request->hasFile('qris_image')) {
            $this->deleteQrisImage($setting);
            $data['qris_image_path'] = $request->file('qris_image')->store('qris', 'public');
        } elseif ($request->boolean('hapus_qris')) {
            $this->deleteQrisImage($setting);
            $data['qris_image_path'] = null;
        }

        unset($data['logo'], $data['cropped_logo_base64'], $data['hapus_logo'], $data['qris_image'], $data['hapus_qris']);

        $setting->fill($data)->save();

        ActivityLog::record('ubah', 'Memperbarui informasi & pengaturan toko', $setting);

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Pengaturan toko berhasil disimpan.');
    }

    /**
     * Hapus file logo lama dari disk 'public' bila ada.
     */
    private function deleteLogo(StoreSetting $setting): void
    {
        if ($setting->logo_path) {
            Storage::disk('public')->delete($setting->logo_path);
        }
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
