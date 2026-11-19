<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\StoresBase64Images;
use App\Models\ActivityLog;
use App\Models\Banner;
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
            'banners' => Banner::query()->orderBy('urutan')->get(),
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
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'cropped_banner_base64' => ['nullable', 'string'],
            'hapus_banner' => ['nullable', 'boolean'],
            'banner_scale' => ['nullable', 'string', 'in:cover,contain,fill'],
            'banner_size' => ['nullable', 'string', 'in:21:9,16:9,16:7,4:1'],
            'banner_slide_duration' => ['nullable', 'integer', 'min:1', 'max:60'],
            'banner_auto_slide' => ['nullable', 'boolean'],
            'banner_orders' => ['nullable', 'array'],
            'banner_orders.*' => ['nullable', 'integer'],
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

        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('banner', 'public');
            $maxUrutan = (int) (Banner::max('urutan') ?? 0);
            Banner::create([
                'gambar' => $path,
                'urutan' => $maxUrutan + 1,
                'is_active' => true,
            ]);
            $data['banner_path'] = $path;
        } elseif ($request->filled('cropped_banner_base64')) {
            $savedBanner = $this->storeBase64Image((string) $request->input('cropped_banner_base64'), 'banner');

            if ($savedBanner) {
                $maxUrutan = (int) (Banner::max('urutan') ?? 0);
                Banner::create([
                    'gambar' => $savedBanner,
                    'urutan' => $maxUrutan + 1,
                    'is_active' => true,
                ]);
                $data['banner_path'] = $savedBanner;
            }
        } elseif ($request->boolean('hapus_banner')) {
            foreach (Banner::all() as $b) {
                if ($b->gambar && Storage::disk('public')->exists($b->gambar)) {
                    Storage::disk('public')->delete($b->gambar);
                }
            }
            Banner::query()->delete();
            $this->deleteBanner($setting);
            $data['banner_path'] = null;
        }

        if ($request->has('banner_orders') && is_array($request->input('banner_orders'))) {
            foreach ($request->input('banner_orders') as $bannerId => $urutan) {
                Banner::where('id', $bannerId)->update(['urutan' => (int) $urutan]);
            }
        }

        if ($request->hasFile('qris_image')) {
            $this->deleteQrisImage($setting);
            $data['qris_image_path'] = $request->file('qris_image')->store('qris', 'public');
        } elseif ($request->boolean('hapus_qris')) {
            $this->deleteQrisImage($setting);
            $data['qris_image_path'] = null;
        }

        unset(
            $data['logo'],
            $data['cropped_logo_base64'],
            $data['hapus_logo'],
            $data['banner'],
            $data['cropped_banner_base64'],
            $data['hapus_banner'],
            $data['banner_orders'],
            $data['qris_image'],
            $data['hapus_qris']
        );

        $setting->fill($data)->save();

        ActivityLog::record('ubah', 'Memperbarui informasi & pengaturan toko', $setting);

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Pengaturan toko berhasil disimpan.');
    }

    /**
     * Geser posisi urutan banner ke atas.
     */
    public function moveUpBanner(Banner $banner): RedirectResponse
    {
        $previousBanner = Banner::query()
            ->where('urutan', '<', $banner->urutan)
            ->orderByDesc('urutan')
            ->first();

        if (! $previousBanner) {
            $previousBanner = Banner::query()
                ->where('id', '!=', $banner->id)
                ->where('urutan', '<=', $banner->urutan)
                ->orderByDesc('urutan')
                ->first();
        }

        if ($previousBanner) {
            $currentUrutan = $banner->urutan;
            $prevUrutan = $previousBanner->urutan;

            if ($currentUrutan === $prevUrutan) {
                $banner->urutan = max(0, $currentUrutan - 1);
            } else {
                $banner->urutan = $prevUrutan;
                $previousBanner->urutan = $currentUrutan;
                $previousBanner->save();
            }
            $banner->save();

            ActivityLog::record('ubah', "Menggeser urutan banner #{$banner->id} ke atas (urutan {$banner->urutan})", $banner);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Urutan banner berhasil digeser ke atas.');
    }

    /**
     * Geser posisi urutan banner ke bawah.
     */
    public function moveDownBanner(Banner $banner): RedirectResponse
    {
        $nextBanner = Banner::query()
            ->where('urutan', '>', $banner->urutan)
            ->orderBy('urutan')
            ->first();

        if (! $nextBanner) {
            $nextBanner = Banner::query()
                ->where('id', '!=', $banner->id)
                ->where('urutan', '>=', $banner->urutan)
                ->orderBy('urutan')
                ->first();
        }

        if ($nextBanner) {
            $currentUrutan = $banner->urutan;
            $nextUrutan = $nextBanner->urutan;

            if ($currentUrutan === $nextUrutan) {
                $banner->urutan = $currentUrutan + 1;
            } else {
                $banner->urutan = $nextUrutan;
                $nextBanner->urutan = $currentUrutan;
                $nextBanner->save();
            }
            $banner->save();

            ActivityLog::record('ubah', "Menggeser urutan banner #{$banner->id} ke bawah (urutan {$banner->urutan})", $banner);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Urutan banner berhasil digeser ke bawah.');
    }

    /**
     * Hapus satu item banner dari daftar multi-banner.
     */
    public function deleteBannerItem(Banner $banner): RedirectResponse
    {
        if ($banner->gambar && Storage::disk('public')->exists($banner->gambar)) {
            Storage::disk('public')->delete($banner->gambar);
        }

        $setting = StoreSetting::current();
        if ($setting->banner_path === $banner->gambar) {
            $nextAvailable = Banner::where('id', '!=', $banner->id)->value('gambar');
            $setting->banner_path = $nextAvailable;
            $setting->save();
        }

        $banner->delete();

        ActivityLog::record('hapus', "Menghapus banner #{$banner->id}");

        return redirect()->route('admin.settings.index')
            ->with('success', 'Banner berhasil dihapus.');
    }

    /**
     * Hapus file banner lama dari disk 'public' bila ada.
     */
    private function deleteBanner(StoreSetting $setting): void
    {
        if ($setting->banner_path) {
            Storage::disk('public')->delete($setting->banner_path);
        }
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
