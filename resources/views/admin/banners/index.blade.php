@extends('admin.layouts.app', ['title' => 'Pengaturan Banner Promo', 'active' => 'banners'])

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-stone-200">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 011.385 1.155l-1.02 4.079 2.502 3.128a1 1 0 01-.78 1.624H2.36a1 1 0 01-.78-1.624l2.502-3.128-1.02-4.08A1 1 0 014.447 4.106l1.6.8L10 3.324V3a1 1 0 011-1z" clip-rule="evenodd"/>
                    </svg>
                    Khusus Owner Toko
                </span>
                <span class="text-xs text-stone-500">• Beranda &amp; Promo</span>
            </div>
            <h1 class="text-2xl font-serif font-bold text-[#540F18] tracking-tight">Pengaturan Banner Promo</h1>
            <p class="text-xs sm:text-sm text-stone-600 mt-0.5">
                Atur skala gambar, ukuran rasio banner, rotasi geser slide otomatis (5 detik), dan kelola materi banner toko.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('customer.index') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-white hover:bg-stone-50 text-stone-700 border border-stone-300 shadow-2xs transition">
                <svg class="w-4 h-4 text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Lihat di Web Toko
            </a>
            <button type="button"
                    onclick="openAddModal()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-white bg-[#6B0F1A] hover:bg-[#540F18] shadow-2xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Tambah Banner
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if (session('success'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs sm:text-sm flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs sm:text-sm space-y-1 shadow-2xs">
            <div class="flex items-center gap-2 font-semibold">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span>Mohon periksa kesalahan input berikut:</span>
            </div>
            <ul class="list-disc list-inside text-xs pl-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Live Preview Card (Pratinjau Langsung Banner Toko) -->
    <div class="bg-white rounded-2xl border border-stone-200 p-4 sm:p-6 shadow-xs space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 border-b border-stone-100 pb-3">
            <div>
                <h2 class="text-sm sm:text-base font-serif font-bold text-[#540F18] flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#6B0F1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Pratinjau Langsung (Live Preview)
                </h2>
                <p class="text-[11px] sm:text-xs text-stone-500">
                    Tampilan ini mencerminkan persis ukuran, scale, dan rotasi otomatis banner di halaman web pembeli.
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-stone-500 bg-stone-100 px-2.5 py-1 rounded-full">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Rotasi per {{ $setting->banner_slide_duration ?? 5 }} Detik
            </span>
        </div>

        <div class="max-w-4xl mx-auto py-2">
            <x-banner-carousel :banners="$banners->where('is_active', true)" :setting="$setting" preview-id="adminPreviewBanner" />
        </div>
    </div>

    <!-- Form Pengaturan Tampilan & Rotasi Banner -->
    <div class="bg-white rounded-2xl border border-stone-200 p-4 sm:p-6 shadow-xs">
        <div class="border-b border-stone-100 pb-3 mb-5">
            <h2 class="text-sm sm:text-base font-serif font-bold text-[#540F18]">
                Konfigurasi Ukuran, Scale, &amp; Timer Banner
            </h2>
            <p class="text-[11px] sm:text-xs text-stone-500">
                Pilih ukuran banner dan skala tampilan agar gambar terlihat rapi dan proporsional.
            </p>
        </div>

        <form action="{{ route('admin.banners.settings') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Pilihan Ukuran Banner (Aspek Rasio) -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                    Ukuran Banner (Aspek Rasio)
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @php
                        $currentSize = old('banner_size', $setting->banner_size ?? '21:9');
                        $sizes = [
                            [
                                'value' => '21:9',
                                'label' => 'Ultra-wide (21:9)',
                                'desc' => 'Ramping & modern (standar promo web)',
                                'badge' => 'Rekomendasi',
                            ],
                            [
                                'value' => '16:9',
                                'label' => 'Standar (16:9)',
                                'desc' => 'Tinggi seimbang proporsional',
                                'badge' => 'Landscape',
                            ],
                            [
                                'value' => '16:7',
                                'label' => 'Lebar (16:7)',
                                'desc' => 'Ruang gambar lebih leluasa',
                                'badge' => 'Heroik',
                            ],
                            [
                                'value' => '4:1',
                                'label' => 'Kompak (4:1)',
                                'desc' => 'Ramping minimalis hemat ruang',
                                'badge' => 'Slim',
                            ],
                        ];
                    @endphp

                    @foreach ($sizes as $s)
                        <label class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition select-none hover:bg-stone-50 has-checked:border-[#6B0F1A] has-checked:bg-rose-50/40 has-checked:ring-1 has-checked:ring-[#6B0F1A] border-stone-200">
                            <input type="radio" name="banner_size" value="{{ $s['value'] }}" class="sr-only" {{ $currentSize === $s['value'] ? 'checked' : '' }}>
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-xs text-stone-800">{{ $s['label'] }}</span>
                                <span class="text-[9px] font-semibold px-1.5 py-0.2 rounded bg-stone-100 text-stone-600">{{ $s['badge'] }}</span>
                            </div>
                            <p class="text-[10px] text-stone-500 leading-tight">{{ $s['desc'] }}</p>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Pilihan Scale Gambar (Object Fit) -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">
                    Scale Tampilan Gambar (Object Fit)
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @php
                        $currentScale = old('banner_scale', $setting->banner_scale ?? 'cover');
                        $scales = [
                            [
                                'value' => 'cover',
                                'label' => 'Cover (Isi Penuh)',
                                'desc' => 'Gambar mengisi seluruh wadah banner tanpa ruang kosong (potong proporsional).',
                                'badge' => 'Terbaik',
                            ],
                            [
                                'value' => 'contain',
                                'label' => 'Contain (Muat Utuh)',
                                'desc' => 'Seluruh gambar tampil utuh dengan rasio asli tanpa terpotong sama sekali.',
                                'badge' => 'Utuh',
                            ],
                            [
                                'value' => 'fill',
                                'label' => 'Fill (Stretch Penuh)',
                                'desc' => 'Gambar diregangkan agar pas dengan wadah banner secara tepat.',
                                'badge' => 'Regang',
                            ],
                        ];
                    @endphp

                    @foreach ($scales as $sc)
                        <label class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition select-none hover:bg-stone-50 has-checked:border-[#6B0F1A] has-checked:bg-rose-50/40 has-checked:ring-1 has-checked:ring-[#6B0F1A] border-stone-200">
                            <input type="radio" name="banner_scale" value="{{ $sc['value'] }}" class="sr-only" {{ $currentScale === $sc['value'] ? 'checked' : '' }}>
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-xs text-stone-800">{{ $sc['label'] }}</span>
                                <span class="text-[9px] font-semibold px-1.5 py-0.2 rounded bg-stone-100 text-stone-600">{{ $sc['badge'] }}</span>
                            </div>
                            <p class="text-[10px] text-stone-500 leading-tight">{{ $sc['desc'] }}</p>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Pengaturan Geser Slide (Slide Timer) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-stone-100">
                <div class="space-y-1.5">
                    <label for="banner_slide_duration" class="block text-xs font-bold text-stone-700">
                        Durasi Waktu Geser Slide (Detik)
                    </label>
                    <div class="relative max-w-xs">
                        <input type="number"
                               id="banner_slide_duration"
                               name="banner_slide_duration"
                               min="1"
                               max="60"
                               value="{{ old('banner_slide_duration', $setting->banner_slide_duration ?? 5) }}"
                               class="w-full px-3 py-2 border border-stone-300 rounded-xl text-xs sm:text-sm focus:ring-1 focus:ring-[#6B0F1A] focus:border-[#6B0F1A]"
                               required>
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-stone-400">detik (Default: 5s)</span>
                    </div>
                    <p class="text-[11px] text-stone-500">Waktu jeda antar transisi banner otomatis (misal: 5 detik).</p>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-stone-700">Rotasi Geser Otomatis</label>
                    <label class="inline-flex items-center gap-2.5 cursor-pointer mt-1 select-none">
                        <input type="hidden" name="banner_auto_slide" value="0">
                        <input type="checkbox"
                               name="banner_auto_slide"
                               value="1"
                               class="w-4 h-4 text-[#6B0F1A] rounded border-stone-300 focus:ring-[#6B0F1A]"
                               {{ old('banner_auto_slide', $setting->banner_auto_slide ?? true) ? 'checked' : '' }}>
                        <span class="text-xs text-stone-700 font-medium">Aktifkan pergantian slide otomatis setiap 5 detik</span>
                    </label>
                </div>
            </div>

            <!-- Submit Setting Button -->
            <div class="pt-2 flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold text-white bg-[#6B0F1A] hover:bg-[#540F18] shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Pengaturan Tampilan
                </button>
            </div>
        </form>
    </div>

    <!-- Kelola Banner Promosi (Daftar Banner) -->
    <div class="bg-white rounded-2xl border border-stone-200 p-4 sm:p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-stone-100">
            <div>
                <h2 class="text-sm sm:text-base font-serif font-bold text-[#540F18]">
                    Daftar Banner Promosi Aktif ({{ $banners->count() }})
                </h2>
                <p class="text-[11px] sm:text-xs text-stone-500">
                    Banner aktif akan otomatis masuk ke slide beranda toko pembeli.
                </p>
            </div>
            <button type="button"
                    onclick="openAddModal()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-[#6B0F1A] hover:bg-[#540F18] transition shadow-2xs">
                + Tambah Banner
            </button>
        </div>

        @if ($banners->isEmpty())
            <div class="p-8 text-center rounded-xl border border-dashed border-stone-300 bg-stone-50/50 space-y-2">
                <div class="w-12 h-12 rounded-full bg-rose-50 text-[#6B0F1A] flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18M3 4.5h15.5a1 1 0 0 1 .8 1.6L16.7 9l2.6 2.9a1 1 0 0 1-.8 1.6H3" />
                    </svg>
                </div>
                <p class="text-xs sm:text-sm font-semibold text-stone-700">Belum ada banner promosi</p>
                <p class="text-[11px] text-stone-500 max-w-sm mx-auto">
                    Klik tombol "Tambah Banner" di atas untuk mengunggah materi banner promosi pertama Anda.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($banners as $b)
                    <div class="relative bg-stone-50/60 rounded-xl border border-stone-200 p-3 space-y-3 flex flex-col justify-between transition hover:shadow-sm">
                        <div class="space-y-2">
                            <!-- Thumbnail & Status -->
                            <div class="relative aspect-[21/9] rounded-lg overflow-hidden bg-stone-200 border border-stone-200/80">
                                <img src="{{ $b->gambar_url }}" alt="{{ $b->judul ?? 'Banner' }}" class="w-full h-full object-cover">
                                <div class="absolute top-1.5 right-1.5 flex items-center gap-1">
                                    @if ($b->is_active)
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500 text-white shadow-2xs">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-stone-400 text-white shadow-2xs">Nonaktif</span>
                                    @endif
                                </div>
                                @if ($b->badge)
                                    <div class="absolute bottom-1.5 left-1.5">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-600 text-white shadow-2xs">{{ $b->badge }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Banner Details -->
                            <div>
                                <h3 class="font-bold text-xs sm:text-sm text-stone-900 line-clamp-1" title="{{ $b->judul }}">
                                    {{ $b->judul ?: 'Tanpa Judul' }}
                                </h3>
                                @if ($b->subjudul)
                                    <p class="text-[11px] text-stone-500 line-clamp-1 mt-0.5" title="{{ $b->subjudul }}">
                                        {{ $b->subjudul }}
                                    </p>
                                @endif
                                <div class="flex items-center gap-2 text-[10px] text-stone-400 mt-1">
                                    <span>Urutan: #{{ $b->urutan }}</span>
                                    @if ($b->scale)
                                        <span>• Scale: {{ $b->scale }}</span>
                                    @endif
                                    @if ($b->link)
                                        <span class="truncate">• Link: {{ $b->link }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Actions Bar -->
                        <div class="pt-2 border-t border-stone-200 flex items-center justify-between gap-2">
                            <!-- Toggle Active Form -->
                            <form action="{{ route('admin.banners.toggle', $b) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-[11px] font-semibold {{ $b->is_active ? 'text-amber-700 hover:text-amber-900' : 'text-emerald-700 hover:text-emerald-900' }} transition">
                                    {{ $b->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>

                            <div class="flex items-center gap-1.5">
                                <button type="button"
                                        onclick='openEditModal(@json($b))'
                                        class="px-2 py-1 text-[11px] font-medium text-stone-700 bg-white border border-stone-300 rounded hover:bg-stone-50 transition">
                                    Edit
                                </button>
                                <form action="{{ route('admin.banners.destroy', $b) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus banner ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2 py-1 text-[11px] font-medium text-rose-700 bg-rose-50 border border-rose-200 rounded hover:bg-rose-100 transition">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Banner -->
<div id="addModal" class="fixed inset-0 z-50 hidden bg-stone-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-stone-100 pb-3">
            <h3 class="font-serif font-bold text-base sm:text-lg text-[#540F18]">Tambah Banner Promo Baru</h3>
            <button type="button" onclick="closeAddModal()" class="text-stone-400 hover:text-stone-600 text-lg font-bold">&times;</button>
        </div>

        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Upload Gambar -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700">Gambar Banner <span class="text-rose-500">*</span></label>
                <input type="file" name="gambar" accept="image/*" required class="w-full text-xs text-stone-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#6B0F1A] file:text-white hover:file:bg-[#540F18]">
                <p class="text-[10px] text-stone-400">Format: JPG, PNG, WEBP, SVG (Maks. 3MB). Disarankan rasio sesuai pengaturan banner.</p>
            </div>

            <!-- Judul Banner -->
            <div class="space-y-1">
                <label class="block text-xs font-bold text-stone-700">Judul Banner (Opsional)</label>
                <input type="text" name="judul" placeholder="Contoh: Promo Spesial Hari Ini" class="w-full px-3 py-2 border border-stone-300 rounded-xl text-xs sm:text-sm">
            </div>

            <!-- Subjudul / Deskripsi -->
            <div class="space-y-1">
                <label class="block text-xs font-bold text-stone-700">Subjudul / Deskripsi (Opsional)</label>
                <input type="text" name="subjudul" placeholder="Contoh: Diskon 20% untuk semua varian kue tart" class="w-full px-3 py-2 border border-stone-300 rounded-xl text-xs sm:text-sm">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <!-- Badge Promo -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-stone-700">Badge Tag (Opsional)</label>
                    <input type="text" name="badge" placeholder="Contoh: Diskon 20%" class="w-full px-3 py-2 border border-stone-300 rounded-xl text-xs sm:text-sm">
                </div>

                <!-- Urutan Tampil -->
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-stone-700">Urutan (Angka)</label>
                    <input type="number" name="urutan" value="0" min="0" class="w-full px-3 py-2 border border-stone-300 rounded-xl text-xs sm:text-sm">
                </div>
            </div>

            <!-- Link Tujuan -->
            <div class="space-y-1">
                <label class="block text-xs font-bold text-stone-700">Link Tujuan Klik (Opsional)</label>
                <input type="url" name="link" placeholder="https://... atau /produk" class="w-full px-3 py-2 border border-stone-300 rounded-xl text-xs sm:text-sm">
            </div>

            <!-- Scale Override -->
            <div class="space-y-1">
                <label class="block text-xs font-bold text-stone-700">Khusus Scale Gambar Ini (Opsional)</label>
                <select name="scale" class="w-full px-3 py-2 border border-stone-300 rounded-xl text-xs sm:text-sm">
                    <option value="">Ikuti Pengaturan Global (Default)</option>
                    <option value="cover">Cover (Isi Penuh)</option>
                    <option value="contain">Contain (Muat Utuh)</option>
                    <option value="fill">Fill (Regang Penuh)</option>
                </select>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="add_is_active" value="1" checked class="w-4 h-4 text-[#6B0F1A] rounded border-stone-300">
                <label for="add_is_active" class="text-xs text-stone-700 font-medium">Aktifkan banner ini sekarang</label>
            </div>

            <div class="pt-3 border-t border-stone-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-stone-600 bg-stone-100 hover:bg-stone-200 transition">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-semibold text-white bg-[#6B0F1A] hover:bg-[#540F18] transition">Simpan Banner</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Banner -->
<div id="editModal" class="fixed inset-0 z-50 hidden bg-stone-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-stone-100 pb-3">
            <h3 class="font-serif font-bold text-base sm:text-lg text-[#540F18]">Edit Banner Promo</h3>
            <button type="button" onclick="closeEditModal()" class="text-stone-400 hover:text-stone-600 text-lg font-bold">&times;</button>
        </div>

        <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- Ganti Gambar -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700">Ganti Gambar (Biarkan kosong jika tidak diubah)</label>
                <input type="file" name="gambar" accept="image/*" class="w-full text-xs text-stone-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-stone-700 file:text-white hover:file:bg-stone-800">
            </div>

            <!-- Judul Banner -->
            <div class="space-y-1">
                <label class="block text-xs font-bold text-stone-700">Judul Banner (Opsional)</label>
                <input type="text" id="edit_judul" name="judul" class="w-full px-3 py-2 border border-stone-300 rounded-xl text-xs sm:text-sm">
            </div>

            <!-- Subjudul -->
            <div class="space-y-1">
                <label class="block text-xs font-bold text-stone-700">Subjudul / Deskripsi (Opsional)</label>
                <input type="text" id="edit_subjudul" name="subjudul" class="w-full px-3 py-2 border border-stone-300 rounded-xl text-xs sm:text-sm">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-stone-700">Badge Tag (Opsional)</label>
                    <input type="text" id="edit_badge" name="badge" class="w-full px-3 py-2 border border-stone-300 rounded-xl text-xs sm:text-sm">
                </div>
                <div class="space-y-1">
                    <label class="block text-xs font-bold text-stone-700">Urutan (Angka)</label>
                    <input type="number" id="edit_urutan" name="urutan" min="0" class="w-full px-3 py-2 border border-stone-300 rounded-xl text-xs sm:text-sm">
                </div>
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-stone-700">Link Tujuan Klik (Opsional)</label>
                <input type="url" id="edit_link" name="link" class="w-full px-3 py-2 border border-stone-300 rounded-xl text-xs sm:text-sm">
            </div>

            <div class="space-y-1">
                <label class="block text-xs font-bold text-stone-700">Scale Gambar Ini</label>
                <select id="edit_scale" name="scale" class="w-full px-3 py-2 border border-stone-300 rounded-xl text-xs sm:text-sm">
                    <option value="">Ikuti Pengaturan Global (Default)</option>
                    <option value="cover">Cover (Isi Penuh)</option>
                    <option value="contain">Contain (Muat Utuh)</option>
                    <option value="fill">Fill (Regang Penuh)</option>
                </select>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="w-4 h-4 text-[#6B0F1A] rounded border-stone-300">
                <label for="edit_is_active" class="text-xs text-stone-700 font-medium">Banner aktif</label>
            </div>

            <div class="pt-3 border-t border-stone-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-stone-600 bg-stone-100 hover:bg-stone-200 transition">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-semibold text-white bg-[#6B0F1A] hover:bg-[#540F18] transition">Perbarui Banner</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }
    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }
    function openEditModal(banner) {
        const form = document.getElementById('editForm');
        form.action = `/admin/banners/${banner.id}`;
        document.getElementById('edit_judul').value = banner.judul || '';
        document.getElementById('edit_subjudul').value = banner.subjudul || '';
        document.getElementById('edit_badge').value = banner.badge || '';
        document.getElementById('edit_urutan').value = banner.urutan || 0;
        document.getElementById('edit_link').value = banner.link || '';
        document.getElementById('edit_scale').value = banner.scale || '';
        document.getElementById('edit_is_active').checked = !!banner.is_active;

        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endsection
