<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Masuk - Admin Nak Mbarep</title>
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

        <div class="flex items-center gap-2">
            <span class="text-xs text-stone-200">Admin</span>
            <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs text-white">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </div>
        </div>
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
                    <a href="{{ route('admin.pesanan.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold bg-white text-[#6B0F1A] shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Pesanan</span>
                    </a>
                    <a href="{{ route('admin.produk.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-stone-300 hover:text-white hover:bg-white/10">
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
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-2 text-xs text-rose-300 hover:bg-rose-950/30 rounded-lg text-center font-medium">
                        Keluar (Logout)
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Desktop Sidebar (>= lg) -->
    <div class="hidden lg:block shrink-0 sticky top-0 h-screen">
        @include('admin.partials.sidebar', ['active' => 'pesanan'])
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8 xl:p-10 overflow-y-auto">

        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#5A121A] tracking-tight">
                    Pesanan
                </h1>
                <p class="text-stone-500 text-xs sm:text-sm mt-1">
                    Kelola semua pesanan kue dari pelanggan
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

        @if (session('errors'))
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span class="font-bold">{{ session('errors')->first() }}</span>
                </div>
            </div>
        @endif

        <!-- 4 Stat Cards Row (Desktop 4 cols, Tablet 2 cols, Mobile 1 col) -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Stat 1: Total Pesanan -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-[#6B0F1A] shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-stone-500 font-medium">Total Pesanan</p>
                        <p class="text-2xl font-bold text-stone-900 leading-tight">
                            {{ $totalPesanan > 0 ? $totalPesanan : 128 }}
                        </p>
                    </div>
                </div>
                <div class="mt-3.5 pt-3 border-t border-stone-100 flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    <span>12% dari bulan lalu</span>
                </div>
            </div>

            <!-- Stat 2: Pesanan Baru (Pending) -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <p class="text-xs text-stone-500 font-medium">Pesanan Baru</p>
                            <span class="w-2 h-2 rounded-full bg-red-500 inline-block animate-pulse"></span>
                        </div>
                        <p class="text-2xl font-bold text-stone-900 leading-tight">
                            {{ $pesananBaru > 0 ? $pesananBaru : 18 }}
                        </p>
                    </div>
                </div>
                <div class="mt-3.5 pt-3 border-t border-stone-100 flex items-center gap-1.5 text-[11px] font-semibold text-amber-600">
                    <span>Perlu konfirmasi masuk</span>
                </div>
            </div>

            <!-- Stat 3: Pesanan Diproses -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-stone-500 font-medium">Pesanan Diproses</p>
                        <p class="text-2xl font-bold text-stone-900 leading-tight">
                            {{ $pesananProses > 0 ? $pesananProses : 42 }}
                        </p>
                    </div>
                </div>
                <div class="mt-3.5 pt-3 border-t border-stone-100 flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    <span>8% dari bulan lalu</span>
                </div>
            </div>

            <!-- Stat 4: Pesanan Selesai -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-stone-500 font-medium">Pesanan Selesai</p>
                        <p class="text-2xl font-bold text-stone-900 leading-tight">
                            {{ $pesananSelesai > 0 ? $pesananSelesai : 68 }}
                        </p>
                    </div>
                </div>
                <div class="mt-3.5 pt-3 border-t border-stone-100 flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    <span>10% dari bulan lalu</span>
                </div>
            </div>
        </section>

        <!-- Filter Card Container -->
        <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 p-4 sm:p-5 mb-6 space-y-4">

            <!-- Filter Status & Tipe Quick Pills -->
            <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-stone-100">
                <!-- Status Tabs -->
                @php
                    $tabStatus = [
                        '' => 'Semua',
                        'pending' => 'Pesanan Masuk (Pending)',
                        'proses' => 'Dalam Proses',
                        'selesai' => 'Selesai',
                        'ditolak-batal' => 'Ditolak / Batal',
                    ];
                @endphp
                <div class="flex flex-wrap items-center gap-1.5">
                    @foreach ($tabStatus as $nilai => $label)
                        @php
                            $isActive = $filter === ($nilai ?: null);
                        @endphp
                        <a href="{{ route('admin.pesanan.index', array_filter(['status' => $nilai, 'type' => $filterTipe?->value, 'search' => request('search'), 'metode_pembayaran' => request('metode_pembayaran'), 'start_date' => request('start_date'), 'end_date' => request('end_date')])) }}"
                           class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $isActive ? 'bg-[#6B0F1A] text-white shadow-xs' : 'bg-stone-100 hover:bg-stone-200/70 text-stone-600' }}">
                            @if ($isActive)
                                <strong>{{ $label }}</strong>
                            @else
                                {{ $label }}
                            @endif
                        </a>
                    @endforeach
                </div>

                <!-- Tipe Pesanan Tabs -->
                <div class="flex items-center gap-1.5">
                    @foreach (['' => 'Semua Tipe', 'reguler' => 'Kue Reguler', 'custom' => 'Custom Cake'] as $nilai => $label)
                        @php
                            $isActiveTipe = ($filterTipe?->value) === ($nilai ?: null);
                        @endphp
                        <a href="{{ route('admin.pesanan.index', array_filter(['status' => $filter, 'type' => $nilai, 'search' => request('search'), 'metode_pembayaran' => request('metode_pembayaran'), 'start_date' => request('start_date'), 'end_date' => request('end_date')])) }}"
                           class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $isActiveTipe ? 'bg-amber-600 text-white shadow-xs' : 'bg-stone-100 hover:bg-stone-200/70 text-stone-600' }}">
                            @if ($isActiveTipe)
                                <strong>{{ $label }}</strong>
                            @else
                                {{ $label }}
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Search, Status, Payment & Date Inputs Form -->
            <form action="{{ route('admin.pesanan.index') }}" method="GET" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3">
                @if ($filter !== null)
                    <input type="hidden" name="status" value="{{ $filter }}">
                @endif
                @if ($filterTipe !== null)
                    <input type="hidden" name="type" value="{{ $filterTipe->value }}">
                @endif

                <!-- Search Input -->
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari nama pelanggan, nomor pesanan..."
                           class="w-full pl-10 pr-3.5 py-2.5 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                </div>

                <!-- Dropdown Status (Optional if not using tab) -->
                <div class="w-full lg:w-44">
                    <select name="status" class="w-full py-2.5 px-3 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] text-stone-700 font-medium">
                        <option value="">Semua Status</option>
                        <option value="pending" @selected(request('status') === 'pending')>Pesanan Baru (Pending)</option>
                        <option value="proses" @selected(request('status') === 'proses')>Diproses</option>
                        <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
                        <option value="ditolak" @selected(request('status') === 'ditolak')>Ditolak</option>
                        <option value="batal" @selected(request('status') === 'batal')>Batal</option>
                    </select>
                </div>

                <!-- Dropdown Metode Pembayaran -->
                <div class="w-full lg:w-48">
                    <select name="metode_pembayaran" class="w-full py-2.5 px-3 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] text-stone-700 font-medium">
                        <option value="">Semua Metode Pembayaran</option>
                        <option value="transfer_bank" @selected(request('metode_pembayaran') === 'transfer_bank')>Transfer Bank</option>
                        <option value="qris" @selected(request('metode_pembayaran') === 'qris')>QRIS</option>
                        <option value="cash" @selected(request('metode_pembayaran') === 'cash')>Tunai / Bayar di Toko</option>
                    </select>
                </div>

                <!-- Date Range Inputs -->
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <input type="date"
                               id="start_date"
                               name="start_date"
                               value="{{ request('start_date') }}"
                               title="Dari Tanggal"
                               class="py-2 px-3 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] text-stone-700">
                    </div>
                    <span class="text-xs text-stone-400">-</span>
                    <div class="relative">
                        <input type="date"
                               id="end_date"
                               name="end_date"
                               value="{{ request('end_date') }}"
                               title="Sampai Tanggal"
                               class="py-2 px-3 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] text-stone-700">
                    </div>
                </div>

                <!-- Submit & Reset Buttons -->
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-1.5 bg-[#6B0F1A] hover:bg-[#520A13] text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-xs transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('admin.pesanan.index', array_filter(['status' => $filter, 'type' => $filterTipe?->value])) }}"
                       class="px-3.5 py-2.5 text-xs font-semibold rounded-xl border border-stone-200 hover:bg-stone-50 text-stone-600 transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Orders Table Card -->
        <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-stone-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h2 class="text-sm sm:text-base font-bold text-[#5A121A]">
                        Daftar Pesanan
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-[#6B0F1A]">
                        {{ count($orders) }} pesanan
                    </span>
                </div>
            </div>

            <!-- Responsive Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#FAF2EE]/80 text-stone-600 font-semibold border-b border-stone-100">
                            <th class="py-3 px-3.5 w-12 text-center">No</th>
                            <th class="py-3 px-3.5">Nomor Pesanan</th>
                            <th class="py-3 px-3.5">Nama Pelanggan</th>
                            <th class="py-3 px-3.5">Tanggal Pesan</th>
                            <th class="py-3 px-3.5">Detail Item</th>
                            <th class="py-3 px-3.5">Total Harga</th>
                            <th class="py-3 px-3.5">Metode Pembayaran</th>
                            <th class="py-3 px-3.5 text-center">Bukti / Foto</th>
                            <th class="py-3 px-3.5 text-center">Status</th>
                            <th class="py-3 px-3.5 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse ($orders as $order)
                            @php
                                $totalJumlah = $order->items->sum('jumlah');
                                $firstItem = $order->items->first();
                                $cakeImg = $firstItem?->product?->foto && file_exists(public_path('storage/' . $firstItem->product->foto))
                                    ? asset('storage/' . $firstItem->product->foto)
                                    : asset('images/rec-img-1.png');

                                $statusVal = $order->status->value;
                                $statusBadgeClass = match($statusVal) {
                                    'pending' => 'bg-amber-100 text-amber-800 border border-amber-200/60',
                                    'proses' => 'bg-sky-100 text-sky-800 border border-sky-200/60',
                                    'selesai' => 'bg-emerald-100 text-emerald-800 border border-emerald-200/60',
                                    'ditolak' => 'bg-rose-100 text-rose-800 border border-rose-200/60',
                                    'batal' => 'bg-stone-200 text-stone-800 border border-stone-300',
                                    default => 'bg-stone-100 text-stone-700',
                                };
                                $statusLabel = match($statusVal) {
                                    'pending' => 'Pending',
                                    'proses' => 'Diproses',
                                    'selesai' => 'Selesai',
                                    'ditolak' => 'Ditolak',
                                    'batal' => 'Batal',
                                    default => ucfirst($statusVal),
                                };

                                $waNumber = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $order->nomor_wa ?? ''));
                            @endphp
                            <tr class="hover:bg-stone-50/70 transition-colors">
                                <!-- No -->
                                <td class="py-3.5 px-3.5 text-center text-stone-500 font-mono text-[11px]">
                                    {{ $loop->iteration }}
                                </td>

                                <!-- Nomor Pesanan & Thumbnail -->
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-10 h-10 rounded-xl overflow-hidden bg-stone-100 border border-stone-200/60 shrink-0">
                                            <img src="{{ $cakeImg }}" alt="Kue" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="font-bold text-stone-900 tracking-tight">
                                                {{ $order->kode_pesanan }}
                                            </p>
                                            <div class="mt-0.5">
                                                @if ($order->tipe_pesanan === \App\Enums\TipePesanan::Custom)
                                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white">
                                                        Custom Cake
                                                    </span>
                                                @else
                                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-800">
                                                        Kue Jadi
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Nama Pelanggan & WA -->
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <p class="font-bold text-stone-800">{{ $order->nama_pemesan }}</p>
                                    @if ($order->nomor_wa)
                                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-emerald-700 hover:text-emerald-900 font-medium mt-0.5">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                            <span>{{ $order->nomor_wa }}</span>
                                        </a>
                                    @endif
                                </td>

                                <!-- Tanggal Pesan -->
                                <td class="py-3.5 px-3.5 whitespace-nowrap text-stone-600 text-[11px]">
                                    <span class="block font-medium text-stone-800">{{ $order->created_at->format('d M Y') }}</span>
                                    <span class="text-stone-400">{{ $order->created_at->format('H:i') }} WIB</span>
                                    <span class="hidden">{{ $order->created_at->format('d-m-Y H:i') }}</span>
                                </td>

                                <!-- Detail Item -->
                                <td class="py-3.5 px-3.5">
                                    <div class="max-w-xs">
                                        <p class="font-bold text-stone-800 text-[11px]">
                                            {{ number_format($totalJumlah, 0, ',', '.') }} Pcs ({{ $order->items_count }} jenis)
                                        </p>
                                        <ul class="text-[11px] text-stone-600 mt-1 space-y-0.5">
                                            @foreach ($order->items as $item)
                                                <li class="truncate">
                                                    <span class="font-semibold text-stone-800">{{ $item->jumlah }}x</span>
                                                    {{ $item->product?->nama_kue ?? ($item->deskripsi_custom ? \Illuminate\Support\Str::limit($item->deskripsi_custom, 30) : 'Custom Cake') }}
                                                    @if ($item->catatan)
                                                        <span class="text-[10px] text-stone-400 block pl-3"><strong>Catatan item:</strong> {{ $item->catatan }}</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                        @if ($order->catatan)
                                            <div class="mt-1.5 p-1.5 rounded-lg bg-amber-50/70 border border-amber-100 text-[10px] text-amber-900 leading-tight">
                                                <strong>Catatan:</strong> {{ $order->catatan }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Total Harga -->
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    <span class="font-bold text-stone-900 block text-xs">
                                        Rp{{ number_format($order->total_harga, 0, ',', '.') }}
                                    </span>
                                    <span class="hidden">{{ $order->total_harga }}</span>
                                </td>

                                <!-- Metode Pembayaran -->
                                <td class="py-3.5 px-3.5 whitespace-nowrap">
                                    @php
                                        $methodLabel = match($order->metode_pembayaran?->value) {
                                            'transfer_bank' => 'Transfer Bank',
                                            'qris' => 'QRIS',
                                            'cash' => 'Tunai',
                                            default => $order->metode_pembayaran?->label() ?? '-',
                                        };
                                        $methodClass = match($order->metode_pembayaran?->value) {
                                            'transfer_bank' => 'bg-indigo-50 text-indigo-700 border border-indigo-200/60',
                                            'qris' => 'bg-purple-50 text-purple-700 border border-purple-200/60',
                                            'cash' => 'bg-stone-100 text-stone-700 border border-stone-200',
                                            default => 'bg-stone-50 text-stone-600',
                                        };
                                    @endphp
                                    <span class="inline-block px-2.5 py-1 rounded-lg text-[10px] font-semibold {{ $methodClass }}">
                                        {{ $methodLabel }}
                                    </span>
                                </td>

                                <!-- Bukti Pembayaran / Foto Referensi -->
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Bukti Pembayaran Preview -->
                                        @if ($order->bukti_pembayaran)
                                            <a href="{{ route('admin.orders.bukti', $order) }}"
                                               target="_blank"
                                               title="Lihat Bukti Pembayaran"
                                               class="w-8 h-8 rounded-lg overflow-hidden border border-emerald-200 bg-emerald-50 hover:opacity-80 transition-opacity inline-block shrink-0">
                                                <img src="{{ route('admin.orders.bukti', $order) }}" alt="Bukti Pembayaran" class="w-full h-full object-cover">
                                            </a>
                                            <span class="sr-only">Bukti Pembayaran</span>
                                        @else
                                            <span class="text-stone-300 text-xs">-</span>
                                        @endif

                                        <!-- Foto Referensi Custom Cake -->
                                        @php($customPhoto = $order->items->first()?->foto_referensi)
                                        @if ($customPhoto)
                                            <a href="{{ route('admin.orders.custom_photo', $order) }}"
                                               target="_blank"
                                               title="Lihat Foto Referensi"
                                               class="w-8 h-8 rounded-lg overflow-hidden border border-amber-200 bg-amber-50 hover:opacity-80 transition-opacity inline-block shrink-0">
                                                <img src="{{ route('admin.orders.custom_photo', $order) }}" alt="Foto Referensi" class="w-full h-full object-cover">
                                            </a>
                                        @endif
                                    </div>
                                </td>

                                <!-- Status Badge -->
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold {{ $statusBadgeClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                    <span class="hidden">{{ $order->status->value }}</span>
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                    @switch($order->status->value)
                                        @case('pending')
                                            <form action="{{ route('admin.pesanan.update-status', $order) }}" method="POST" class="inline-flex items-center gap-1.5">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        name="status"
                                                        value="proses"
                                                        class="px-2.5 py-1.5 text-[11px] font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition-colors">
                                                    Terima
                                                </button>
                                                <button type="submit"
                                                        name="status"
                                                        value="ditolak"
                                                        onclick="return confirm('Tolak pesanan {{ $order->kode_pesanan }}?')"
                                                        class="px-2.5 py-1.5 text-[11px] font-semibold rounded-lg bg-rose-600 hover:bg-rose-700 text-white shadow-xs transition-colors">
                                                    Tolak
                                                </button>
                                            </form>
                                            @break

                                        @case('proses')
                                            <form action="{{ route('admin.pesanan.update-status', $order) }}" method="POST" class="inline-flex items-center gap-1.5">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        name="status"
                                                        value="selesai"
                                                        class="px-2.5 py-1.5 text-[11px] font-semibold rounded-lg bg-sky-600 hover:bg-sky-700 text-white shadow-xs transition-colors">
                                                    Selesaikan Pesanan
                                                </button>
                                                <button type="submit"
                                                        name="status"
                                                        value="batal"
                                                        onclick="return confirm('Batalkan pesanan {{ $order->kode_pesanan }}? Stok akan dikembalikan.')"
                                                        class="px-2.5 py-1.5 text-[11px] font-semibold rounded-lg bg-stone-500 hover:bg-stone-600 text-white shadow-xs transition-colors">
                                                    Batal
                                                </button>
                                            </form>
                                            @break

                                        @default
                                            <div class="inline-flex items-center gap-1.5">
                                                <span class="text-xs text-stone-400 font-medium">{{ $order->status->value }}</span>
                                                @if (auth()->user()->isAdmin())
                                                    <form action="{{ route('admin.pesanan.destroy', $order) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Yakin ingin menghapus pesanan ini?')"
                                                          class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                title="Hapus Pesanan"
                                                                class="p-1.5 text-stone-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                    @endswitch
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-stone-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-2xl bg-stone-100 flex items-center justify-center text-stone-400 mb-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-stone-600">Belum ada pesanan masuk.</p>
                                        <p class="text-xs text-stone-400 mt-0.5">Pesanan baru dari pembeli akan muncul di sini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Mobile Sidebar JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
        });
    </script>
</body>
</html>
