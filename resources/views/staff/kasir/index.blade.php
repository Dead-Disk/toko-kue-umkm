@extends('staff.layouts.app', ['title' => 'Kasir / POS Offline', 'active' => 'kasir'])

@push('styles')
    <style>
        #struk {
            display: none;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        }
        #struk.is-visible {
            display: block;
        }
        @media print {
            aside, header, #mobileSidebarDrawer, #mobileSidebarBackdrop {
                display: none !important;
            }
            main {
                padding: 0 !important;
                margin: 0 !important;
                overflow: visible !important;
            }
            body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
                color: #000 !important;
            }
            .no-print {
                display: none !important;
            }
            #struk {
                display: block !important;
                box-shadow: none !important;
                border: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
                margin: 0 !important;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Top Header & Breadcrumb -->
    <div class="no-print mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-stone-500 mb-1">
                <a href="{{ route('staff.dashboard') }}" class="hover:text-[#6B0F1A] transition-colors">Beranda</a>
                <span>/</span>
                <span class="text-stone-800 font-semibold">Kasir / POS</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#5A121A] tracking-tight">
                Kasir / POS Offline
            </h1>
            <p class="text-stone-500 text-xs sm:text-sm mt-1">
                Pencatatan transaksi tunai langsung di toko secara cepat.
            </p>
        </div>

        {{-- Navigasi Quick Links Pills --}}
        <nav class="flex flex-wrap items-center gap-2">
            <a href="{{ route('staff.dashboard') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-white text-stone-600 border border-stone-200 hover:text-[#6B0F1A] hover:border-[#6B0F1A]/40 transition shadow-2xs">
                <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('staff.pesanan.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-white text-stone-600 border border-stone-200 hover:text-[#6B0F1A] hover:border-[#6B0F1A]/40 transition shadow-2xs">
                <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span>Pesanan Masuk</span>
            </a>

            <a href="{{ route('staff.produk.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-white text-stone-600 border border-stone-200 hover:text-[#6B0F1A] hover:border-[#6B0F1A]/40 transition shadow-2xs">
                <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span>Daftar Produk</span>
            </a>
        </nav>
    </div>

    {{-- Toast alerts --}}
    @if (session('success'))
        <div data-toast-message="{{ session('success') }}" data-toast-type="success" hidden></div>
        <div class="no-print mb-6 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs sm:text-sm flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div data-toast-message="{{ session('error') }}" data-toast-type="error" hidden></div>
        <div class="no-print mb-6 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs sm:text-sm flex items-center gap-2 shadow-2xs">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Main Layout Grid --}}
    <div class="no-print grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- Form Transaksi Kasir (Main Container Card) --}}
        <section class="lg:col-span-7 bg-white rounded-2xl shadow-md border border-stone-200/70 p-5 sm:p-6 space-y-5">
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <h2 class="text-base sm:text-lg font-bold text-stone-800 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-xl bg-[#6B0F1A] text-white flex items-center justify-center text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </span>
                    Transaksi Penjualan Tunai
                </h2>
            </div>

            @if ($products->isEmpty())
                <div class="p-6 text-center text-stone-500 bg-stone-50 rounded-xl border border-dashed border-stone-200">
                    <p class="text-sm">Belum ada produk dengan stok tersedia. Tambahkan stok produk terlebih dahulu.</p>
                    <a href="{{ route('staff.produk.index') }}" class="inline-block mt-3 text-xs font-semibold text-[#6B0F1A] hover:underline">
                        Lihat Stok Produk →
                    </a>
                </div>
            @else
                @php
                    $oldItems = old('items');

                    if (! is_array($oldItems) || $oldItems === []) {
                        $oldItems = [[
                            'product_id' => old('product_id'),
                            'qty' => old('jumlah', 1),
                        ]];
                    }
                @endphp

                <form action="{{ route('staff.kasir.store') }}" method="POST" data-cashier-pos class="space-y-5">
                    @csrf

                    @error('items')
                        <p class="text-xs font-semibold text-rose-600 bg-rose-50 p-2.5 rounded-lg border border-rose-200">{{ $message }}</p>
                    @enderror

                    @error('items.*')
                        <p class="text-xs font-semibold text-rose-600 bg-rose-50 p-2.5 rounded-lg border border-rose-200">{{ $message }}</p>
                    @enderror

                    {{-- Table of Cart Items --}}
                    <div class="overflow-x-auto rounded-xl border border-stone-200 bg-stone-50/30">
                        <table class="w-full text-left text-xs sm:text-sm">
                            <thead class="bg-stone-100/70 text-stone-600 uppercase text-[11px] font-semibold border-b border-stone-200">
                                <tr>
                                    <th class="py-2.5 px-3">Produk</th>
                                    <th class="py-2.5 px-3 w-20 text-center">Qty</th>
                                    <th class="py-2.5 px-3 w-28 text-right">Subtotal</th>
                                    <th class="py-2.5 px-3 w-12 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody data-cashier-rows class="divide-y divide-stone-100 bg-white">
                                @foreach ($oldItems as $item)
                                    <tr class="item-row hover:bg-stone-50/50 transition" data-cashier-row>
                                        <td class="p-3 align-top min-w-[180px]">
                                            <select name="items[{{ $loop->index }}][product_id]"
                                                    data-cashier-product
                                                    required
                                                    class="w-full px-2.5 py-1.5 text-xs sm:text-sm rounded-lg border border-stone-300 bg-white focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition text-stone-800">
                                                <option value="">-- Pilih produk --</option>
                                                @foreach ($products as $product)
                                                    <option
                                                        value="{{ $product->id }}"
                                                        data-harga="{{ $product->harga }}"
                                                        data-stok="{{ $product->stok }}"
                                                        data-nama="{{ $product->nama_kue }}"
                                                        @selected((string) ($item['product_id'] ?? '') === (string) $product->id)
                                                    >
                                                        {{ $product->nama_kue }} — Rp {{ number_format($product->harga, 0, ',', '.') }} (stok {{ $product->stok }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="text-[11px] text-stone-500 mt-1">
                                                Harga: <span data-cashier-price class="font-medium text-stone-700">-</span> &middot;
                                                Stok: <span data-cashier-stock class="font-medium text-stone-700">-</span>
                                            </div>
                                        </td>
                                        <td class="p-3 align-top text-center">
                                            <input
                                                type="number"
                                                name="items[{{ $loop->index }}][qty]"
                                                min="1"
                                                step="1"
                                                value="{{ $item['qty'] ?? 1 }}"
                                                data-cashier-qty
                                                required
                                                class="w-16 px-2 py-1.5 text-xs sm:text-sm text-center rounded-lg border border-stone-300 bg-white focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition text-stone-800"
                                            >
                                        </td>
                                        <td class="p-3 align-top text-right whitespace-nowrap pt-3">
                                            <strong data-cashier-subtotal class="text-xs sm:text-sm font-bold text-stone-800">Rp 0</strong>
                                        </td>
                                        <td class="p-3 align-top text-center pt-2.5">
                                            <button type="button"
                                                    class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition cursor-pointer"
                                                    data-cashier-remove
                                                    aria-label="Hapus baris"
                                                    title="Hapus baris">
                                                <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                <span class="sr-only">[X]</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Row Template for dynamic addition --}}
                    <template data-cashier-row-template>
                        <tr class="item-row hover:bg-stone-50/50 transition" data-cashier-row>
                            <td class="p-3 align-top min-w-[180px]">
                                <select name="items[__INDEX__][product_id]"
                                        data-cashier-product
                                        required
                                        class="w-full px-2.5 py-1.5 text-xs sm:text-sm rounded-lg border border-stone-300 bg-white focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition text-stone-800">
                                    <option value="">-- Pilih produk --</option>
                                    @foreach ($products as $product)
                                        <option
                                            value="{{ $product->id }}"
                                            data-harga="{{ $product->harga }}"
                                            data-stok="{{ $product->stok }}"
                                            data-nama="{{ $product->nama_kue }}"
                                        >
                                            {{ $product->nama_kue }} — Rp {{ number_format($product->harga, 0, ',', '.') }} (stok {{ $product->stok }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="text-[11px] text-stone-500 mt-1">
                                    Harga: <span data-cashier-price class="font-medium text-stone-700">-</span> &middot;
                                    Stok: <span data-cashier-stock class="font-medium text-stone-700">-</span>
                                </div>
                            </td>
                            <td class="p-3 align-top text-center">
                                <input
                                    type="number"
                                    name="items[__INDEX__][qty]"
                                    min="1"
                                    step="1"
                                    value="1"
                                    data-cashier-qty
                                    required
                                    class="w-16 px-2 py-1.5 text-xs sm:text-sm text-center rounded-lg border border-stone-300 bg-white focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition text-stone-800"
                                >
                            </td>
                            <td class="p-3 align-top text-right whitespace-nowrap pt-3">
                                <strong data-cashier-subtotal class="text-xs sm:text-sm font-bold text-stone-800">Rp 0</strong>
                            </td>
                            <td class="p-3 align-top text-center pt-2.5">
                                <button type="button"
                                        class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition cursor-pointer"
                                        data-cashier-remove
                                        aria-label="Hapus baris"
                                        title="Hapus baris">
                                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <span class="sr-only">[X]</span>
                                </button>
                            </td>
                        </tr>
                    </template>

                    {{-- Tombol Tambah Baris --}}
                    <div>
                        <button type="button"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-[#6B0F1A] bg-[#FAF2EE] hover:bg-[#F4E1D9] border border-[#6B0F1A]/30 rounded-xl transition shadow-2xs cursor-pointer"
                                data-cashier-add>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Tambah Produk</span>
                        </button>
                    </div>

                    {{-- Form Fields --}}
                    <div class="space-y-4 pt-2 border-t border-stone-100">
                        <div>
                            <label for="nama_pemesan" class="block text-xs font-semibold text-stone-700 mb-1">
                                Nama Pembeli <span class="text-stone-400 font-normal">(opsional)</span>
                            </label>
                            <input type="text"
                                   name="nama_pemesan"
                                   id="nama_pemesan"
                                   maxlength="255"
                                   value="{{ old('nama_pemesan') }}"
                                   placeholder="Contoh: Walk-in Customer / Bu Ani"
                                   class="w-full px-3.5 py-2 text-xs sm:text-sm rounded-xl border border-stone-300 bg-white focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition text-stone-800 placeholder:text-stone-400">
                        </div>

                        <div>
                            <label for="uang_diterima" class="block text-xs font-semibold text-stone-700 mb-1">
                                Uang Diterima <span class="text-stone-400 font-normal">(opsional)</span>
                            </label>
                            <input
                                type="number"
                                name="uang_diterima"
                                id="uang_diterima"
                                min="0"
                                step="1"
                                value="{{ old('uang_diterima') }}"
                                data-cashier-paid
                                placeholder="Contoh: 100000"
                                class="w-full px-3.5 py-2 text-xs sm:text-sm rounded-xl border border-stone-300 bg-white focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition text-stone-800 placeholder:text-stone-400"
                            >
                            <p class="text-[11px] text-stone-500 mt-1">Isi untuk menghitung kembalian secara otomatis.</p>
                        </div>

                        <div>
                            <label for="catatan" class="block text-xs font-semibold text-stone-700 mb-1">
                                Catatan <span class="text-stone-400 font-normal">(opsional)</span>
                            </label>
                            <textarea name="catatan"
                                      id="catatan"
                                      rows="2"
                                      maxlength="1000"
                                      placeholder="Catatan khusus transaksi..."
                                      class="w-full px-3.5 py-2 text-xs sm:text-sm rounded-xl border border-stone-300 bg-white focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition text-stone-800 placeholder:text-stone-400 resize-none">{{ old('catatan') }}</textarea>
                        </div>
                    </div>

                    {{-- Box Total & Kembalian --}}
                    <div class="rounded-2xl bg-[#FAF2EE]/80 border border-[#DE9DA7]/50 p-4 space-y-2">
                        <div class="flex justify-between items-center text-xs sm:text-sm font-medium text-stone-600">
                            <span>Kembalian</span>
                            <strong data-cashier-change class="text-sm sm:text-base font-bold text-emerald-700">-</strong>
                        </div>
                        <div class="border-t border-stone-200/60 pt-2 flex justify-between items-end">
                            <div>
                                <p class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Total Belanja</p>
                                <div class="text-2xl sm:text-3xl font-extrabold text-[#6B0F1A] mt-0.5" data-cashier-total>Rp 0</div>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                            class="w-full py-3.5 px-6 bg-[#6B0F1A] hover:bg-[#540F18] active:bg-[#3A0A0F] text-white font-bold text-sm sm:text-base rounded-full shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-[#6B0F1A]/30 text-center cursor-pointer">
                        Proses Pembayaran Tunai &amp; Cetak Struk / Selesai
                    </button>
                </form>
            @endif
        </section>

        {{-- Daftar Produk Tersedia (Side Card) --}}
        <section class="lg:col-span-5 bg-white rounded-2xl shadow-md border border-stone-200/70 p-5 sm:p-6 space-y-4">
            <div class="border-b border-stone-100 pb-3 flex items-center justify-between">
                <h2 class="text-base font-bold text-stone-800 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-stone-100 text-[#6B0F1A] flex items-center justify-center text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </span>
                    Daftar Produk Tersedia
                </h2>
                <span class="text-xs text-stone-400 font-medium">{{ $products->count() }} item</span>
            </div>

            @if ($products->isEmpty())
                <p class="text-xs text-stone-500 italic">Tidak ada produk ready dengan stok lebih dari nol.</p>
            @else
                <div class="overflow-x-auto max-h-[520px] overflow-y-auto divide-y divide-stone-100">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="sticky top-0 bg-stone-100/90 backdrop-blur-xs text-stone-600 uppercase text-[10px] font-semibold">
                            <tr>
                                <th class="py-2 px-2.5">Produk</th>
                                <th class="py-2 px-2 text-right">Harga</th>
                                <th class="py-2 px-2.5 text-center">Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-xs">
                            @foreach ($products as $product)
                                <tr class="hover:bg-stone-50/60 transition">
                                    <td class="py-2 px-2.5 font-medium text-stone-800">{{ $product->nama_kue }}</td>
                                    <td class="py-2 px-2 text-right text-stone-600 whitespace-nowrap">Rp {{ number_format($product->harga, 0, ',', '.') }}</td>
                                    <td class="py-2 px-2.5 text-center whitespace-nowrap">
                                        <span class="inline-block px-2 py-0.5 text-[11px] font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ $product->stok }} pcs
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>

    {{-- Struk Transaksi / Receipt Section --}}
    @if ($receiptOrder)
        <section id="struk" class="is-visible bg-white rounded-2xl shadow-md border border-stone-200/80 p-6 max-w-md mx-auto space-y-4 mt-6">
            <div class="text-center space-y-1">
                <h2 class="text-lg font-bold text-stone-900 tracking-tight">{{ $storeSetting->store_name ?: 'Toko Kue' }}</h2>
                @if ($storeSetting->store_address)
                    <p class="text-xs text-stone-600">{{ $storeSetting->store_address }}</p>
                @endif
                @if ($storeSetting->store_phone)
                    <p class="text-xs text-stone-600">Telp: {{ $storeSetting->store_phone }}</p>
                @endif
            </div>

            <div class="border-t border-dashed border-stone-300 my-2"></div>

            <div class="text-xs space-y-1 text-stone-700">
                <div class="flex justify-between">
                    <span>No. Transaksi:</span>
                    <span class="font-bold text-[#6B0F1A]">{{ $receiptOrder->kode_pesanan }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Tanggal:</span>
                    <span>{{ $receiptOrder->created_at->format('d-m-Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Kasir:</span>
                    <span>{{ auth()->user()->name ?? 'Kasir' }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Pembeli:</span>
                    <span>{{ $receiptOrder->nama_pemesan ?: 'Pelanggan Toko' }}</span>
                </div>
            </div>

            <div class="border-t border-dashed border-stone-300 my-2"></div>

            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-stone-200 text-stone-600">
                        <th class="py-1 text-left">Item</th>
                        <th class="py-1 text-center w-12">Qty</th>
                        <th class="py-1 text-right w-24">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach ($receiptOrder->items as $item)
                        <tr>
                            <td class="py-1.5 font-medium">{{ $item->product?->nama_kue ?? 'Produk' }}</td>
                            <td class="py-1.5 text-center">{{ $item->jumlah }}</td>
                            <td class="py-1.5 text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="border-t border-dashed border-stone-300 my-2"></div>

            <div class="text-xs space-y-1 text-stone-800">
                <div class="flex justify-between text-sm font-bold">
                    <span>Total</span>
                    <strong class="text-[#6B0F1A]">Rp {{ number_format($receiptOrder->total_harga, 0, ',', '.') }}</strong>
                </div>

                @if ($receipt['uang_diterima'] !== null)
                    <div class="flex justify-between">
                        <span>Tunai</span>
                        <span>Rp {{ number_format($receipt['uang_diterima'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-emerald-700 font-semibold">
                        <span>Kembalian</span>
                        <span>Rp {{ number_format($receipt['kembalian'], 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>

            <p class="text-center text-xs text-stone-500 pt-2">Terima kasih atas pembelian Anda.</p>

            <div class="no-print pt-3 flex items-center justify-center gap-3">
                <button type="button"
                        class="px-4 py-2 bg-[#4D626C] hover:bg-[#3d4f57] text-white rounded-xl text-xs font-semibold shadow-xs transition flex items-center gap-1.5 cursor-pointer"
                        onclick="window.print()">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak Struk</span>
                </button>
                <a href="{{ route('staff.kasir.index') }}"
                   class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-semibold transition">
                    Transaksi Baru
                </a>
            </div>
        </section>
    @endif
@endsection
