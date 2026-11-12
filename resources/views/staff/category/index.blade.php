@extends('staff.layouts.app', ['title' => 'Kategori Produk', 'active' => 'kategori'])

@section('content')
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-stone-500 mb-1">
        <a href="{{ route('staff.dashboard') }}" class="hover:text-[#6B0F1A] transition-colors">Beranda</a>
        <span>/</span>
        <span class="text-stone-800 font-semibold">Kategori</span>
    </div>

    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#5A121A] tracking-tight">
                Kategori Produk
            </h1>
            <p class="text-stone-500 text-xs sm:text-sm mt-1">
                Daftar kategori kue beserta ringkasan jumlah dan stok produknya.
            </p>
        </div>

        <a href="{{ route('staff.produk.index') }}"
           class="inline-flex items-center gap-1.5 self-start px-4 py-2.5 rounded-xl bg-white border border-stone-200 hover:bg-stone-50 text-stone-700 text-xs font-semibold shadow-xs transition-colors">
            <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span>Lihat Stok Produk</span>
        </a>
    </div>

    <!-- Summary Cards -->
    <section class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-[#6B0F1A] shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-stone-500 font-medium">Total Kategori</p>
                    <p class="text-2xl font-bold text-stone-900 leading-tight">{{ $totalKategori }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-700 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-stone-500 font-medium">Total Produk</p>
                    <p class="text-2xl font-bold text-stone-900 leading-tight">{{ $totalProduk }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Category Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse ($categories as $category)
            @php
                $catImg = match($category->slug) {
                    'kue-tart' => asset('images/cat-kue-tart.png'),
                    'donat' => asset('images/cat-donat.png'),
                    'roti-gulung' => asset('images/cat-roti-gulung.png'),
                    'spiku' => asset('images/cat-spiku.png'),
                    default => asset('images/cat-kue-tart.png'),
                };
            @endphp

            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow flex flex-col justify-between">
                <div class="flex items-start gap-3.5">
                    <div class="w-14 h-14 rounded-2xl overflow-hidden bg-[#FAF2EE] p-1.5 border border-stone-100 shrink-0 flex items-center justify-center">
                        <img src="{{ $catImg }}" alt="{{ $category->nama_kategori }}" class="w-full h-full object-contain">
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-sm font-bold text-stone-800 truncate" title="{{ $category->nama_kategori }}">
                            {{ $category->nama_kategori }}
                        </h2>
                        <p class="text-[11px] text-stone-500 mt-0.5">
                            {{ $category->products_count }} produk &middot;
                            {{ $category->total_stok ?? 0 }} pcs stok
                        </p>

                        <div class="mt-2 flex flex-wrap items-center gap-1.5">
                            @if ($category->produk_habis_count > 0)
                                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-100">
                                    {{ $category->produk_habis_count }} habis
                                </span>
                            @else
                                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    Stok aman
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <a href="{{ route('staff.produk.index', ['category_id' => $category->id]) }}"
                   class="mt-4 inline-flex items-center justify-center gap-1.5 w-full px-3.5 py-2 rounded-xl bg-[#FAF2EE] hover:bg-[#F4E1D9] text-[#6B0F1A] text-xs font-semibold border border-[#6B0F1A]/15 transition-colors">
                    <span>Lihat Produk Kategori Ini</span>
                    <span>→</span>
                </a>
            </div>
        @empty
            <div class="sm:col-span-2 xl:col-span-3 bg-white rounded-2xl p-10 text-center border border-stone-200/60">
                <div class="w-14 h-14 rounded-2xl bg-stone-100 flex items-center justify-center text-stone-400 mx-auto mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-stone-600">Belum ada kategori produk.</p>
                <p class="text-xs text-stone-400 mt-1">Kategori yang ditambahkan admin akan muncul di sini.</p>
            </div>
        @endforelse
    </div>
@endsection
