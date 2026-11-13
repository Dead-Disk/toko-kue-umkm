<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin Nak Mbarep</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-nav.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-[#FAF2EE] text-stone-800 antialiased min-h-screen flex flex-col lg:flex-row">

    <!-- Mobile Topbar (< lg) -->
    <header class="lg:hidden bg-[#4D626C] text-white px-4 py-3 flex items-center justify-between sticky top-0 z-40 shadow-sm">
        <div class="flex items-center gap-2.5">
            <button type="button" id="openMobileSidebarBtn" class="p-2 rounded-lg hover:bg-white/10 text-white focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/logo-nav.png') }}" alt="Nak Mbarep Logo" class="h-6 w-auto brightness-0 invert">
                <span class="font-bold text-base tracking-tight">Nak Mbarep</span>
            </div>
        </div>

        <a href="{{ route('admin.profile.edit') }}" title="Lihat Profil" class="flex items-center gap-2 hover:opacity-85 transition-opacity">
            <span class="text-xs text-stone-200 capitalize">{{ auth()->user()->role ?? 'Admin' }}</span>
            <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs text-white">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
        </a>
    </header>

    <!-- Mobile Sidebar Drawer Backdrop -->
    <div id="mobileSidebarBackdrop" class="fixed inset-0 bg-stone-900/50 z-50 hidden transition-opacity lg:hidden">
        <div id="mobileSidebarDrawer" class="w-72 max-w-[80vw] h-full bg-[#4D626C] text-stone-200 flex flex-col justify-between shadow-2xl transform -translate-x-full transition-transform duration-300 ease-in-out">
            <div>
                <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <img src="{{ asset('images/logo-nav.png') }}" alt="Nak Mbarep Logo" class="h-7 w-auto brightness-0 invert">
                        <span class="text-white font-bold text-base">Nak Mbarep</span>
                    </div>
                    <button type="button" id="closeMobileSidebarBtn" class="p-1.5 rounded-lg text-stone-300 hover:text-white hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <nav class="p-4 space-y-1.5">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-stone-300 hover:text-white hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Beranda</span>
                    </a>
                    <a href="{{ route('admin.pesanan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-stone-300 hover:text-white hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Pesanan</span>
                    </a>
                    <a href="{{ route('admin.produk.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold bg-white text-[#6B0F1A] shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span>Produk</span>
                    </a>
                    <a href="{{ route('admin.kategori.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-stone-300 hover:text-white hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        <span>Kategori</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-stone-300 hover:text-white hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Pelanggan</span>
                    </a>
                    <a href="{{ route('admin.kasir.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-stone-300 hover:text-white hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        <span>Laporan & Kasir</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-stone-300 hover:text-white hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Pengaturan</span>
                    </a>
                </nav>
            </div>

            <div class="p-4 border-t border-white/10 space-y-2">
                <a href="{{ route('customer.index') }}" target="_blank" class="block text-center py-2 text-xs text-stone-200 bg-white/10 rounded-lg">
                    Lihat Web Toko ↗
                </a>
                <div class="flex items-center gap-2">
                    @if (auth()->user()?->isAdmin())
                        <a href="{{ route('admin.logs.index') }}"
                           class="flex-1 py-2 text-xs text-amber-200 hover:bg-white/10 rounded-lg text-center font-medium border border-amber-300/20">
                            Log Aktivitas
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="{{ auth()->user()?->isAdmin() ? 'flex-1' : 'w-full' }}">
                        @csrf
                        <button type="submit" class="w-full py-2 text-xs text-rose-300 hover:bg-rose-950/30 rounded-lg text-center font-medium">
                            Keluar (Logout)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Sidebar (>= lg) -->
    <div class="hidden lg:block shrink-0 sticky top-0 h-screen">
        @include('admin.partials.sidebar', ['active' => 'produk'])
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8 xl:p-10 overflow-y-auto">
        <div class="flex items-center gap-2 text-xs text-stone-500 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#6B0F1A] transition-colors">Beranda</a>
                <span>/</span>
                <span class="text-stone-800 font-semibold">Produk</span>
        </div>
        <!-- Top Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#5A121A] tracking-tight">
                    Kelola Produk
                </h1>
                <p class="text-stone-500 text-xs sm:text-sm mt-1">
                    {{ $showTrashed
                        ? 'Daftar produk yang telah dihapus. Pulihkan produk jika masih dibutuhkan.'
                        : 'Atur dan kelola semua produk kue di toko Anda.' }}
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
                        <form action="{{ route('admin.produk.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1">
                            @if ($showTrashed)
                                <input type="hidden" name="trashed" value="1">
                            @endif

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
                            @if (auth()->user()->isAdmin())
                                @if ($showTrashed || $categories->sum('trashed_products_count') > 0)
                                    <a href="{{ route('admin.produk.index', array_merge(request()->except(['page', 'trashed', 'export']), $showTrashed ? [] : ['trashed' => 1])) }}"
                                       class="inline-flex items-center justify-center gap-1.5 bg-white border border-stone-200 hover:bg-stone-50 text-stone-700 text-xs font-semibold px-3.5 py-2 rounded-xl transition-colors">
                                        <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span>{{ $showTrashed ? 'Lihat Produk Aktif' : 'Produk Terhapus' }}</span>
                                        @unless ($showTrashed)
                                            <span class="inline-flex items-center justify-center min-w-5 h-5 px-1 rounded-full bg-rose-100 text-rose-700 text-[10px] font-bold">
                                                {{ $categories->sum('trashed_products_count') }}
                                            </span>
                                        @endunless
                                    </a>
                                @endif

                                <button type="button"
                                        id="openCreateModalBtn"
                                        class="inline-flex items-center justify-center gap-1.5 bg-[#6B0F1A] hover:bg-[#520A13] text-white text-xs font-semibold px-4 py-2 rounded-xl shadow-xs transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span>Tambah Produk</span>
                                </button>
                            @endif

                            <a href="{{ route('admin.produk.index', array_merge(request()->query(), ['export' => 'csv'])) }}"
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
                                    <th class="py-3 px-4 text-center">Stok</th>
                                    @if (auth()->user()->isAdmin())
                                        <th class="py-3 px-4 text-center w-24">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                @forelse ($products as $product)
                                    @php
                                        $photoUrl = $product->foto && file_exists(public_path('storage/' . $product->foto))
                                            ? asset('storage/' . $product->foto)
                                            : asset('images/rec-img-' . (($loop->index % 4) + 1) . '.png');
                                        $rowNumber = sprintf('%03d', $loop->iteration + ($products->firstItem() - 1));
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
                                            @if ($showTrashed)
                                                <span class="inline-block mt-1 px-1.5 py-0.5 rounded bg-rose-100 text-rose-700 text-[10px] font-semibold"
                                                      title="Dihapus {{ $product->deleted_at?->format('d M Y H:i') }}">
                                                    Dihapus
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Kategori -->
                                        <td class="py-3 px-4 text-stone-600 whitespace-nowrap">
                                            {{ $product->category?->nama_kategori ?? '-' }}
                                        </td>

                                        <!-- Harga -->
                                        <td class="py-3 px-4 font-semibold text-stone-800 whitespace-nowrap">
                                            Rp{{ number_format($product->harga, 0, ',', '.') }}
                                        </td>

                                        <!-- Stok & Status -->
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            @if ($product->status_stok->value === 'ready' && $product->stok > 0)
                                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                                    Ready ({{ $product->stok }})
                                                </span>
                                            @else
                                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-rose-100 text-rose-800">
                                                    Habis
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Aksi -->
                                        @if (auth()->user()->isAdmin())
                                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                                @if ($showTrashed)
                                                    <form action="{{ route('admin.produk.restore', $product) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Pulihkan produk {{ $product->nama_kue }}?')"
                                                          class="inline-block">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                title="Pulihkan Produk"
                                                                class="inline-flex items-center gap-1 px-2 py-1.5 text-[11px] font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                            </svg>
                                                            <span>Pulihkan</span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <div class="inline-flex items-center gap-1.5">
                                                        <!-- Edit Button (Opens Edit Drawer) -->
                                                        <button type="button"
                                                                title="Edit Produk"
                                                                onclick="openEditModal({{ json_encode([
                                                                    'id' => $product->id,
                                                                    'nama_kue' => $product->nama_kue,
                                                                    'category_id' => $product->category_id,
                                                                    'harga' => $product->harga,
                                                                    'stok' => $product->stok,
                                                                    'max_per_order' => $product->max_per_order,
                                                                    'status_stok' => $product->status_stok->value,
                                                                    'deskripsi' => $product->deskripsi,
                                                                    'foto' => $photoUrl,
                                                                    'update_url' => route('admin.produk.update', $product),
                                                                ]) }})"
                                                                class="p-1.5 text-stone-400 hover:text-sky-600 hover:bg-sky-50 rounded-lg transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                            </svg>
                                                        </button>

                                                        <!-- Delete Form -->
                                                        <form action="{{ route('admin.produk.destroy', $product) }}"
                                                              method="POST"
                                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ $product->nama_kue }}?')"
                                                              class="inline-block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    title="Hapus Produk"
                                                                    class="p-1.5 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->isAdmin() ? '7' : '6' }}" class="py-12 text-center text-stone-400">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-12 h-12 rounded-2xl bg-stone-100 flex items-center justify-center text-stone-400 mb-2">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                    </svg>
                                                </div>
                                                <p class="text-sm font-semibold text-stone-600">
                                                    {{ $showTrashed ? 'Tidak ada produk terhapus' : 'Belum ada produk yang ditemukan' }}
                                                </p>
                                                <p class="text-xs text-stone-400 mt-0.5">
                                                    {{ $showTrashed
                                                        ? 'Semua produk pada filter ini masih aktif atau belum pernah dihapus.'
                                                        : 'Silakan tambahkan produk baru atau ubah kata kunci pencarian.' }}
                                                </p>

                                                @php
                                                    $filteredCategory = request('category_id')
                                                        ? $categories->firstWhere('id', (int) request('category_id'))
                                                        : null;
                                                @endphp

                                                @if (! $showTrashed && $filteredCategory && $filteredCategory->trashed_products_count > 0)
                                                    <p class="text-xs text-amber-600 mt-2">
                                                        {{ $filteredCategory->trashed_products_count }} produk di kategori ini telah dihapus.
                                                        <a href="{{ route('admin.produk.index', array_merge(request()->except(['page', 'trashed', 'export']), ['trashed' => 1])) }}"
                                                           class="font-semibold underline hover:text-amber-800">
                                                            Tampilkan produk terhapus
                                                        </a>
                                                    </p>
                                                @endif
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
                            Tambah Kategori Produk
                        </h2>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.kategori.create') }}"
                               class="inline-flex items-center gap-1 border border-stone-200 hover:border-rose-300 hover:bg-rose-50 text-stone-700 hover:text-[#6B0F1A] text-[11px] font-semibold px-2.5 py-1.5 rounded-lg transition-all">
                                <span>+</span>
                                <span>Tambah Kategori</span>
                            </a>
                        @endif
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
                                $categoryQuery = ['category_id' => $category->id];
                                if ($showTrashed) {
                                    $categoryQuery['trashed'] = 1;
                                }
                            @endphp
                            <a href="{{ $isActiveCategory ? route('admin.produk.index') : route('admin.produk.index', $categoryQuery) }}"
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
                                            @if ($category->trashed_products_count > 0)
                                                <span class="text-rose-400">· {{ $category->trashed_products_count }} dihapus</span>
                                            @endif
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

    </main>

    <!-- Slide-over Drawer / Modal for "Tambah / Edit Produk" -->
    <div id="productModalBackdrop" class="fixed inset-0 bg-stone-900/50 z-50 hidden transition-opacity">
        <div class="fixed inset-y-0 right-0 max-w-full flex">
            <div id="productModalPanel" class="w-screen max-w-md bg-white shadow-2xl flex flex-col justify-between transform translate-x-full transition-transform duration-300 ease-in-out">

                <!-- Drawer Header -->
                <div class="px-6 py-4 border-b border-stone-100 flex items-center justify-between bg-stone-50/60">
                    <div>
                        <h2 id="modalTitle" class="text-base font-bold text-[#5A121A]">
                            Tambah / Edit Produk
                        </h2>
                        <p id="modalSubtitle" class="text-xs text-stone-500">
                            Isi detail formulir kue di bawah ini
                        </p>
                    </div>
                    <button type="button" id="closeProductModalBtn" class="p-2 text-stone-400 hover:text-stone-700 rounded-lg hover:bg-stone-100 transition-colors">
                        ✕
                    </button>
                </div>

                <!-- Form Content (Scrollable) -->
                <form id="productForm" action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <!-- Foto Produk & Preview -->
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1.5">Foto Produk</label>
                        <div class="flex items-center gap-4">
                            <div class="w-24 h-24 rounded-2xl overflow-hidden bg-[#FAF2EE] border border-stone-200 shrink-0">
                                <img id="previewImage"
                                     src="{{ asset('images/hero-strawberry-cake.png') }}"
                                     alt="Preview Foto"
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="space-y-1.5">
                                <label for="fotoInput" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 cursor-pointer shadow-xs transition-colors">
                                    <svg class="w-4 h-4 text-[#6B0F1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    <span>Ubah Foto</span>
                                </label>
                                <input type="file" id="fotoInput" name="foto" accept="image/*" class="hidden">
                                <p class="text-[10px] text-stone-400">Format: JPG, PNG, WEBP (Maks. 2MB)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Nama Produk -->
                    <div>
                        <label for="modalNamaKue" class="block text-xs font-bold text-stone-700 mb-1">Nama Produk</label>
                        <input type="text"
                               id="modalNamaKue"
                               name="nama_kue"
                               placeholder="Contoh: Tart Pink"
                               required
                               class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label for="modalCategoryId" class="block text-xs font-bold text-stone-700 mb-1">Kategori</label>
                        <select id="modalCategoryId"
                                name="category_id"
                                required
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Harga -->
                    <div>
                        <label for="modalHarga" class="block text-xs font-bold text-stone-700 mb-1">Harga (Rp)</label>
                        <input type="number"
                               id="modalHarga"
                               name="harga"
                               placeholder="Contoh: 65000"
                               min="0"
                               step="1"
                               required
                               class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                    </div>

                    <!-- Grid Stok & Status Stok -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="modalStok" class="block text-xs font-bold text-stone-700 mb-1">Stok</label>
                            <input type="number"
                                   id="modalStok"
                                   name="stok"
                                   value="10"
                                   min="0"
                                   step="1"
                                   required
                                   class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                        </div>
                        <div>
                            <label for="modalStatusStok" class="block text-xs font-bold text-stone-700 mb-1">Status Stok</label>
                            <select id="modalStatusStok"
                                    name="status_stok"
                                    required
                                    class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                                <option value="ready">Ready</option>
                                <option value="habis">Habis</option>
                            </select>
                        </div>
                    </div>

                    <!-- Maksimal Beli per Pesanan -->
                    <div>
                        <label for="modalMaxPerOrder" class="block text-xs font-bold text-stone-700 mb-1">Maksimal Beli per Pesanan</label>
                        <input type="number"
                               id="modalMaxPerOrder"
                               name="max_per_order"
                               value="5"
                               min="1"
                               class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label for="modalDeskripsi" class="block text-xs font-bold text-stone-700 mb-1">Deskripsi</label>
                        <textarea id="modalDeskripsi"
                                  name="deskripsi"
                                  rows="3"
                                  placeholder="Tuliskan rasa, ukuran, dan keistimewaan kue..."
                                  class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all"></textarea>
                    </div>

                    <!-- Drawer Footer Buttons -->
                    <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-2.5">
                        <button type="button"
                                id="cancelProductModalBtn"
                                class="px-4 py-2.5 text-xs font-semibold rounded-xl border border-stone-200 hover:bg-stone-50 text-stone-700 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                                id="submitProductModalBtn"
                                class="px-5 py-2.5 text-xs font-semibold rounded-xl bg-[#6B0F1A] hover:bg-[#520A13] text-white shadow-xs transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- JavaScript for Modal & Mobile Drawer -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Mobile Sidebar Drawer
            const openMobileBtn = document.getElementById('openMobileSidebarBtn');
            const closeMobileBtn = document.getElementById('closeMobileSidebarBtn');
            const mobileBackdrop = document.getElementById('mobileSidebarBackdrop');
            const mobileDrawer = document.getElementById('mobileSidebarDrawer');

            if (openMobileBtn) {
                openMobileBtn.addEventListener('click', () => {
                    mobileBackdrop.classList.remove('hidden');
                    setTimeout(() => mobileDrawer.classList.remove('-translate-x-full'), 10);
                });
            }
            if (closeMobileBtn) {
                closeMobileBtn.addEventListener('click', closeMobileSidebar);
            }
            if (mobileBackdrop) {
                mobileBackdrop.addEventListener('click', (e) => {
                    if (e.target === mobileBackdrop) closeMobileSidebar();
                });
            }
            function closeMobileSidebar() {
                mobileDrawer.classList.add('-translate-x-full');
                setTimeout(() => mobileBackdrop.classList.add('hidden'), 300);
            }

            // Product Modal / Slide-over Drawer
            const modalBackdrop = document.getElementById('productModalBackdrop');
            const modalPanel = document.getElementById('productModalPanel');
            const openCreateBtn = document.getElementById('openCreateModalBtn');
            const closeProductModalBtn = document.getElementById('closeProductModalBtn');
            const cancelProductModalBtn = document.getElementById('cancelProductModalBtn');
            const productForm = document.getElementById('productForm');
            const formMethod = document.getElementById('formMethod');
            const modalTitle = document.getElementById('modalTitle');
            const submitBtn = document.getElementById('submitProductModalBtn');

            const fotoInput = document.getElementById('fotoInput');
            const previewImage = document.getElementById('previewImage');

            // Image Preview on file select
            if (fotoInput) {
                fotoInput.addEventListener('change', function () {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            previewImage.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Open Create Modal
            if (openCreateBtn) {
                openCreateBtn.addEventListener('click', function () {
                    modalTitle.textContent = 'Tambah Produk';
                    submitBtn.textContent = 'Simpan Produk';
                    productForm.action = "{{ route('admin.produk.store') }}";
                    formMethod.value = 'POST';

                    // Reset values
                    productForm.reset();
                    previewImage.src = "{{ asset('images/hero-strawberry-cake.png') }}";
                    document.getElementById('modalStatusStok').value = 'ready';
                    document.getElementById('modalStok').value = '10';
                    document.getElementById('modalMaxPerOrder').value = '5';

                    openDrawer();
                });
            }

            // Open Edit Modal Function
            window.openEditModal = function (product) {
                modalTitle.textContent = 'Edit Produk: ' + product.nama_kue;
                submitBtn.textContent = 'Simpan Perubahan';
                productForm.action = product.update_url;
                formMethod.value = 'PUT';

                document.getElementById('modalNamaKue').value = product.nama_kue || '';
                document.getElementById('modalCategoryId').value = product.category_id || '';
                document.getElementById('modalHarga').value = product.harga || 0;
                document.getElementById('modalStok').value = product.stok || 0;
                document.getElementById('modalStatusStok').value = product.status_stok || 'ready';
                document.getElementById('modalMaxPerOrder').value = product.max_per_order || 5;
                document.getElementById('modalDeskripsi').value = product.deskripsi || '';

                if (product.foto) {
                    previewImage.src = product.foto;
                }

                openDrawer();
            };

            function openDrawer() {
                modalBackdrop.classList.remove('hidden');
                setTimeout(() => modalPanel.classList.remove('translate-x-full'), 10);
            }

            function closeDrawer() {
                modalPanel.classList.add('translate-x-full');
                setTimeout(() => modalBackdrop.classList.add('hidden'), 300);
            }

            if (closeProductModalBtn) closeProductModalBtn.addEventListener('click', closeDrawer);
            if (cancelProductModalBtn) cancelProductModalBtn.addEventListener('click', closeDrawer);
            if (modalBackdrop) {
                modalBackdrop.addEventListener('click', (e) => {
                    if (e.target === modalBackdrop) closeDrawer();
                });
            }
        });
    </script>
</body>
</html>
