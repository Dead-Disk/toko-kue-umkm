<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori & Custom Cake - Admin Nak Mbarep</title>
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
                    @include('admin.partials.navigation', ['active' => 'kategori', 'mobile' => true])
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
        @include('admin.partials.sidebar', ['active' => 'kategori'])
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8 xl:p-10 overflow-y-auto">
        <div class="flex items-center gap-2 text-xs text-stone-500 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#6B0F1A] transition-colors">Beranda</a>
                <span>/</span>
                <span class="text-stone-800 font-semibold">Kategori</span>
        </div>
        <!-- Top Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#5A121A] tracking-tight">
                    Kelola Kategori &amp; Opsi Custom Cake
                </h1>
                <p class="text-stone-500 text-xs sm:text-sm mt-1">
                    Atur kategori produk kue dan opsi varian custom cake di toko Anda.
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
                    Terdapat kesalahan:
                </p>
                <ul class="list-disc list-inside space-y-1 text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $totalKategori = $categories->count();
            $totalProdukKategori = $categories->sum('products_count');
            $totalVarian = $cakeOptions->count();
            $topCategory = $categories->sortByDesc('products_count')->first();
        @endphp

        <!-- 4 Stat Cards Row -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Stat 1: Total Kategori -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-[#6B0F1A] shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-stone-500 font-medium">Total Kategori</p>
                        <p class="text-2xl font-bold text-stone-900 leading-tight">
                            {{ $totalKategori }}
                        </p>
                    </div>
                </div>
                <div class="mt-3.5 pt-3 border-t border-stone-100 flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600">
                    <span>Kelompok katalog aktif</span>
                </div>
            </div>

            <!-- Stat 2: Total Produk Terhubung -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-stone-500 font-medium">Total Produk</p>
                        <p class="text-2xl font-bold text-stone-900 leading-tight">
                            {{ $totalProdukKategori }}
                        </p>
                    </div>
                </div>
                <div class="mt-3.5 pt-3 border-t border-stone-100 flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600">
                    <span>Tersebar di kategori</span>
                </div>
            </div>

            <!-- Stat 3: Varian Custom Cake -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-stone-500 font-medium">Varian Custom</p>
                        <p class="text-2xl font-bold text-stone-900 leading-tight">
                            {{ $totalVarian }}
                        </p>
                    </div>
                </div>
                <div class="mt-3.5 pt-3 border-t border-stone-100 flex items-center gap-1.5 text-[11px] font-semibold text-sky-600">
                    <span>Bentuk &amp; ukuran custom cake</span>
                </div>
            </div>

            <!-- Stat 4: Kategori Terpopuler -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-stone-500 font-medium">Kategori Teratas</p>
                        <p class="text-lg font-bold text-stone-900 leading-tight truncate">
                            {{ $topCategory?->nama_kategori ?? '-' }}
                        </p>
                    </div>
                </div>
                <div class="mt-3.5 pt-3 border-t border-stone-100 flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600">
                    <span>{{ $topCategory?->products_count ?? 0 }} produk aktif</span>
                </div>
            </div>
        </section>

        <!-- Navigation Tabs Container -->
        <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 overflow-hidden mb-6">
            <!-- Tabs Bar -->
            <div class="px-5 pt-4 border-b border-stone-200/70 flex items-center gap-4 bg-stone-50/50">
                <button type="button"
                        id="tabCategoryBtn"
                        onclick="switchTab('categories')"
                        class="pb-3 text-xs font-bold border-b-2 border-[#6B0F1A] text-[#6B0F1A] flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span>Kategori Kue ({{ $totalKategori }})</span>
                </button>

                <button type="button"
                        id="tabOptionBtn"
                        onclick="switchTab('options')"
                        class="pb-3 text-xs font-semibold border-b-2 border-transparent text-stone-500 hover:text-stone-800 flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <span>Varian Custom Cake ({{ $totalVarian }})</span>
                </button>
            </div>

            <!-- Tab 1: Kategori Kue Panel -->
            <div id="tabCategoryPanel" class="p-5 sm:p-6 space-y-6">

                <!-- Header Actions Row -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-stone-100">
                    <div>
                        <h2 class="text-base font-bold text-[#5A121A]">
                            Kelola Kategori Kue
                        </h2>
                        <p class="text-xs text-stone-500">
                            Daftar kategori untuk mengelompokkan kue pada katalog pembeli.
                        </p>
                    </div>

                    <a href="{{ route('admin.kategori.create') }}"
                       class="inline-flex items-center gap-1.5 bg-[#6B0F1A] hover:bg-[#520A13] text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-xs transition-colors shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tambah Kategori</span>
                    </a>
                </div>

                <!-- Visual Category Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
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
                        <div class="bg-stone-50/70 rounded-2xl p-4 border border-stone-200/70 hover:border-rose-200 hover:bg-rose-50/30 transition-all flex flex-col justify-between group">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="w-12 h-12 rounded-xl bg-white p-1.5 border border-stone-200/70 shadow-xs flex items-center justify-center shrink-0">
                                    <img src="{{ $catImg }}" alt="{{ $category->nama_kategori }}" class="w-full h-full object-contain">
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white text-stone-600 border border-stone-200/80">
                                    {{ $category->products_count }} Produk
                                    @if ($category->trashed_products_count > 0)
                                        · {{ $category->trashed_products_count }} Dihapus
                                    @endif
                                </span>
                            </div>

                            <div>
                                <h3 class="font-bold text-sm text-stone-900 group-hover:text-[#6B0F1A] transition-colors">
                                    {{ $category->nama_kategori }}
                                </h3>
                                <p class="text-[11px] text-stone-400 font-mono mt-0.5">
                                    slug: {{ $category->slug }}
                                </p>
                            </div>

                            <div class="pt-3 mt-3 border-t border-stone-200/50 flex items-center justify-between">
                                <a href="{{ route('admin.produk.index', ['category_id' => $category->id]) }}"
                                   class="text-[11px] text-stone-500 hover:text-[#6B0F1A] font-semibold flex items-center gap-1 transition-colors">
                                    <span>Lihat Produk</span>
                                    <span>→</span>
                                </a>

                                <div class="flex items-center gap-1">
                                    <a href="{{ route('admin.kategori.edit', $category) }}"
                                       title="Edit Kategori"
                                       class="p-1.5 text-stone-400 hover:text-sky-600 hover:bg-white rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </a>

                                    <form action="{{ route('admin.kategori.destroy', $category) }}"
                                          method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori {{ $category->nama_kategori }}?')"
                                          class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="Hapus Kategori"
                                                class="p-1.5 text-stone-400 hover:text-rose-600 hover:bg-white rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center text-stone-400">
                            Belum ada kategori yang dibuat.
                        </div>
                    @endforelse
                </div>

                <!-- Table View of Categories -->
                <div class="mt-6">
                    <h3 class="text-xs font-bold text-stone-700 uppercase tracking-wider mb-3">
                        Tabel Rincian Kategori
                    </h3>
                    <div class="overflow-x-auto rounded-xl border border-stone-200/70">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-stone-50 text-stone-600 font-semibold border-b border-stone-200/70">
                                    <th class="py-3 px-4 w-12 text-center">No</th>
                                    <th class="py-3 px-4">Nama Kategori</th>
                                    <th class="py-3 px-4">Slug</th>
                                    <th class="py-3 px-4 text-center">Jumlah Produk</th>
                                    <th class="py-3 px-4 text-center w-28">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                @forelse ($categories as $category)
                                    <tr class="hover:bg-stone-50/70 transition-colors">
                                        <td class="py-3 px-4 text-center text-stone-500 font-mono text-[11px]">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td class="py-3 px-4 font-bold text-stone-800">
                                            {{ $category->nama_kategori }}
                                        </td>
                                        <td class="py-3 px-4 text-stone-500 font-mono text-[11px]">
                                            {{ $category->slug }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-[#6B0F1A]">
                                                {{ $category->products_count }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1.5">
                                                <a href="{{ route('admin.kategori.edit', $category) }}"
                                                   class="px-2.5 py-1 text-[11px] font-semibold text-stone-700 bg-stone-100 hover:bg-stone-200 rounded-lg transition-colors">
                                                    Edit
                                                </a>
                                                <form action="{{ route('admin.kategori.destroy', $category) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Hapus kategori ini?')"
                                                      class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="px-2.5 py-1 text-[11px] font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-stone-400">
                                            Belum ada kategori kue.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Tab 2: Varian Custom Cake Panel -->
            <div id="tabOptionPanel" class="p-5 sm:p-6 space-y-6 hidden">

                <!-- Header Actions Row -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-stone-100">
                    <div>
                        <h2 class="text-base font-bold text-[#5A121A]">
                            Kelola Varian Ukuran
                        </h2>
                        <p class="text-xs text-stone-500">
                            Pilihan bentuk, ukuran, dan harga dasar kue custom cake yang dapat dipesan pembeli.
                        </p>
                    </div>

                    <a href="{{ route('admin.pengaturan.create') }}"
                       class="inline-flex items-center gap-1.5 bg-[#6B0F1A] hover:bg-[#520A13] text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-xs transition-colors shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tambah Opsi</span>
                    </a>
                </div>

                <!-- Table View of Cake Options -->
                <div class="overflow-x-auto rounded-xl border border-stone-200/70">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-stone-50 text-stone-600 font-semibold border-b border-stone-200/70">
                                <th class="py-3 px-4 w-12 text-center">No</th>
                                <th class="py-3 px-4">Bentuk</th>
                                <th class="py-3 px-4">Ukuran</th>
                                <th class="py-3 px-4">Harga Dasar (Rp)</th>
                                <th class="py-3 px-4">Label Opsi</th>
                                <th class="py-3 px-4 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse ($cakeOptions as $cakeOption)
                                <tr class="hover:bg-stone-50/70 transition-colors">
                                    <td class="py-3 px-4 text-center text-stone-500 font-mono text-[11px]">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="py-3 px-4 font-bold text-stone-800">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800">
                                            {{ $cakeOption->bentuk->label() ?? $cakeOption->bentuk->value }}
                                        </span>
                                        <span class="hidden">{{ $cakeOption->bentuk->value }}</span>
                                    </td>
                                    <td class="py-3 px-4 font-semibold text-stone-800">
                                        {{ $cakeOption->ukuran }}
                                    </td>
                                    <td class="py-3 px-4 text-stone-800">
                                        <span class="font-bold text-stone-900">Rp{{ number_format($cakeOption->harga_dasar, 0, ',', '.') }}</span>
                                        <span class="hidden">{{ $cakeOption->harga_dasar }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-stone-600">
                                        {{ $cakeOption->label() }}
                                    </td>
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="{{ route('admin.pengaturan.edit', $cakeOption) }}"
                                               class="px-2.5 py-1 text-[11px] font-semibold text-stone-700 bg-stone-100 hover:bg-stone-200 rounded-lg transition-colors">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.pengaturan.destroy', $cakeOption) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Hapus opsi ini?')"
                                                  class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-2.5 py-1 text-[11px] font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-stone-400">
                                        Belum ada opsi custom cake.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>

    </main>

    <!-- JavaScript for Tabs & Mobile Drawer -->
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

            // Tab Switching Logic
            window.switchTab = function (tab) {
                const catBtn = document.getElementById('tabCategoryBtn');
                const optBtn = document.getElementById('tabOptionBtn');
                const catPanel = document.getElementById('tabCategoryPanel');
                const optPanel = document.getElementById('tabOptionPanel');

                if (tab === 'categories') {
                    catPanel.classList.remove('hidden');
                    optPanel.classList.add('hidden');

                    catBtn.className = 'pb-3 text-xs font-bold border-b-2 border-[#6B0F1A] text-[#6B0F1A] flex items-center gap-2 transition-all';
                    optBtn.className = 'pb-3 text-xs font-semibold border-b-2 border-transparent text-stone-500 hover:text-stone-800 flex items-center gap-2 transition-all';
                } else {
                    optPanel.classList.remove('hidden');
                    catPanel.classList.add('hidden');

                    optBtn.className = 'pb-3 text-xs font-bold border-b-2 border-[#6B0F1A] text-[#6B0F1A] flex items-center gap-2 transition-all';
                    catBtn.className = 'pb-3 text-xs font-semibold border-b-2 border-transparent text-stone-500 hover:text-stone-800 flex items-center gap-2 transition-all';
                }
            };

            // Read URL param ?tab=custom or #custom
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') === 'custom' || window.location.hash === '#custom') {
                switchTab('options');
            }
        });
    </script>
</body>
</html>
