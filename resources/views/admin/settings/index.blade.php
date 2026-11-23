@extends('admin.layouts.app', ['title' => 'Pengaturan Toko', 'active' => 'pengaturan'])

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
@endpush

@section('content')
    <!-- Top Header & Breadcrumb -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-stone-500 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#6B0F1A] transition-colors">Beranda</a>
                <span>/</span>
                <span class="text-stone-800 font-semibold">Pengaturan Toko</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#5A121A] tracking-tight">
                Pengaturan Toko
            </h1>
            <p class="text-stone-500 text-xs sm:text-sm mt-1">
                Kelola logo, profil toko, kontak, rekening bank, dan barcode pembayaran QRIS Toko Kue Nak Mbarep.
            </p>
        </div>

        <!-- Quick Status Pills -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Bank Status Pill -->
            @if ($setting->hasBankDetails())
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/70 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Transfer Bank Aktif
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200/70 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    Transfer Bank Belum Lengkap
                </span>
            @endif

            <!-- QRIS Status Pill -->
            @if ($setting->hasQrisImage())
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/70 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    QRIS Aktif
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200/70 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    QRIS Belum Diunggah
                </span>
            @endif
        </div>
    </div>

    <!-- Flash Notifications -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs sm:text-sm flex items-start gap-3 shadow-xs animate-in fade-in duration-300">
            <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0 text-emerald-700 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-emerald-900">Berhasil Disimpan!</p>
                <p class="mt-0.5 text-emerald-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-800 text-xs sm:text-sm shadow-xs">
            <div class="flex items-center gap-2 font-bold text-rose-900 mb-2">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Mohon periksa kembali formulir berikut:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-rose-700 pl-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Settings Form -->
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- Left Column: Profil Toko & Rekening Bank (7 cols) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Card 1: Profil Toko -->
                <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 p-5 sm:p-7">
                    <div class="flex items-center gap-3 pb-4 mb-5 border-b border-stone-100">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center text-[#6B0F1A] shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-[#5A121A] tracking-tight">Profil Toko</h2>
                            <p class="text-xs text-stone-500">Informasi identitas toko yang dilihat oleh pelanggan</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Nama Toko -->
                        <div>
                            <label for="store_name" class="block text-xs font-bold text-stone-700 mb-1.5">
                                Nama Toko <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   id="store_name"
                                   name="store_name"
                                   value="{{ old('store_name', $setting->store_name) }}"
                                   placeholder="Contoh: Toko Kue Nak Mbarep"
                                   required
                                   class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                            <p class="text-[11px] text-stone-400 mt-1">Nama ini akan muncul di judul web, struk, dan pesan ke pelanggan.</p>
                        </div>

                        <!-- No HP / WhatsApp -->
                        <div>
                            <label for="store_phone" class="block text-xs font-bold text-stone-700 mb-1.5">
                                No HP / WA Toko
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <input type="tel"
                                       id="store_phone"
                                       name="store_phone"
                                       value="{{ old('store_phone', $setting->store_phone) }}"
                                       placeholder="08xxxxxxxxxx"
                                       class="w-full text-xs sm:text-sm pl-10 pr-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                            </div>
                            <p class="text-[11px] text-stone-400 mt-1">Nomor WhatsApp untuk konfirmasi pesanan dan pertanyaan pembeli.</p>
                        </div>

                        <!-- Alamat Toko -->
                        <div>
                            <label for="store_address" class="block text-xs font-bold text-stone-700 mb-1.5">
                                Alamat Toko
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <input type="text"
                                       id="store_address"
                                       name="store_address"
                                       value="{{ old('store_address', $setting->store_address) }}"
                                       placeholder="Contoh: Jl. Melati No. 12, Kelurahan X, Kota Y"
                                       class="w-full text-xs sm:text-sm pl-10 pr-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                            </div>
                        </div>

                        <!-- Deskripsi Toko -->
                        <div>
                            <label for="store_description" class="block text-xs font-bold text-stone-700 mb-1.5">
                                Deskripsi Toko
                            </label>
                            <textarea id="store_description"
                                      name="store_description"
                                      rows="3"
                                      placeholder="Ceritakan tentang toko kue, spesialisasi jenis kue, atau catatan penting untuk pelanggan..."
                                      class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">{{ old('store_description', $setting->store_description) }}</textarea>
                            <p class="text-[11px] text-stone-400 mt-1">Ditampilkan pada halaman profil toko dan footer katalog.</p>
                        </div>
                    </div>
                </div>

                <!-- Card: Kontak & Akun Media Sosial -->
                <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 p-5 sm:p-7">
                    <div class="flex items-center gap-3 pb-4 mb-5 border-b border-stone-100">
                        <div class="w-10 h-10 rounded-xl bg-pink-50 border border-pink-100 flex items-center justify-center text-[#6B0F1A] shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-[#5A121A] tracking-tight">Kontak & Akun Media Sosial</h2>
                            <p class="text-xs text-stone-500">Tautan akun media sosial toko yang muncul di footer web pembeli dan halaman Tentang Kami</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Email Toko -->
                        <div>
                            <label for="store_email" class="block text-xs font-bold text-stone-700 mb-1.5">
                                Alamat Email Toko / Customer Service
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <input type="email"
                                       id="store_email"
                                       name="store_email"
                                       value="{{ old('store_email', $setting->store_email) }}"
                                       placeholder="kontak@tokokuenakmbarep.com"
                                       class="w-full text-xs sm:text-sm pl-10 pr-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                            </div>
                        </div>

                        <!-- Instagram URL -->
                        <div>
                            <label for="instagram_url" class="block text-xs font-bold text-stone-700 mb-1.5 flex items-center gap-1.5">
                                <span class="text-pink-600">
                                    <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                </span>
                                <span>Link Akun Instagram</span>
                            </label>
                            <input type="url"
                                   id="instagram_url"
                                   name="instagram_url"
                                   value="{{ old('instagram_url', $setting->instagram_url) }}"
                                   placeholder="https://instagram.com/tokokuenakmbarep"
                                   class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                        </div>

                        <!-- TikTok URL -->
                        <div>
                            <label for="tiktok_url" class="block text-xs font-bold text-stone-700 mb-1.5 flex items-center gap-1.5">
                                <span class="text-stone-900">
                                    <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.24 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                                </span>
                                <span>Link Akun TikTok</span>
                            </label>
                            <input type="url"
                                   id="tiktok_url"
                                   name="tiktok_url"
                                   value="{{ old('tiktok_url', $setting->tiktok_url) }}"
                                   placeholder="https://tiktok.com/@tokokuenakmbarep"
                                   class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                        </div>

                        <!-- Facebook URL -->
                        <div>
                            <label for="facebook_url" class="block text-xs font-bold text-stone-700 mb-1.5 flex items-center gap-1.5">
                                <span class="text-blue-600">
                                    <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </span>
                                <span>Link Akun Facebook</span>
                            </label>
                            <input type="url"
                                   id="facebook_url"
                                   name="facebook_url"
                                   value="{{ old('facebook_url', $setting->facebook_url) }}"
                                   placeholder="https://facebook.com/tokokuenakmbarep"
                                   class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                        </div>
                    </div>
                </div>

                <!-- Card 2: Rekening Bank -->
                <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 p-5 sm:p-7">
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-stone-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-700 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-[#5A121A] tracking-tight">Rekening Bank (Transfer Bank)</h2>
                                <p class="text-xs text-stone-500">Tujuan transfer saat pembeli memilih metode transfer bank</p>
                            </div>
                        </div>

                        @if ($setting->hasBankDetails())
                            <span class="hidden sm:inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200">
                                ✓ Siap Pakai
                            </span>
                        @endif
                    </div>

                    <!-- Notice if incomplete -->
                    @unless ($setting->hasBankDetails())
                        <div class="mb-5 p-3.5 rounded-xl bg-amber-50/80 border border-amber-200/80 text-amber-800 text-xs flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p class="leading-relaxed">
                                Transfer Bank belum aktif di halaman pembeli sampai nama bank, nomor rekening, dan nama pemilik rekening diisi.
                            </p>
                        </div>
                    @endunless

                    <div class="space-y-4">
                        <!-- Nama Bank -->
                        <div>
                            <label for="bank_name" class="block text-xs font-bold text-stone-700 mb-1.5">
                                Nama Bank
                            </label>
                            <input type="text"
                                   id="bank_name"
                                   name="bank_name"
                                   value="{{ old('bank_name', $setting->bank_name) }}"
                                   placeholder="BNI / BCA / BRI / Mandiri"
                                   class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                        </div>

                        <!-- Grid 2: Nomor Rekening & Nama Pemilik -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="bank_account_number" class="block text-xs font-bold text-stone-700 mb-1.5">
                                    Nomor Rekening
                                </label>
                                <input type="text"
                                       id="bank_account_number"
                                       name="bank_account_number"
                                       value="{{ old('bank_account_number', $setting->bank_account_number) }}"
                                       placeholder="Contoh: 1234567890"
                                       class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all font-mono">
                            </div>

                            <div>
                                <label for="bank_account_holder" class="block text-xs font-bold text-stone-700 mb-1.5">
                                    Nama Pemilik Rekening
                                </label>
                                <input type="text"
                                       id="bank_account_holder"
                                       name="bank_account_holder"
                                       value="{{ old('bank_account_holder', $setting->bank_account_holder) }}"
                                       placeholder="Contoh: Toko Kue Nak Mbarep"
                                       class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Logo, QRIS & Quick Links (5 cols) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- Card: Logo Toko -->
                <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 p-5 sm:p-7">
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-stone-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center text-[#6B0F1A] shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-[#5A121A] tracking-tight">Logo Toko</h2>
                                <p class="text-xs text-stone-500">Tampil di navbar pembeli serta top bar panel admin & staf</p>
                            </div>
                        </div>

                        @if ($setting->hasLogo())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200">
                                Kustom
                            </span>
                        @endif
                    </div>

                    <!-- Current Logo Display -->
                    <div class="mb-5 p-4 rounded-xl bg-stone-50 border border-stone-200/80 text-center">
                        <p class="text-xs font-semibold text-stone-700 mb-3">
                            {{ $setting->hasLogo() ? 'Logo Saat Ini:' : 'Logo Bawaan (Belum Ada Logo Kustom):' }}
                        </p>
                        <div class="inline-block bg-white p-3 rounded-full border border-stone-200 shadow-sm">
                            <img id="logoPreviewImg"
                                 src="{{ $setting->logoUrl() }}"
                                 alt="Logo {{ $setting->store_name ?: 'Toko' }}"
                                 class="w-20 h-20 rounded-full object-cover">
                        </div>

                        @if ($setting->hasLogo())
                            <div class="mt-4 pt-3 border-t border-stone-200/80">
                                <label class="inline-flex items-center gap-2 text-xs font-semibold text-rose-600 hover:text-rose-700 cursor-pointer">
                                    <input type="checkbox"
                                           name="hapus_logo"
                                           value="1"
                                           class="rounded border-stone-300 text-rose-600 focus:ring-rose-500">
                                    <span>Hapus logo dan gunakan logo bawaan</span>
                                </label>
                            </div>
                        @endif
                    </div>

                    <!-- Upload Input Area -->
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <label for="logo" class="block text-xs font-bold text-stone-700">
                                {{ $setting->hasLogo() ? 'Ganti Logo Toko' : 'Unggah Logo Toko' }}
                            </label>
                            <button type="button"
                                    id="logoCropBtn"
                                    class="{{ $setting->hasLogo() ? '' : 'hidden' }} inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-semibold rounded-lg border border-[#6B0F1A]/20 bg-rose-50 hover:bg-rose-100 text-[#6B0F1A] cursor-pointer shadow-xs transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3v14a2 2 0 002 2h14M17 21v-4a2 2 0 00-2-2H3"/>
                                </svg>
                                <span>Atur Posisi & Crop</span>
                            </button>
                        </div>

                        <div class="relative border-2 border-dashed border-stone-200 hover:border-[#6B0F1A]/40 rounded-xl p-4 text-center transition-colors bg-stone-50/50">
                            <input type="file"
                                   id="logo"
                                   name="logo"
                                   accept="image/jpeg,image/png,image/webp"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <input type="hidden"
                                   name="cropped_logo_base64"
                                   id="logoCroppedBase64">

                            <div class="space-y-1">
                                <div class="w-10 h-10 rounded-full bg-rose-50 text-[#6B0F1A] mx-auto flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold text-stone-700">
                                    Klik atau seret file gambar ke sini
                                </p>
                                <p class="text-[11px] text-stone-400">
                                    Format JPG, PNG, atau WEBP. Maksimal 2 MB. Setelah memilih file, jendela atur posisi & crop 1:1 akan terbuka otomatis.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Multi-Banner Toko (Beranda) -->
                <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 p-5 sm:p-7">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 mb-5 border-b border-stone-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center text-[#6B0F1A] shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-[#5A121A] tracking-tight">Multi-Banner Toko (Beranda)</h2>
                                <p class="text-xs text-stone-500">Unggah banner toko sebanyak apa pun, atur urutan tampilan, dan timer geser otomatis</p>
                            </div>
                        </div>

                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold border shrink-0 w-fit {{ $banners->isNotEmpty() ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-stone-100 text-stone-600 border-stone-200' }}">
                            <span class="w-2 h-2 rounded-full {{ $banners->isNotEmpty() ? 'bg-emerald-500 animate-pulse' : 'bg-stone-400' }}"></span>
                            {{ $banners->count() }} Banner Aktif
                        </span>
                    </div>

                    <!-- Live Carousel Preview -->
                    <div class="mb-6 p-4 rounded-xl bg-stone-50 border border-stone-200/80">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1.5 sm:gap-2 mb-3">
                            <p class="text-xs font-semibold text-stone-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#6B0F1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Pratinjau Tampilan Beranda Pembeli:
                            </p>
                            <span id="bannerCanvasBadge" class="text-[10px] font-semibold text-stone-500 bg-white px-2.5 py-0.5 rounded-full border border-stone-200 w-fit">
                                {{ $setting->banner_size ?? '21:9' }} · {{ $setting->banner_scale ?? 'cover' }} · Geser per {{ $setting->banner_slide_duration ?? 5 }} Detik
                            </span>
                        </div>
                        <div id="bannerPreviewWrapper" class="w-full">
                            <x-banner-carousel :banners="$banners" :setting="$setting" preview-id="adminSettingsBannerPreview" />
                        </div>
                    </div>

                    <!-- Daftar Multi-Banner & Pengaturan Urutan -->
                    <div class="mb-6 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 sm:gap-2">
                            <h3 class="text-xs font-bold text-stone-800 uppercase tracking-wider flex flex-wrap items-center gap-1.5">
                                <span>Daftar Urutan Banner</span>
                                <span class="text-stone-400 normal-case font-normal text-[11px] sm:text-xs">(Tahan &amp; geser untuk atur urutan)</span>
                            </h3>
                            <div class="flex items-center gap-2">
                                <span id="bannerReorderStatus" class="hidden text-[11px] font-medium text-emerald-600 transition-opacity">
                                    Urutan tersimpan ✓
                                </span>
                                @if ($banners->isNotEmpty())
                                    <span class="text-[10px] sm:text-[11px] text-stone-500">Urutan paling atas tampil pertama</span>
                                @endif
                            </div>
                        </div>

                        @if ($banners->isNotEmpty())
                            <div id="bannerListContainer" class="space-y-3">
                                @foreach ($banners as $index => $b)
                                    <div class="banner-item p-3.5 sm:p-4 rounded-xl sm:rounded-2xl border border-stone-200 bg-white shadow-2xs hover:border-[#6B0F1A]/40 transition-all space-y-2.5 min-w-0 max-w-full relative"
                                         data-id="{{ $b->id }}">
                                        <input type="hidden" name="banner_orders[{{ $b->id }}]" class="banner-order-input" value="{{ $index + 1 }}">

                                        <!-- Header Baris Banner: Badge Urutan & Judul -->
                                        <div class="flex items-center justify-between gap-2 min-w-0 max-w-full">
                                            <div class="flex items-center gap-2 flex-wrap min-w-0">
                                                <span class="banner-number-badge w-6 h-6 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-2xs">
                                                    {{ $index + 1 }}
                                                </span>
                                                <span class="banner-title-text text-xs font-bold text-stone-800 truncate">
                                                    Banner ke-{{ $index + 1 }}
                                                </span>
                                                <span class="banner-order-badge text-[10px] text-stone-500 bg-stone-100 px-2 py-0.5 rounded-full font-medium shrink-0">
                                                    Urutan #{{ $b->urutan }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Preview Gambar Banner Sesuai Rasio Canvas -->
                                        <div class="w-full {{ $setting->bannerAspectRatioClass() }} rounded-xl overflow-hidden border border-stone-200 bg-stone-100 relative">
                                            <img src="{{ $b->gambar_url }}"
                                                 alt="Banner ke-{{ $index + 1 }}"
                                                 class="w-full h-full {{ $setting->bannerScaleClass() }}">
                                        </div>

                                        <!-- Kontrol Urutan / Aksi di Kanan Bawah Blok Banner -->
                                        <div class="flex justify-end items-center gap-2 mt-2 pt-2 border-t border-stone-100 min-w-0 max-w-full">
                                            <!-- Drag Handle Geser Urutan -->
                                            <div role="button"
                                                 tabindex="0"
                                                 class="banner-drag-handle inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-stone-200 bg-stone-50 hover:bg-stone-100 active:bg-stone-200 text-stone-700 text-xs font-semibold cursor-grab active:cursor-grabbing select-none transition shadow-2xs"
                                                 style="touch-action: none;"
                                                 title="Tahan dan geser untuk mengatur urutan"
                                                 aria-label="Tahan dan geser untuk mengatur urutan banner ke-{{ $index + 1 }}">
                                                <svg class="w-3.5 h-3.5 text-stone-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                                </svg>
                                                <span class="text-[11px]">Tahan &amp; Geser</span>
                                            </div>

                                            <!-- Tombol Hapus Banner -->
                                            <button type="submit"
                                                    form="deleteBanner-{{ $b->id }}"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus banner ini?');"
                                                    title="Hapus banner ini"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold transition cursor-pointer shadow-2xs active:bg-rose-200">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span class="text-[11px]">Hapus</span>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-6 rounded-xl border border-dashed border-stone-200 text-center bg-stone-50/50">
                                <p class="text-xs font-medium text-stone-500">
                                    Belum ada banner di daftar. Silakan unggah gambar di bawah untuk menambahkan banner toko.
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Area Unggah Banner Baru -->
                    <div class="space-y-4 pt-4 border-t border-stone-100">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <label for="banner" class="block text-xs font-bold text-stone-800">
                                    + Tambah Banner Baru
                                </label>
                                <p class="text-[11px] text-stone-500">Unggah foto banner baru dan potong presisi sesuai canvas pembeli</p>
                            </div>
                            <button type="button"
                                    id="bannerCropBtn"
                                    class="hidden inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-semibold rounded-lg border border-[#6B0F1A]/20 bg-rose-50 hover:bg-rose-100 text-[#6B0F1A] cursor-pointer shadow-xs transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3v14a2 2 0 002 2h14M17 21v-4a2 2 0 00-2-2H3"/>
                                </svg>
                                <span>Atur Posisi & Scale Banner</span>
                            </button>
                        </div>

                        <div class="relative border-2 border-dashed border-stone-200 hover:border-[#6B0F1A]/40 rounded-xl p-4 text-center transition-colors bg-stone-50/50">
                            <input type="file"
                                   id="banner"
                                   name="banner"
                                   accept="image/jpeg,image/png,image/webp"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <input type="hidden"
                                   name="cropped_banner_base64"
                                   id="bannerCroppedBase64">

                            <div class="space-y-1">
                                <div class="w-10 h-10 rounded-full bg-rose-50 text-[#6B0F1A] mx-auto flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold text-stone-700">
                                    Klik atau seret file banner ke sini
                                </p>
                                <p class="text-[11px] text-stone-400">
                                    Format JPG, PNG, atau WEBP. Maksimal 4 MB. Jendela atur posisi & scale (canvas 21:9) akan terbuka otomatis.
                                </p>
                            </div>
                        </div>

                        <!-- Mini Preview Gambar yang Baru Dipilih Sebelum Disimpan -->
                        <div id="newBannerPendingContainer" class="hidden p-3 rounded-xl border border-emerald-200 bg-emerald-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-20 {{ $setting->bannerAspectRatioClass() }} rounded-lg overflow-hidden border border-emerald-200 bg-white shrink-0">
                                    <img id="bannerPreviewImg" src="" alt="Pratinjau Baru" class="w-full h-full {{ $setting->bannerScaleClass() }}">
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-emerald-800">Banner Baru Siap Ditambahkan!</p>
                                    <p class="text-[10px] text-emerald-600">Klik "Simpan Pengaturan" di bawah untuk menyimpan banner ini ke dalam daftar.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pengaturan Skala Tampilan & Ukuran Banner -->
                        <div class="pt-4 border-t border-stone-100 space-y-4">
                            <!-- Ukuran Canvas (Aspek Rasio) -->
                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-2">
                                    Ukuran / Aspek Rasio Canvas Banner
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    @php
                                        $selectedSize = old('banner_size', $setting->banner_size ?? '21:9');
                                        $sizeOptions = [
                                            ['value' => '21:9', 'label' => '21:9 (Ultra-wide)', 'hint' => 'Rekomendasi'],
                                            ['value' => '16:9', 'label' => '16:9 (Standar)', 'hint' => 'Landscape'],
                                            ['value' => '16:7', 'label' => '16:7 (Wide)', 'hint' => 'Proporsional'],
                                            ['value' => '4:1', 'label' => '4:1 (Slim Ribbon)', 'hint' => 'Ramping'],
                                        ];
                                    @endphp
                                    @foreach ($sizeOptions as $opt)
                                        <label class="flex flex-col p-2.5 rounded-xl border border-stone-200 bg-white hover:bg-stone-50/80 cursor-pointer transition has-[:checked]:border-[#6B0F1A] has-[:checked]:bg-rose-50/40">
                                            <div class="flex items-center justify-between mb-1">
                                                <input type="radio"
                                                       name="banner_size"
                                                       value="{{ $opt['value'] }}"
                                                       {{ $selectedSize === $opt['value'] ? 'checked' : '' }}
                                                       class="text-[#6B0F1A] focus:ring-[#6B0F1A] border-stone-300">
                                                <span class="text-[9px] font-medium text-stone-400">{{ $opt['hint'] }}</span>
                                            </div>
                                            <span class="text-xs font-semibold text-stone-800">{{ $opt['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Skala Gambar (Object Fit) -->
                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-2">
                                    Skala Tampilan Gambar (Object Fit)
                                </label>
                                <div class="grid grid-cols-3 gap-2">
                                    @php
                                        $selectedScale = old('banner_scale', $setting->banner_scale ?? 'cover');
                                        $scaleOptions = [
                                            ['value' => 'cover', 'label' => 'Cover (Penuh)', 'desc' => 'Crop proporsional tanpa ruang kosong'],
                                            ['value' => 'contain', 'label' => 'Contain (Muat Utuh)', 'desc' => 'Seluruh gambar tampil tanpa terpotong'],
                                            ['value' => 'fill', 'label' => 'Fill (Regang)', 'desc' => 'Rentangkan gambar penuhi canvas'],
                                        ];
                                    @endphp
                                    @foreach ($scaleOptions as $opt)
                                        <label class="flex flex-col p-2.5 rounded-xl border border-stone-200 bg-white hover:bg-stone-50/80 cursor-pointer transition has-[:checked]:border-[#6B0F1A] has-[:checked]:bg-rose-50/40">
                                            <div class="flex items-center gap-2 mb-1">
                                                <input type="radio"
                                                       name="banner_scale"
                                                       value="{{ $opt['value'] }}"
                                                       {{ $selectedScale === $opt['value'] ? 'checked' : '' }}
                                                       class="text-[#6B0F1A] focus:ring-[#6B0F1A] border-stone-300">
                                                <span class="text-xs font-semibold text-stone-800">{{ $opt['label'] }}</span>
                                            </div>
                                            <span class="text-[10px] text-stone-500 leading-tight">{{ $opt['desc'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Durasi Geser / Auto Slide -->
                            <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                                <div class="flex items-center gap-2">
                                    <label for="banner_slide_duration" class="text-xs font-bold text-stone-700 whitespace-nowrap">
                                        Waktu Geser (Slide Timer):
                                    </label>
                                    <div class="flex items-center gap-1.5">
                                        <input type="number"
                                               id="banner_slide_duration"
                                               name="banner_slide_duration"
                                               min="1"
                                               max="60"
                                               value="{{ old('banner_slide_duration', $setting->banner_slide_duration ?? 5) }}"
                                               class="w-16 px-2.5 py-1 text-xs sm:text-sm font-semibold rounded-lg border border-stone-200 text-center focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A]">
                                        <span class="text-xs text-stone-500 font-medium">detik</span>
                                    </div>
                                </div>

                                <label class="inline-flex items-center gap-2 text-xs font-semibold text-stone-700 cursor-pointer">
                                    <input type="checkbox"
                                           name="banner_auto_slide"
                                           value="1"
                                           {{ old('banner_auto_slide', $setting->banner_auto_slide ?? true) ? 'checked' : '' }}
                                           class="rounded border-stone-300 text-[#6B0F1A] focus:ring-[#6B0F1A]">
                                    <span>Aktifkan geser otomatis (auto-slide)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: QRIS -->
                <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 p-5 sm:p-7">
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-stone-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-700 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-[#5A121A] tracking-tight">QRIS</h2>
                                <p class="text-xs text-stone-500">Barcode QRIS untuk pembayaran digital</p>
                            </div>
                        </div>

                        @if ($setting->hasQrisImage())
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200">
                                Aktif
                            </span>
                        @endif
                    </div>

                    <!-- QRIS Current Image Display -->
                    @if ($setting->hasQrisImage())
                        <div class="mb-5 p-4 rounded-xl bg-stone-50 border border-stone-200/80 text-center">
                            <p class="text-xs font-semibold text-stone-700 mb-3">Gambar QRIS Saat Ini:</p>
                            <div class="inline-block bg-white p-3 rounded-xl border border-stone-200 shadow-sm">
                                <img src="{{ asset('storage/'.$setting->qris_image_path) }}"
                                     alt="QRIS {{ $setting->store_name }}"
                                     class="max-w-[200px] h-auto mx-auto object-contain rounded-lg">
                            </div>

                            <div class="mt-4 pt-3 border-t border-stone-200/80">
                                <label class="inline-flex items-center gap-2 text-xs font-semibold text-rose-600 hover:text-rose-700 cursor-pointer">
                                    <input type="checkbox"
                                           name="hapus_qris"
                                           value="1"
                                           class="rounded border-stone-300 text-rose-600 focus:ring-rose-500">
                                    <span>Hapus gambar QRIS saat menyimpan</span>
                                </label>
                            </div>
                        </div>
                    @else
                        <div class="mb-5 p-3.5 rounded-xl bg-amber-50/80 border border-amber-200/80 text-amber-800 text-xs flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="leading-relaxed">
                                QRIS belum aktif di halaman pembeli sampai gambar QRIS diunggah.
                            </p>
                        </div>
                    @endif

                    <!-- Upload Input Area -->
                    <div class="space-y-3">
                        <label for="qris_image" class="block text-xs font-bold text-stone-700">
                            {{ $setting->hasQrisImage() ? 'Ganti Gambar QRIS' : 'Gambar QRIS' }}
                        </label>

                        <div class="relative border-2 border-dashed border-stone-200 hover:border-[#6B0F1A]/40 rounded-xl p-4 text-center transition-colors bg-stone-50/50">
                            <input type="file"
                                   id="qris_image"
                                   name="qris_image"
                                   accept="image/jpeg,image/png,image/webp"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                   onchange="previewQrisImage(event)">

                            <div id="qrisUploadPrompt" class="space-y-1">
                                <div class="w-10 h-10 rounded-full bg-rose-50 text-[#6B0F1A] mx-auto flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <p class="text-xs font-semibold text-stone-700">
                                    Klik atau seret file gambar ke sini
                                </p>
                                <p class="text-[11px] text-stone-400">
                                    Format JPG, PNG, atau WEBP. Maksimal 2 MB.
                                </p>
                            </div>

                            <!-- Live preview of newly chosen file -->
                            <div id="qrisNewPreviewContainer" class="hidden space-y-2">
                                <p class="text-[11px] font-semibold text-emerald-700">Pratinjau File Baru Terpilih:</p>
                                <img id="qrisNewPreview" src="#" alt="Pratinjau QRIS Baru" class="max-w-[160px] h-auto mx-auto rounded-lg border border-stone-200 shadow-xs">
                                <p id="qrisNewFileName" class="text-[11px] text-stone-500 font-mono truncate"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Tautan Terkait -->
                <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 p-5 sm:p-6">
                    <h3 class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-3">
                        Pengaturan Terkait Lainnya
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.pengaturan.index') }}"
                           class="flex items-center justify-between p-3 rounded-xl bg-stone-50 hover:bg-rose-50/50 hover:text-[#6B0F1A] border border-stone-100 text-xs font-semibold text-stone-700 transition-colors">
                            <span class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-[#6B0F1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                                <span>Pengaturan Custom Cake</span>
                            </span>
                            <span class="text-stone-400 text-sm">→</span>
                        </a>

                        <a href="{{ route('admin.users.index') }}"
                           class="flex items-center justify-between p-3 rounded-xl bg-stone-50 hover:bg-rose-50/50 hover:text-[#6B0F1A] border border-stone-100 text-xs font-semibold text-stone-700 transition-colors">
                            <span class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-[#6B0F1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span>Kelola User & Staf</span>
                            </span>
                            <span class="text-stone-400 text-sm">→</span>
                        </a>

                        <a href="{{ route('customer.index') }}" target="_blank"
                           class="flex items-center justify-between p-3 rounded-xl bg-stone-50 hover:bg-rose-50/50 hover:text-[#6B0F1A] border border-stone-100 text-xs font-semibold text-stone-700 transition-colors">
                            <span class="flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-[#6B0F1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                <span>Lihat Tampilan Web Toko</span>
                            </span>
                            <span class="text-stone-400 text-sm">↗</span>
                        </a>
                    </div>
                </div>

            </div>

        </div>

        <!-- Sticky Bottom Action Bar -->
        <div class="mt-8 pt-4 border-t border-stone-200/80 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-stone-500 text-center sm:text-left">
                Perubahan pada pengaturan akan langsung diterapkan ke seluruh halaman toko.
            </p>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex-1 sm:flex-none px-5 py-2.5 rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 text-xs sm:text-sm font-semibold transition-colors text-center shadow-xs">
                    Batal
                </a>

                <button type="submit"
                        class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl bg-[#6B0F1A] hover:bg-[#520A13] text-white text-xs sm:text-sm font-semibold shadow-xs transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Simpan Pengaturan</span>
                </button>
            </div>
        </div>
    </form>

    {{-- Hidden Helper Forms for Multi-Banner Operations (Move Up / Down / Delete) --}}
    @foreach ($banners as $b)
        <form id="moveUpBanner-{{ $b->id }}" action="{{ route('admin.settings.banners.move-up', $b->id) }}" method="POST" class="hidden">
            @csrf
        </form>
        <form id="moveDownBanner-{{ $b->id }}" action="{{ route('admin.settings.banners.move-down', $b->id) }}" method="POST" class="hidden">
            @csrf
        </form>
        <form id="deleteBanner-{{ $b->id }}" action="{{ route('admin.settings.banners.destroy', $b->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    <!-- Script for Live QRIS & Banner Preview -->
    <script>
        function previewQrisImage(event) {
            const input = event.target;
            const prompt = document.getElementById('qrisUploadPrompt');
            const container = document.getElementById('qrisNewPreviewContainer');
            const preview = document.getElementById('qrisNewPreview');
            const fileName = document.getElementById('qrisNewFileName');

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    fileName.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                    prompt.classList.add('hidden');
                    container.classList.remove('hidden');
                };

                reader.readAsDataURL(file);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const wrapper = document.getElementById('bannerPreviewWrapper');
            const img = document.getElementById('bannerPreviewImg');
            const badge = document.getElementById('bannerCanvasBadge');
            const emptyLabel = document.getElementById('emptyStateRatioLabel');

            const sizeRadios = document.querySelectorAll('input[name="banner_size"]');
            const scaleRadios = document.querySelectorAll('input[name="banner_scale"]');

            const aspectClassMap = {
                '21:9': 'aspect-[21/9]',
                '16:9': 'aspect-[16/9]',
                '16:7': 'aspect-[16/7]',
                '4:1': 'aspect-[4/1]',
            };

            const scaleClassMap = {
                'cover': 'object-cover',
                'contain': 'object-contain',
                'fill': 'object-fill',
            };

            function updateLiveBannerPreview() {
                const checkedSize = document.querySelector('input[name="banner_size"]:checked')?.value || '21:9';
                const checkedScale = document.querySelector('input[name="banner_scale"]:checked')?.value || 'cover';

                if (wrapper) {
                    Object.values(aspectClassMap).forEach(cls => wrapper.classList.remove(cls));
                    wrapper.classList.add(aspectClassMap[checkedSize] || 'aspect-[21/9]');
                }

                if (img) {
                    Object.values(scaleClassMap).forEach(cls => img.classList.remove(cls));
                    img.classList.add(scaleClassMap[checkedScale] || 'object-cover');
                }

                if (badge) {
                    badge.textContent = `${checkedSize} · ${checkedScale}`;
                }

                if (emptyLabel) {
                    emptyLabel.textContent = checkedSize;
                }
            }

            sizeRadios.forEach(radio => radio.addEventListener('change', updateLiveBannerPreview));
            scaleRadios.forEach(radio => radio.addEventListener('change', updateLiveBannerPreview));

            // Inisialisasi Drag and Drop untuk Daftar Urutan Banner
            function initBannerDragAndDrop() {
                const container = document.getElementById('bannerListContainer');
                if (!container) return;

                const handles = container.querySelectorAll('.banner-drag-handle');
                if (!handles.length) return;

                let isDragging = false;
                let draggedItem = null;
                let initialOrder = [];
                let activePointerId = null;

                function getBannerIds() {
                    return Array.from(container.querySelectorAll('.banner-item')).map(el => el.dataset.id);
                }

                function updateBannerNumbering() {
                    const items = container.querySelectorAll('.banner-item');
                    items.forEach((item, index) => {
                        const num = index + 1;
                        const numBadge = item.querySelector('.banner-number-badge');
                        const titleText = item.querySelector('.banner-title-text');
                        const orderBadge = item.querySelector('.banner-order-badge');
                        const orderInput = item.querySelector('.banner-order-input');

                        if (numBadge) numBadge.textContent = num;
                        if (titleText) titleText.textContent = `Banner ke-${num}`;
                        if (orderBadge) orderBadge.textContent = `Urutan #${num}`;
                        if (orderInput) orderInput.value = num;
                    });
                }

                function revertBannerOrder(order) {
                    if (!container || !order) return;
                    order.forEach(id => {
                        const item = container.querySelector(`.banner-item[data-id="${id}"]`);
                        if (item) container.appendChild(item);
                    });
                }

                function displayToast(message, type = 'success') {
                    if (typeof window.showToast === 'function') {
                        window.showToast(message, type);
                        return;
                    }

                    const toast = document.createElement('div');
                    toast.className = `fixed top-4 right-4 z-50 px-4 py-2.5 rounded-xl shadow-lg text-xs font-semibold text-white transition-all transform duration-300 flex items-center gap-2 ${
                        type === 'success' ? 'bg-emerald-600' : 'bg-rose-600'
                    }`;
                    toast.textContent = message;
                    document.body.appendChild(toast);
                    setTimeout(() => {
                        toast.style.opacity = '0';
                        setTimeout(() => toast.remove(), 300);
                    }, 3000);
                }

                async function saveBannerOrder(newOrder, prevOrder) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                        || document.querySelector('input[name="_token"]')?.value;
                    const reorderUrl = "{{ route('admin.settings.banners.reorder') }}";
                    const statusEl = document.getElementById('bannerReorderStatus');

                    if (statusEl) {
                        statusEl.textContent = 'Menyimpan urutan...';
                        statusEl.className = 'text-[11px] font-medium text-stone-500 animate-pulse';
                        statusEl.classList.remove('hidden');
                    }

                    try {
                        const response = await fetch(reorderUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                orders: newOrder.map(Number),
                            }),
                        });

                        const data = await response.json().catch(() => null);

                        if (!response.ok || !data?.success) {
                            throw new Error(data?.message || 'Gagal menyimpan urutan banner.');
                        }

                        if (statusEl) {
                            statusEl.textContent = 'Urutan tersimpan ✓';
                            statusEl.className = 'text-[11px] font-medium text-emerald-600 transition-opacity';
                            setTimeout(() => {
                                statusEl.classList.add('hidden');
                            }, 3000);
                        }

                        displayToast(data.message || 'Urutan banner berhasil disimpan.', 'success');
                    } catch (err) {
                        console.error('Reorder error:', err);
                        if (statusEl) {
                            statusEl.textContent = 'Gagal menyimpan urutan ✕';
                            statusEl.className = 'text-[11px] font-medium text-rose-600';
                            setTimeout(() => {
                                statusEl.classList.add('hidden');
                            }, 4000);
                        }

                        displayToast(err.message || 'Gagal menyimpan urutan banner. Silakan coba lagi.', 'error');
                        revertBannerOrder(prevOrder);
                        updateBannerNumbering();
                    }
                }

                handles.forEach(handle => {
                    handle.addEventListener('pointerdown', function (e) {
                        if (e.pointerType === 'mouse' && e.button !== 0) return;
                        const item = handle.closest('.banner-item');
                        if (!item) return;

                        isDragging = true;
                        draggedItem = item;
                        initialOrder = getBannerIds();
                        activePointerId = e.pointerId;

                        try {
                            handle.setPointerCapture(e.pointerId);
                        } catch (_) {}

                        draggedItem.classList.add('is-dragging', 'shadow-lg', 'border-[#6B0F1A]', 'ring-2', 'ring-[#6B0F1A]/30', 'scale-[1.01]', 'z-30');
                        draggedItem.style.transition = 'none';
                        document.body.classList.add('select-none');
                    });
                });

                function handlePointerMove(e) {
                    if (!isDragging || !draggedItem) return;

                    const siblings = [...container.querySelectorAll('.banner-item:not(.is-dragging)')];
                    const nextSibling = siblings.find(sibling => {
                        const rect = sibling.getBoundingClientRect();
                        return e.clientY < rect.top + rect.height / 2;
                    });

                    if (nextSibling) {
                        container.insertBefore(draggedItem, nextSibling);
                    } else {
                        container.appendChild(draggedItem);
                    }
                }

                function handlePointerEnd(e) {
                    if (!isDragging || !draggedItem) return;

                    if (activePointerId !== null) {
                        const handle = draggedItem.querySelector('.banner-drag-handle');
                        if (handle && handle.hasPointerCapture(activePointerId)) {
                            try {
                                handle.releasePointerCapture(activePointerId);
                            } catch (_) {}
                        }
                    }

                    draggedItem.classList.remove('is-dragging', 'shadow-lg', 'border-[#6B0F1A]', 'ring-2', 'ring-[#6B0F1A]/30', 'scale-[1.01]', 'z-30');
                    draggedItem.style.transition = '';
                    document.body.classList.remove('select-none');

                    const newOrder = getBannerIds();
                    isDragging = false;
                    draggedItem = null;
                    activePointerId = null;

                    if (JSON.stringify(initialOrder) !== JSON.stringify(newOrder)) {
                        updateBannerNumbering();
                        saveBannerOrder(newOrder, initialOrder);
                    }
                }

                window.addEventListener('pointermove', handlePointerMove);
                window.addEventListener('pointerup', handlePointerEnd);
                window.addEventListener('pointercancel', handlePointerEnd);
            }

            initBannerDragAndDrop();
        });
    </script>
@endsection
