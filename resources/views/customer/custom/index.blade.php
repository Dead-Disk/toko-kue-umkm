<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Custom Cake - {{ $storeSetting->store_name ?? 'Nak Mbarep' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-nav.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Instrument Sans', system-ui, sans-serif;
        }
    </style>
</head>
<body class="bg-[#FAF2EE] text-gray-800 min-h-screen antialiased flex flex-col font-sans">
    {{-- Top Navigation Bar --}}
    @include('customer.partials.navbar')

    {{-- Main Container Card --}}
    <main class="flex-1 flex items-start justify-center px-3 py-6 sm:px-6 sm:py-8 lg:py-10">
        <div class="w-full max-w-lg md:max-w-2xl lg:max-w-5xl xl:max-w-6xl mx-auto bg-white rounded-2xl sm:rounded-3xl shadow-xl border border-stone-200/70 p-4 sm:p-6 lg:p-8 space-y-6">

            {{-- Header & Link Kembali --}}
            <div class="border-b border-stone-100 pb-4 sm:pb-5">
                <a href="{{ route('customer.index') }}" class="inline-flex items-center gap-1.5 text-xs text-[#6B0F1A] hover:text-[#540F18] font-semibold transition mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-serif font-bold text-[#6B0F1A] tracking-tight">Pesan Custom Cake</h1>
                        <p class="text-xs sm:text-sm text-stone-500 mt-1">Buat kue spesial sesuai dengan impian dan seleramu.</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-[#6B0F1A] border border-[#DE9DA7]/50 w-fit">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#6B0F1A] animate-pulse"></span>
                        Estimasi Harga Otomatis
                    </span>
                </div>
            </div>

            {{-- Form Custom Cake --}}
            <form action="{{ route('customer.custom.review') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- Alert Error Validation (Spans full width) --}}
                @if ($errors->any())
                    <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-700 space-y-1.5">
                        <div class="flex items-center gap-1.5 font-bold text-rose-800">
                            <svg class="w-4 h-4 shrink-0 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span>Terjadi kesalahan pengisian form:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Two-Column Grid on Desktop, Single-Column on Mobile --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

                    {{-- ==================== KOLOM KIRI (lg:col-span-7): Data Pemesan & Kustomisasi Kue ==================== --}}
                    <div class="lg:col-span-7 space-y-6 min-w-0">

                        {{-- Section 1: Informasi Pemesan --}}
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 pb-2 border-b border-stone-100">
                                <span class="w-6 h-6 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-2xs">1</span>
                                <h2 class="text-sm font-bold text-stone-800">Informasi Pemesan</h2>
                            </div>

                            {{-- Nama Pemesan & Nomor WA (Berdampingan di sm ke atas, stacked di mobile 412px) --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                                {{-- Nama Pemesan --}}
                                <div>
                                    <label for="nama_pemesan" class="block text-xs font-bold text-stone-700 mb-1.5">
                                        Nama Pemesan <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text"
                                           id="nama_pemesan"
                                           name="nama_pemesan"
                                           value="{{ old('nama_pemesan', $draft['nama_pemesan'] ?? '') }}"
                                           placeholder="Masukkan nama lengkap"
                                           required
                                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-stone-200 bg-stone-50/30 text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] focus:bg-white transition">
                                </div>

                                {{-- Nomor WA --}}
                                <div>
                                    <label for="nomor_wa" class="block text-xs font-bold text-stone-700 mb-1.5">
                                        Nomor WhatsApp <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="tel"
                                           id="nomor_wa"
                                           name="nomor_wa"
                                           value="{{ old('nomor_wa', $draft['nomor_wa'] ?? '') }}"
                                           placeholder="Contoh: 081234567890"
                                           required
                                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-stone-200 bg-stone-50/30 text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] focus:bg-white transition">
                                </div>
                            </div>

                            {{-- Alamat Lengkap --}}
                            <div>
                                <label for="alamat_lengkap" class="block text-xs font-bold text-stone-700 mb-1.5">
                                    Alamat Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <textarea id="alamat_lengkap"
                                          name="alamat_lengkap"
                                          rows="3"
                                          placeholder="Isi alamat pengiriman, atau ketik &quot;Ambil di toko&quot; bila ambil sendiri"
                                          required
                                          class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-stone-200 bg-stone-50/30 text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] focus:bg-white transition resize-none">{{ old('alamat_lengkap', $draft['alamat_lengkap'] ?? '') }}</textarea>
                            </div>
                        </div>

                        {{-- Section 2: Detail Desain & Kustomisasi --}}
                        <div class="space-y-4 pt-2">
                            <div class="flex items-center gap-2 pb-2 border-b border-stone-100">
                                <span class="w-6 h-6 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-2xs">2</span>
                                <h2 class="text-sm font-bold text-stone-800">Detail &amp; Desain Kue</h2>
                            </div>

                            {{-- Produk Acuan & Ukuran Kue --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 sm:gap-4">
                                {{-- Produk Acuan --}}
                                <div class="sm:col-span-2">
                                    <label for="base_product_id" class="block text-xs font-bold text-stone-700 mb-1.5">
                                        Produk Acuan <span class="text-stone-400 font-normal">(opsional)</span>
                                    </label>
                                    <select id="base_product_id"
                                            name="base_product_id"
                                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-stone-200 bg-stone-50/30 text-stone-800 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] focus:bg-white transition cursor-pointer">
                                        <option value="">-- Tanpa Produk Acuan --</option>
                                        @foreach ($products as $item)
                                            <option value="{{ $item->id }}" @selected((string) old('base_product_id', $draft['base_product_id'] ?? '') === (string) $item->id)
                                                    data-harga="{{ $item->harga }}"
                                                    data-harga-format="Rp {{ number_format($item->harga, 0, ',', '.') }}"
                                                    data-nama="{{ $item->nama_kue }}"
                                                    data-foto="{{ $item->foto_url ?? '' }}"
                                                    data-category-id="{{ $item->category_id ?? '' }}">
                                                {{ $item->nama_kue }} - Rp {{ number_format($item->harga, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>

                                    {{-- Live Preview Box Produk Acuan --}}
                                    <div id="base-product-preview" class="hidden mt-2.5 p-3 rounded-2xl bg-gradient-to-r from-stone-50 via-rose-50/20 to-stone-50 border border-[#DE9DA7]/60 shadow-xs transition-all">
                                        <div class="flex items-center gap-3">
                                            {{-- Thumbnail Gambar Produk --}}
                                            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl overflow-hidden bg-white border border-stone-200 shrink-0 flex items-center justify-center shadow-2xs">
                                                <img id="base-product-img" src="" alt="Foto Produk Acuan" class="w-full h-full object-cover hidden">
                                                <div id="base-product-img-fallback" class="w-full h-full flex flex-col items-center justify-center bg-rose-50 text-[#6B0F1A]">
                                                    <svg class="w-6 h-6 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                                    </svg>
                                                </div>
                                            </div>

                                            {{-- Rincian Produk Acuan --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-100 text-[#6B0F1A]">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-[#6B0F1A]"></span>
                                                        Acuan Terpilih
                                                    </span>
                                                    <button type="button"
                                                            id="base-product-reset"
                                                            class="text-[11px] text-stone-400 hover:text-rose-600 font-medium transition cursor-pointer flex items-center gap-0.5"
                                                            title="Batalkan pilihan produk acuan">
                                                        <span>✕ Hapus</span>
                                                    </button>
                                                </div>
                                                <p id="base-product-nama" class="font-bold text-stone-800 text-xs sm:text-sm truncate mt-0.5">-</p>
                                                <p class="text-xs text-stone-500 mt-0.5 flex items-center gap-1.5">
                                                    <span>Harga Acuan:</span>
                                                    <strong id="base-product-harga" class="font-bold text-[#6B0F1A]">-</strong>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Kategori Kue (Filter Opsi Varian) --}}
                                <div class="sm:col-span-2">
                                    <label for="cake_category_id" class="block text-xs font-bold text-stone-700 mb-1.5">
                                        Kategori Kue <span class="text-stone-400 font-normal">(opsional, untuk menyaring bentuk &amp; ukuran)</span>
                                    </label>
                                    <select id="cake_category_id"
                                            name="cake_category_id"
                                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-stone-200 bg-stone-50/30 text-stone-800 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] focus:bg-white transition cursor-pointer">
                                        <option value="">-- Semua Kategori --</option>
                                        @foreach ($categoriesWithOptions as $category)
                                            <option value="{{ $category->id }}" @selected((string) $selectedCategoryId === (string) $category->id)>
                                                {{ $category->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Ukuran Kue --}}
                                <div class="sm:col-span-2">
                                    <label for="cake_option_id" class="block text-xs font-bold text-stone-700 mb-1.5">
                                        Ukuran Kue <span class="text-rose-500">*</span>
                                    </label>
                                    <select id="cake_option_id"
                                            name="cake_option_id"
                                            required
                                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-stone-200 bg-stone-50/30 text-stone-800 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] focus:bg-white transition">
                                        <option value="">-- Pilih Ukuran --</option>
                                        @foreach ($cakeOptions as $option)
                                            <option value="{{ $option->id }}" @selected((string) old('cake_option_id', $draft['cake_option_id'] ?? '') === (string) $option->id)
                                                    data-harga="{{ $option->harga_dasar }}"
                                                    data-category-id="{{ $option->category_id ?? '' }}">
                                                {{ $option->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p id="cake-option-empty" class="hidden text-[11px] text-amber-700 mt-1.5 font-medium">
                                        Kategori ini belum memiliki opsi varian khusus. Silakan pilih kategori lain atau pilih "Semua Kategori".
                                    </p>
                                </div>
                            </div>

                            {{-- Deskripsi Request --}}
                            <div>
                                <label for="deskripsi_custom" class="block text-xs font-bold text-stone-700 mb-1.5">
                                    Deskripsi Request (rasa, tulisan di atas kue, dll.) <span class="text-rose-500">*</span>
                                </label>
                                <textarea id="deskripsi_custom"
                                          name="deskripsi_custom"
                                          rows="4"
                                          placeholder="Tuliskan tema, warna krim, tulisan ucapan, rasa kue, dsb."
                                          required
                                          class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-stone-200 bg-stone-50/30 text-stone-800 placeholder:text-stone-400 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] focus:bg-white transition resize-none">{{ old('deskripsi_custom', $draft['deskripsi_custom'] ?? '') }}</textarea>
                            </div>

                            {{-- Foto Referensi Desain --}}
                            <div>
                                <label for="foto_referensi" class="block text-xs font-bold text-stone-700 mb-1.5">
                                    Foto Referensi Desain Kue <span class="text-stone-400 font-normal">(opsional)</span>
                                </label>
                                <input type="file"
                                       id="foto_referensi"
                                       name="foto_referensi"
                                       accept="image/*"
                                       class="w-full text-xs text-stone-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#6B0F1A]/10 file:text-[#6B0F1A] hover:file:bg-[#6B0F1A]/20 cursor-pointer border border-stone-200 rounded-xl p-2 bg-stone-50/30 focus:outline-none focus:border-[#6B0F1A]">
                                @if (! empty($draft['foto_referensi']))
                                    <p class="text-[11px] text-emerald-600 mt-1.5 flex items-center gap-1 font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Foto tersimpan: {{ basename($draft['foto_referensi']) }} (biarkan kosong bila tidak ingin mengganti)
                                    </p>
                                @endif
                            </div>
                        </div>

                    </div>

                    {{-- ==================== KOLOM KANAN (lg:col-span-5): Pengiriman, Pembayaran & Ringkasan ==================== --}}
                    <div class="lg:col-span-5 space-y-6 min-w-0">

                        {{-- Section 3: Pengiriman & Jadwal --}}
                        <div class="space-y-4">
                            <div class="flex items-center gap-2 pb-2 border-b border-stone-100">
                                <span class="w-6 h-6 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-2xs">3</span>
                                <h2 class="text-sm font-bold text-stone-800">Pengiriman &amp; Jadwal</h2>
                            </div>

                            {{-- Jumlah Beli & Tanggal Ambil --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2 gap-3.5 sm:gap-4">
                                {{-- Jumlah Beli --}}
                                <div>
                                    <label for="jumlah" class="block text-xs font-bold text-stone-700 mb-1.5">
                                        Jumlah Beli (Pcs / Biji) <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="number" id="jumlah" name="jumlah" value="{{ $jumlah }}"
                                           min="1"
                                           max="{{ $maxQuantity }}"
                                           step="1"
                                           data-max-quantity="{{ $maxQuantity }}"
                                           data-max-quantity-message="Maksimal pemesanan untuk pesanan ini adalah {{ $maxQuantity }} pcs."
                                           required
                                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-stone-200 bg-stone-50/30 text-stone-800 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] focus:bg-white transition">
                                    <p class="text-[11px] text-stone-400 mt-1">Maksimal pemesanan {{ $maxQuantity }} pcs per order.</p>
                                </div>

                                {{-- Tanggal Ambil --}}
                                <div>
                                    <label for="tanggal_ambil" class="block text-xs font-bold text-stone-700 mb-1.5">
                                        Tanggal Ambil <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="date"
                                           id="tanggal_ambil"
                                           name="tanggal_ambil"
                                           value="{{ old('tanggal_ambil', $draft['tanggal_ambil'] ?? '') }}"
                                           min="{{ now()->toDateString() }}"
                                           required
                                           class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-stone-200 bg-stone-50/30 text-stone-800 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] focus:bg-white transition">
                                    @error('tanggal_ambil')
                                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Opsi Pengiriman --}}
                            <div>
                                <span class="block text-xs font-bold text-stone-700 mb-1.5">
                                    Opsi Pengiriman <span class="text-rose-500">*</span>
                                </span>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-stone-200 bg-stone-50/40 hover:bg-stone-50 cursor-pointer transition has-[:checked]:border-[#6B0F1A] has-[:checked]:bg-[#6B0F1A]/5 has-[:checked]:text-[#6B0F1A]">
                                        <input type="radio"
                                               name="opsi_pengiriman"
                                               value="kurir_toko"
                                               @checked(old('opsi_pengiriman', $draft['opsi_pengiriman'] ?? 'kurir_toko') === 'kurir_toko')
                                               class="text-[#6B0F1A] focus:ring-[#6B0F1A]">
                                        <span class="text-xs font-semibold">Kurir Toko</span>
                                    </label>
                                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-stone-200 bg-stone-50/40 hover:bg-stone-50 cursor-pointer transition has-[:checked]:border-[#6B0F1A] has-[:checked]:bg-[#6B0F1A]/5 has-[:checked]:text-[#6B0F1A]">
                                        <input type="radio"
                                               name="opsi_pengiriman"
                                               value="ambil_sendiri"
                                               @checked(old('opsi_pengiriman', $draft['opsi_pengiriman'] ?? '') === 'ambil_sendiri')
                                               class="text-[#6B0F1A] focus:ring-[#6B0F1A]">
                                        <span class="text-xs font-semibold">Ambil Sendiri</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Section 4: Metode Pembayaran --}}
                        <div class="space-y-4 pt-2">
                            <div class="flex items-center gap-2 pb-2 border-b border-stone-100">
                                <span class="w-6 h-6 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-2xs">4</span>
                                <h2 class="text-sm font-bold text-stone-800">Metode Pembayaran</h2>
                            </div>

                            <div class="space-y-2.5">
                                {{-- Transfer Bank --}}
                                <label class="flex items-start gap-3 p-3.5 border border-stone-200 rounded-xl cursor-pointer transition-all hover:border-[#6B0F1A]/40 hover:bg-[#FAF2EE]/40 has-[:checked]:border-[#6B0F1A] has-[:checked]:bg-[#FAF2EE]/80 has-[:checked]:shadow-xs">
                                    <input type="radio"
                                           name="metode_pembayaran"
                                           value="transfer_bank"
                                           class="mt-1 w-4 h-4 text-[#6B0F1A] focus:ring-[#6B0F1A] accent-[#6B0F1A]"
                                           @checked(old('metode_pembayaran', $draft['metode_pembayaran'] ?? 'transfer_bank') === 'transfer_bank')
                                           required>
                                    <div class="flex-1">
                                        <span class="text-xs sm:text-sm font-bold text-stone-800 block">Transfer Bank</span>
                                        <p class="text-[11px] text-stone-500 mt-0.5">Bayar melalui BCA, BNI, BRI, Mandiri, dan lainnya</p>
                                    </div>
                                </label>

                                {{-- E-Wallet --}}
                                <label class="flex items-start gap-3 p-3.5 border border-stone-200 rounded-xl cursor-pointer transition-all hover:border-[#6B0F1A]/40 hover:bg-[#FAF2EE]/40 has-[:checked]:border-[#6B0F1A] has-[:checked]:bg-[#FAF2EE]/80 has-[:checked]:shadow-xs">
                                    <input type="radio"
                                           name="metode_pembayaran"
                                           value="qris"
                                           class="mt-1 w-4 h-4 text-[#6B0F1A] focus:ring-[#6B0F1A] accent-[#6B0F1A]"
                                           @checked(old('metode_pembayaran', $draft['metode_pembayaran'] ?? '') === 'qris')
                                           required>
                                    <div class="flex-1">
                                        <span class="text-xs sm:text-sm font-bold text-stone-800 block">E-Wallet</span>
                                        <p class="text-[11px] text-stone-500 mt-0.5">GoPay, OVO, DANA, ShopeePay, dan lainnya</p>
                                    </div>
                                </label>

                                {{-- COD / Bayar di Toko --}}
                                <label class="flex items-start gap-3 p-3.5 border border-stone-200 rounded-xl cursor-pointer transition-all hover:border-[#6B0F1A]/40 hover:bg-[#FAF2EE]/40 has-[:checked]:border-[#6B0F1A] has-[:checked]:bg-[#FAF2EE]/80 has-[:checked]:shadow-xs">
                                    <input type="radio"
                                           name="metode_pembayaran"
                                           value="cash"
                                           class="mt-1 w-4 h-4 text-[#6B0F1A] focus:ring-[#6B0F1A] accent-[#6B0F1A]"
                                           @checked(old('metode_pembayaran', $draft['metode_pembayaran'] ?? '') === 'cash')
                                           required>
                                    <div class="flex-1">
                                        <span class="text-xs sm:text-sm font-bold text-stone-800 block">COD / Bayar di Toko</span>
                                        <p class="text-[11px] text-stone-500 mt-0.5">Bayar saat pesanan tiba / ambil di toko</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Section 5: Ringkasan Harga & Tombol Submit --}}
                        <div class="space-y-4 pt-2">
                            {{-- Estimasi Total Box --}}
                            <div class="rounded-2xl bg-gradient-to-br from-[#FAF2EE] to-rose-50/70 border border-[#DE9DA7]/60 p-4 sm:p-5 space-y-2 shadow-2xs">
                                <div class="flex items-center justify-between text-xs text-stone-600">
                                    <span class="font-semibold uppercase tracking-wider text-[11px] text-stone-500">Estimasi Total Harga</span>
                                    <span class="text-[11px] text-stone-400 font-medium">(Harga Acuan &times; Qty)</span>
                                </div>
                                <div class="flex items-baseline justify-between gap-2">
                                    <strong id="estimasi-total" class="text-xl sm:text-2xl font-bold text-[#6B0F1A] tracking-tight">
                                        Estimasi Total: Rp {{ number_format($estimasiHarga, 0, ',', '.') }}
                                    </strong>
                                </div>
                                <p class="text-[10px] sm:text-[11px] text-stone-500 italic pt-1.5 border-t border-[#DE9DA7]/30">
                                    * Estimasi harga dapat berbeda setelah penjual mengonfirmasi detail desain.
                                </p>
                            </div>

                            {{-- Tombol Aksi Submit --}}
                            <div class="pt-1">
                                <button type="submit"
                                        class="w-full py-3.5 px-6 bg-[#6B0F1A] hover:bg-[#540F18] active:bg-[#3A0A0F] text-white font-bold text-sm sm:text-base rounded-full shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-[#6B0F1A]/30 text-center cursor-pointer flex items-center justify-center gap-2">
                                    <span>Lanjut ke Ringkasan Pesanan</span>
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    </main>

    <script>
        (() => {
            const baseProduct = document.getElementById('base_product_id');
            const categorySelect = document.getElementById('cake_category_id');
            const cakeOption = document.getElementById('cake_option_id');
            const cakeOptionEmpty = document.getElementById('cake-option-empty');
            const quantity = document.getElementById('jumlah');
            const estimate = document.getElementById('estimasi-total');
            const previewBox = document.getElementById('base-product-preview');
            const previewImg = document.getElementById('base-product-img');
            const previewFallback = document.getElementById('base-product-img-fallback');
            const previewNama = document.getElementById('base-product-nama');
            const previewHarga = document.getElementById('base-product-harga');
            const resetBtn = document.getElementById('base-product-reset');

            if (!baseProduct || !cakeOption || !quantity || !estimate) {
                return;
            }

            const parseInteger = (value) => {
                const parsed = Number.parseInt(value, 10);

                return Number.isNaN(parsed) ? 0 : parsed;
            };

            const maxQuantity = parseInteger(quantity.dataset.maxQuantity) || 5;
            const maxQuantityMessage =
                quantity.dataset.maxQuantityMessage ||
                `Maksimal pemesanan untuk pesanan ini adalah ${maxQuantity} pcs.`;

            const selectedPrice = (select) => {
                const option = select.selectedOptions[0];

                return option ? parseInteger(option.dataset.harga) : 0;
            };

            const formatRupiah = (value) => new Intl.NumberFormat('id-ID').format(value);

            const showToast = (message) => {
                let container = document.querySelector('[data-toast-container]');

                if (!container) {
                    container = document.createElement('div');
                    container.dataset.toastContainer = '';
                    container.style.cssText = 'position:fixed;top:1rem;right:1rem;z-index:9999;display:flex;flex-direction:column;align-items:flex-end;gap:.5rem;pointer-events:none;';
                    document.body.appendChild(container);
                }

                const toast = document.createElement('div');
                toast.setAttribute('role', 'alert');
                toast.setAttribute('aria-live', 'assertive');
                toast.textContent = message;
                toast.style.cssText = 'background:#b91c1c;color:#fff;padding:.75rem 1rem;border-radius:.5rem;box-shadow:0 10px 25px rgba(0,0,0,.18);font:14px/1.45 system-ui,sans-serif;max-width:24rem;pointer-events:auto;';
                container.appendChild(toast);

                window.setTimeout(() => toast.remove(), 5000);
            };

            const updateBaseProductPreview = () => {
                if (!previewBox) return;

                const selectedOption = baseProduct.selectedOptions[0];
                const productId = baseProduct.value;

                if (!productId || productId === '' || !selectedOption) {
                    previewBox.classList.add('hidden');
                    return;
                }

                const nama = selectedOption.dataset.nama || selectedOption.text.split(' - ')[0] || 'Produk Acuan';
                const hargaFormatted = selectedOption.dataset.hargaFormat || `Rp ${formatRupiah(selectedOption.dataset.harga || 0)}`;
                const foto = selectedOption.dataset.foto;

                if (previewNama) previewNama.textContent = nama;
                if (previewHarga) previewHarga.textContent = hargaFormatted;

                if (foto && foto.trim() !== '') {
                    if (previewImg) {
                        previewImg.src = foto;
                        previewImg.classList.remove('hidden');
                    }
                    if (previewFallback) previewFallback.classList.add('hidden');
                } else {
                    if (previewImg) previewImg.classList.add('hidden');
                    if (previewFallback) previewFallback.classList.remove('hidden');
                }

                previewBox.classList.remove('hidden');
            };

            const updateEstimate = () => {
                const basePrice = selectedPrice(baseProduct);
                const sizePrice = selectedPrice(cakeOption);
                const price = baseProduct.value !== '' ? basePrice : sizePrice;
                const quantityValue = parseInteger(quantity.value);
                const total = price * quantityValue;

                estimate.textContent = `Estimasi Total: Rp ${formatRupiah(total)}`;
                updateBaseProductPreview();
            };

            const handleQuantityChange = () => {
                if (parseInteger(quantity.value) > maxQuantity) {
                    quantity.value = String(maxQuantity);
                    showToast(maxQuantityMessage);
                }

                updateEstimate();
            };

            const allCakeOptions = Array.from(cakeOption.options).filter((option) => option.value !== '');

            const filterCakeOptions = () => {
                const selectedCategory = categorySelect ? categorySelect.value : '';
                const hasSpecificForCategory = selectedCategory !== '' && allCakeOptions.some(
                    (option) => (option.dataset.categoryId || '') === selectedCategory
                );

                let visibleCount = 0;

                allCakeOptions.forEach((option) => {
                    const optionCategory = option.dataset.categoryId || '';
                    let shouldShow = true;

                    if (selectedCategory !== '') {
                        shouldShow = hasSpecificForCategory
                            ? optionCategory === selectedCategory
                            : optionCategory === '';
                    }

                    option.hidden = !shouldShow;
                    option.disabled = !shouldShow;

                    if (shouldShow) {
                        visibleCount += 1;
                    }
                });

                const currentOption = cakeOption.selectedOptions[0];
                if (currentOption && currentOption.value !== '' && currentOption.hidden) {
                    cakeOption.value = '';
                }

                if (cakeOptionEmpty) {
                    cakeOptionEmpty.classList.toggle('hidden', visibleCount > 0);
                }

                updateEstimate();
            };

            if (resetBtn) {
                resetBtn.addEventListener('click', () => {
                    baseProduct.value = '';
                    updateEstimate();
                });
            }

            if (categorySelect) {
                categorySelect.addEventListener('change', filterCakeOptions);
            }

            baseProduct.addEventListener('change', () => {
                const selectedBaseOption = baseProduct.selectedOptions[0];
                const productCategoryId = selectedBaseOption ? (selectedBaseOption.dataset.categoryId || '') : '';

                if (categorySelect && productCategoryId !== '') {
                    const matchingCategory = Array.from(categorySelect.options).find(
                        (option) => option.value === productCategoryId
                    );

                    if (matchingCategory) {
                        categorySelect.value = productCategoryId;
                        filterCakeOptions();
                        return;
                    }
                }

                updateEstimate();
            });
            cakeOption.addEventListener('change', updateEstimate);

            ['input', 'change', 'keyup'].forEach((eventName) => {
                quantity.addEventListener(eventName, handleQuantityChange);
            });

            quantity.form?.addEventListener('submit', (event) => {
                if (parseInteger(quantity.value) > maxQuantity) {
                    quantity.value = String(maxQuantity);
                    showToast(maxQuantityMessage);
                    updateEstimate();
                    event.preventDefault();
                    quantity.focus();
                }
            });

            filterCakeOptions();
        })();
    </script>
</body>
</html>
