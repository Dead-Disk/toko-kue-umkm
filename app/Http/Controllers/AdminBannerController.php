<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Banner;
use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminBannerController extends Controller
{
    /**
     * Tampilkan daftar banner promo dan pengaturan tampilan banner (khusus owner/admin).
     */
    public function index(): View
    {
        $banners = Banner::query()
            ->orderBy('urutan')
            ->latest('id')
            ->get();

        $setting = StoreSetting::current();

        return view('admin.banners.index', [
            'banners' => $banners,
            'setting' => $setting,
        ]);
    }

    /**
     * Simpan banner promo baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'judul' => ['nullable', 'string', 'max:255'],
            'subjudul' => ['nullable', 'string', 'max:500'],
            'link' => ['nullable', 'string', 'max:255'],
            'badge' => ['nullable', 'string', 'max:50'],
            'scale' => ['nullable', 'string', 'in:cover,contain,fill'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'gambar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:3072'],
        ]);

        $path = $request->file('gambar')->store('banners', 'public');

        $banner = Banner::create([
            'judul' => $data['judul'] ?? null,
            'subjudul' => $data['subjudul'] ?? null,
            'link' => $data['link'] ?? null,
            'badge' => $data['badge'] ?? null,
            'scale' => $data['scale'] ?? null,
            'urutan' => (int) ($data['urutan'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
            'gambar' => $path,
        ]);

        ActivityLog::record(
            'tambah',
            'Menambahkan banner promo: '.($banner->judul ?: 'Banner #'.$banner->id),
            $banner
        );

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner promo berhasil ditambahkan.');
    }

    /**
     * Perbarui data banner promo yang ada.
     */
    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $data = $request->validate([
            'judul' => ['nullable', 'string', 'max:255'],
            'subjudul' => ['nullable', 'string', 'max:500'],
            'link' => ['nullable', 'string', 'max:255'],
            'badge' => ['nullable', 'string', 'max:50'],
            'scale' => ['nullable', 'string', 'in:cover,contain,fill'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:3072'],
        ]);

        if ($request->hasFile('gambar')) {
            if ($banner->gambar && Storage::disk('public')->exists($banner->gambar)) {
                Storage::disk('public')->delete($banner->gambar);
            }
            $banner->gambar = $request->file('gambar')->store('banners', 'public');
        }

        $banner->judul = $data['judul'] ?? null;
        $banner->subjudul = $data['subjudul'] ?? null;
        $banner->link = $data['link'] ?? null;
        $banner->badge = $data['badge'] ?? null;
        $banner->scale = $data['scale'] ?? null;
        $banner->urutan = (int) ($data['urutan'] ?? 0);
        $banner->is_active = $request->boolean('is_active');
        $banner->save();

        ActivityLog::record(
            'ubah',
            'Memperbarui banner promo: '.($banner->judul ?: 'Banner #'.$banner->id),
            $banner
        );

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner promo berhasil diperbarui.');
    }

    /**
     * Hapus banner promo.
     */
    public function destroy(Banner $banner): RedirectResponse
    {
        $nama = $banner->judul ?: 'Banner #'.$banner->id;

        if ($banner->gambar && Storage::disk('public')->exists($banner->gambar)) {
            Storage::disk('public')->delete($banner->gambar);
        }

        $banner->delete();

        ActivityLog::record('hapus', 'Menghapus banner promo: '.$nama);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner promo berhasil dihapus.');
    }

    /**
     * Toggle status aktif banner.
     */
    public function toggle(Banner $banner): RedirectResponse
    {
        $banner->is_active = ! $banner->is_active;
        $banner->save();

        $statusText = $banner->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::record(
            'ubah',
            "Banner promo {$banner->judul} {$statusText}",
            $banner
        );

        return redirect()->route('admin.banners.index')
            ->with('success', "Banner promo berhasil {$statusText}.");
    }

    /**
     * Simpan preferensi scale, ukuran (rasio), dan slide timer banner toko.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'banner_scale' => ['required', 'string', 'in:cover,contain,fill'],
            'banner_size' => ['required', 'string', 'in:21:9,16:9,16:7,4:1'],
            'banner_slide_duration' => ['required', 'integer', 'min:1', 'max:60'],
            'banner_auto_slide' => ['nullable', 'boolean'],
        ]);

        $setting = StoreSetting::current();
        $setting->banner_scale = $data['banner_scale'];
        $setting->banner_size = $data['banner_size'];
        $setting->banner_slide_duration = (int) $data['banner_slide_duration'];
        $setting->banner_auto_slide = $request->boolean('banner_auto_slide', true);
        $setting->save();

        ActivityLog::record(
            'ubah',
            'Memperbarui pengaturan tampilan banner (Scale: '.$setting->banner_scale.', Ukuran: '.$setting->banner_size.', Durasi: '.$setting->banner_slide_duration.' detik)',
            $setting
        );

        return redirect()->route('admin.banners.index')
            ->with('success', 'Pengaturan tampilan banner berhasil disimpan.');
    }
}
