@extends('staff.layouts.app', ['title' => 'Stok Produk', 'active' => 'produk'])

@section('content')
        <div class="flex items-center gap-2 text-xs text-stone-500 mb-1">
                <a href="{{ route('staff.dashboard') }}" class="hover:text-[#6B0F1A] transition-colors">Beranda</a>
                <span>/</span>
                <span class="text-stone-800 font-semibold">Produk</span>
        </div>
        <!-- Top Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#5A121A] tracking-tight">
                    Stok Produk
                </h1>
                <p class="text-stone-500 text-xs sm:text-sm mt-1">
                    Pantau dan perbarui stok serta status ketersediaan produk langsung dari tabel.
                </p>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">✕</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-xs">
                <p class="font-bold mb-1.5 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Terdapat kesalahan pengisian formulir:
                </p>
                <ul class="list-disc list-inside space-y-1 text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Responsive Layout: 2 Columns (Table on left, Category & Mascot on right) -->
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

            <!-- Left / Main Column: Table & Filters (xl:col-span-8) -->
            <div class="xl:col-span-8 space-y-4">

                <!-- Table Card Container -->
                <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 overflow-hidden">

                    <!-- Filter & Action Toolbar -->
                    <div class="p-4 sm:p-5 border-b border-stone-100 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                        <!-- Search & Category Filter Form -->
                        <form action="{{ route('staff.produk.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1">
                            <!-- Search Input -->
                            <div class="relative flex-1">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </span>
                                <input type="text"
                                       name="search"
                                       value="{{ request('search') }}"
                                       placeholder="Cari nama produk, kategori, atau harga..."
                                       class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                            </div>

                            <!-- Dropdown Kategori -->
                            <select name="category_id"
                                    onchange="this.form.submit()"
                                    class="py-2 px-3 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] text-stone-700 font-medium">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>
                                        {{ $cat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('staff.produk.index', array_merge(request()->query(), ['export' => 'csv'])) }}"
                               class="inline-flex items-center justify-center gap-1.5 bg-white border border-stone-200 hover:bg-stone-50 text-stone-700 text-xs font-semibold px-3.5 py-2 rounded-xl transition-colors">
                                <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Export</span>
                            </a>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-[#FAF2EE]/80 text-stone-600 font-semibold border-b border-stone-100">
                                    <th class="py-3 px-4 w-12 text-center">No</th>
                                    <th class="py-3 px-4 w-16">Gambar</th>
                                    <th class="py-3 px-4 font-semibold">Nama Produk</th>
                                    <th class="py-3 px-4 font-semibold">Kategori</th>
                                    <th class="py-3 px-4 font-semibold">Harga</th>
                                    <th class="py-3 px-4 text-center w-40">Stok</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                @forelse ($products as $product)
                                    @php
                                        $photoUrl = $product->foto && file_exists(public_path('storage/' . $product->foto))
                                            ? asset('storage/' . $product->foto)
                                            : asset('images/rec-img-' . (($loop->index % 4) + 1) . '.png');
                                        $rowNumber = sprintf('%03d', $loop->iteration + ($products->firstItem() - 1));
                                        $isReady = $product->status_stok->value === 'ready' && $product->stok > 0;
                                    @endphp
                                    <tr class="hover:bg-stone-50/70 transition-colors">
                                        <!-- No -->
                                        <td class="py-3 px-4 text-center text-stone-500 font-mono text-[11px]">
                                            {{ $rowNumber }}
                                        </td>

                                        <!-- Gambar -->
                                        <td class="py-3 px-4">
                                            <div class="w-12 h-12 rounded-xl overflow-hidden bg-stone-100 border border-stone-200/60 shrink-0">
                                                <img src="{{ $photoUrl }}"
                                                     alt="{{ $product->nama_kue }}"
                                                     class="w-full h-full object-cover">
                                            </div>
                                        </td>

                                        <!-- Nama Produk -->
                                        <td class="py-3 px-4 font-bold text-stone-800">
                                            <span class="block truncate max-w-[180px] sm:max-w-xs" title="{{ $product->nama_kue }}">
                                                {{ $product->nama_kue }}
                                            </span>
                                        </td>

                                        <!-- Kategori -->
                                        <td class="py-3 px-4 text-stone-600 whitespace-nowrap">
                                            {{ $product->category?->nama_kategori ?? '-' }}
                                        </td>

                                        <!-- Harga -->
                                        <td class="py-3 px-4 font-semibold text-stone-800 whitespace-nowrap">
                                            Rp{{ number_format($product->harga, 0, ',', '.') }}
                                        </td>

                                        <!-- Stok & Status Interaktif -->
                                        <td class="py-3 px-4">
                                            <div class="flex flex-col items-center gap-2"
                                                 data-stock-cell
                                                 data-update-url="{{ route('staff.produk.stock.update', $product) }}"
                                                 data-stock="{{ $product->stok }}"
                                                 data-is-ready="{{ $isReady ? 'true' : 'false' }}">

                                                <!-- Stepper stok -->
                                                <div class="inline-flex items-center overflow-hidden rounded-lg border border-stone-200 bg-white shadow-2xs">
                                                    <button type="button"
                                                            data-stock-decrement
                                                            aria-label="Kurangi stok {{ $product->nama_kue }}"
                                                            class="flex h-7 w-7 items-center justify-center text-stone-500 transition-colors hover:bg-rose-50 hover:text-[#6B0F1A] disabled:cursor-not-allowed disabled:opacity-40">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14"/>
                                                        </svg>
                                                    </button>

                                                    <input type="number"
                                                           data-stock-input
                                                           value="{{ $product->stok }}"
                                                           min="0"
                                                           inputmode="numeric"
                                                           aria-label="Jumlah stok {{ $product->nama_kue }}"
                                                           class="h-7 w-11 appearance-none border-x border-stone-200 bg-white text-center font-mono text-[11px] font-bold text-stone-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-[#6B0F1A]/20 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">

                                                    <button type="button"
                                                            data-stock-increment
                                                            aria-label="Tambah stok {{ $product->nama_kue }}"
                                                            class="flex h-7 w-7 items-center justify-center text-stone-500 transition-colors hover:bg-emerald-50 hover:text-emerald-700 disabled:cursor-not-allowed disabled:opacity-40">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v14m-7-7h14"/>
                                                        </svg>
                                                    </button>
                                                </div>

                                                <!-- Toggle Ready / Habis -->
                                                <button type="button"
                                                        role="switch"
                                                        aria-checked="{{ $isReady ? 'true' : 'false' }}"
                                                        data-stock-toggle
                                                        title="{{ $product->stok <= 0 ? 'Tambah stok terlebih dahulu untuk mengubah status' : 'Klik untuk menandai ' . ($isReady ? 'habis' : 'ready') }}"
                                                        @disabled($product->stok <= 0)
                                                        class="inline-flex items-center gap-1.5 rounded-full py-0.5 pl-0.5 pr-1.5 transition-colors hover:bg-stone-100 disabled:cursor-not-allowed disabled:opacity-60">
                                                    <span data-stock-toggle-track
                                                          class="relative inline-flex h-4 w-7 items-center rounded-full transition-colors {{ $isReady ? 'bg-emerald-500' : 'bg-stone-300' }}">
                                                        <span data-stock-toggle-knob
                                                              class="inline-block h-3 w-3 transform rounded-full bg-white shadow-sm transition-transform {{ $isReady ? 'translate-x-3.5' : 'translate-x-0.5' }}"></span>
                                                    </span>
                                                    <span data-stock-status-label
                                                          class="text-[10px] font-semibold {{ $isReady ? 'text-emerald-700' : 'text-stone-500' }}">
                                                        {{ $isReady ? 'Ready' : 'Habis' }}
                                                    </span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-12 text-center text-stone-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-12 h-12 rounded-2xl bg-stone-100 flex items-center justify-center text-stone-400 mb-2">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                    </svg>
                                                </div>
                                                <p class="text-sm font-semibold text-stone-600">Belum ada produk yang ditemukan</p>
                                                <p class="text-xs text-stone-400 mt-0.5">Coba ubah kata kunci pencarian atau hubungi admin untuk menambah produk.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Footer -->
                    <div class="p-4 border-t border-stone-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-stone-500">
                        <div>
                            Menampilkan {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk
                        </div>
                        <div>
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Kategori Produk & Maskot (xl:col-span-4) -->
            <div class="xl:col-span-4 space-y-6">

                <!-- Kategori Card -->
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm sm:text-base font-bold text-[#5A121A] tracking-tight">
                            Kategori Produk
                        </h2>
                        <a href="{{ route('staff.kategori.index') }}"
                           class="inline-flex items-center gap-1 border border-stone-200 hover:border-rose-300 hover:bg-rose-50 text-stone-700 hover:text-[#6B0F1A] text-[11px] font-semibold px-2.5 py-1.5 rounded-lg transition-all">
                            <span>Lihat Semua</span>
                            <span>→</span>
                        </a>
                    </div>

                    <!-- Category Items List -->
                    <div class="space-y-2">
                        @forelse ($categories as $category)
                            @php
                                $catImg = match($category->slug) {
                                    'kue-tart' => asset('images/cat-kue-tart.png'),
                                    'donat' => asset('images/cat-donat.png'),
                                    'roti-gulung' => asset('images/cat-roti-gulung.png'),
                                    'spiku' => asset('images/cat-spiku.png'),
                                    default => asset('images/cat-kue-tart.png'),
                                };
                                $isActiveCategory = request('category_id') == $category->id;
                            @endphp
                            <a href="{{ $isActiveCategory ? route('staff.produk.index') : route('staff.produk.index', ['category_id' => $category->id]) }}"
                               class="flex items-center justify-between p-2.5 rounded-xl border {{ $isActiveCategory ? 'border-[#6B0F1A] bg-rose-50/60 font-semibold' : 'border-stone-100 hover:border-stone-200 hover:bg-stone-50/60' }} transition-all group">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl overflow-hidden bg-white p-1 border border-stone-100 shrink-0 flex items-center justify-center">
                                        <img src="{{ $catImg }}" alt="{{ $category->nama_kategori }}" class="w-full h-full object-contain">
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-stone-800 group-hover:text-[#6B0F1A] transition-colors">
                                            {{ $category->nama_kategori }}
                                        </p>
                                        <p class="text-[10px] text-stone-400">
                                            {{ $category->products_count }} produk
                                        </p>
                                    </div>
                                </div>
                                <span class="text-stone-300 group-hover:text-[#6B0F1A] group-hover:translate-x-0.5 transition-all text-xs font-bold">
                                    ›
                                </span>
                            </a>
                        @empty
                            <p class="text-xs text-stone-400 py-3 text-center">Belum ada kategori</p>
                        @endforelse
                    </div>
                </div>

                <!-- Mascot Bear Card -->
                <div class="bg-gradient-to-br from-rose-50/80 to-pink-50/60 rounded-2xl p-5 border border-rose-100 flex items-center justify-center relative overflow-hidden">
                    <img src="{{ asset('images/hero-mascot.png') }}"
                         alt="Maskot Nak Mbarep"
                         class="h-28 sm:h-32 w-auto object-contain drop-shadow-sm select-none pointer-events-none">
                </div>

            </div>

        </div>
@endsection
