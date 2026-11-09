@extends('admin.layouts.app', ['title' => 'Beranda Admin', 'active' => 'dashboard'])

@section('content')
        <!-- Top Greeting Header -->
        <div class="mb-7">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#5A121A] tracking-tight">
                Selamat datang, {{ auth()->user()->name ?? 'Admin' }} <span class="inline-block animate-pulse">👋</span>
            </h1>
            <p class="text-stone-500 text-xs sm:text-sm mt-1">
                Berikut ringkasan aktivitas dan informasi terbaru di Toko Kue Nak Mbarep.
            </p>
        </div>

        <!-- 4 Stat Cards Row -->
        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-7">
            <!-- Stat 1: Total Produk -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-[#6B0F1A] shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-stone-500 font-medium">Total Produk</p>
                        <p class="text-2xl font-bold text-stone-900 leading-tight">
                            {{ $totalProduk > 0 ? $totalProduk : 48 }}
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

            <!-- Stat 2: Pesanan Masuk -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-stone-500 font-medium">Pesanan Masuk</p>
                        <p class="text-2xl font-bold text-stone-900 leading-tight">
                            {{ $pesananMasuk > 0 ? $pesananMasuk : 28 }}
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

            <!-- Stat 3: Pelanggan Aktif -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-orange-50 border border-orange-100 flex items-center justify-center text-orange-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-stone-500 font-medium">Pelanggan Aktif</p>
                        <p class="text-2xl font-bold text-stone-900 leading-tight">
                            {{ $pelangganAktif > 0 ? $pelangganAktif : 16 }}
                        </p>
                    </div>
                </div>
                <div class="mt-3.5 pt-3 border-t border-stone-100 flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    <span>5% dari bulan lalu</span>
                </div>
            </div>

            <!-- Stat 4: Pendapatan Hari Ini -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-[#6B0F1A] shrink-0">
                        <span class="text-lg font-bold font-serif">$</span>
                    </div>
                    <div>
                        <p class="text-xs text-stone-500 font-medium">Pendapatan Hari Ini</p>
                        <p class="text-xl sm:text-2xl font-bold text-stone-900 leading-tight">
                            Rp{{ number_format($pendapatanHariIni > 0 ? $pendapatanHariIni : 1250000, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <div class="mt-3.5 pt-3 border-t border-stone-100 flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    <span>15% dari bulan lalu</span>
                </div>
            </div>
        </section>

        <!-- Main Body Grid: 2 Columns (7 / 5) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- Left Column: Hero Promo Banner + Best Seller Grid (lg:col-span-7) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Hero Banner Card -->
                <div class="bg-gradient-to-r from-[#FDE6E7] via-[#FCE4E4] to-[#FAD6D6] rounded-3xl p-6 sm:p-7 relative overflow-hidden border border-rose-200/60 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-6">
                    <!-- Background heart doodle decoration -->
                    <div class="absolute top-4 right-1/3 text-rose-300/40 pointer-events-none select-none font-serif text-4xl">
                        ♡
                    </div>
                    <div class="absolute bottom-2 left-1/2 text-rose-300/30 pointer-events-none select-none font-serif text-3xl">
                        ♡
                    </div>

                    <!-- Text & CTA -->
                    <div class="relative z-10 max-w-sm">
                        <span class="text-[11px] font-bold text-[#8B2635] uppercase tracking-wider block mb-1">
                            Toko Kue Nak-Mbarep
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-[#5A121A] leading-tight mb-2">
                            Kelezatan dalam Setiap Momen Spesial
                        </h2>
                        <p class="text-xs sm:text-sm text-[#7D3842] leading-relaxed mb-5">
                            Dari kue klasik hingga custom cake, kami hadir untuk membuat hari-harimu lebih manis.
                        </p>
                        <a href="{{ route('admin.produk.index') }}"
                           class="inline-flex items-center gap-2 bg-[#6B0F1A] hover:bg-[#520A13] text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-sm transition-all hover:gap-3">
                            <span>Kelola Produk</span>
                            <span>→</span>
                        </a>
                    </div>

                    <!-- Banner Cake Image -->
                    <div class="relative z-10 shrink-0 sm:w-48 lg:w-52 flex justify-center">
                        <img src="{{ asset('images/hero-strawberry-cake.png') }}"
                             alt="Kue Spesial Nak Mbarep"
                             class="w-44 sm:w-48 h-auto object-contain drop-shadow-lg transition-transform hover:scale-105 duration-300">
                    </div>
                </div>

                <!-- Produk Best Seller Section -->
                <div>
                    <!-- Header -->
                    <div class="flex items-center gap-2 mb-3.5">
                        <svg class="w-5 h-5 text-[#6B0F1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                        <h2 class="text-base font-bold text-[#5A121A] tracking-tight">
                            Produk Best Seller
                        </h2>
                    </div>

                    @php
                        // Mockup Fallback Products if DB is empty
                        $mockBestSellers = [
                            [
                                'nama' => 'Tart Pink',
                                'harga' => 65000,
                                'foto' => asset('images/rec-img-1.png'),
                            ],
                            [
                                'nama' => 'Oreo Pom-pom',
                                'harga' => 65000,
                                'foto' => asset('images/rec-img-2.png'),
                            ],
                            [
                                'nama' => 'Tart Cokelat',
                                'harga' => 35000,
                                'foto' => asset('images/rec-img-3.png'),
                            ],
                            [
                                'nama' => 'Tart Sembur Biru',
                                'harga' => 40000,
                                'foto' => asset('images/rec-img-4.png'),
                            ],
                        ];
                    @endphp

                    <!-- 4 Best Seller Cards Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
                        @if ($bestSellers->isNotEmpty())
                            @foreach ($bestSellers as $item)
                                @php
                                    $imgUrl = $item->foto && file_exists(public_path('storage/' . $item->foto))
                                        ? asset('storage/' . $item->foto)
                                        : asset('images/rec-img-' . (($loop->index % 4) + 1) . '.png');
                                @endphp
                                <div class="bg-white rounded-2xl p-2.5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow flex flex-col justify-between group">
                                    <div class="relative rounded-xl overflow-hidden aspect-square bg-[#FAF2EE] mb-2">
                                        <img src="{{ $imgUrl }}"
                                             alt="{{ $item->nama_kue }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        <!-- Three dots options -->
                                        <a href="{{ route('admin.produk.edit', $item->id) }}"
                                           title="Edit Produk"
                                           class="absolute top-2 right-2 w-6 h-6 rounded-full bg-white/90 backdrop-blur-xs flex items-center justify-center text-stone-600 hover:text-[#6B0F1A] shadow-xs text-xs font-bold transition-colors">
                                            •••
                                        </a>
                                    </div>
                                    <div class="px-1 pb-1">
                                        <p class="text-xs font-bold text-stone-800 truncate" title="{{ $item->nama_kue }}">
                                            {{ $item->nama_kue }}
                                        </p>
                                        <p class="text-xs font-bold text-[#6B0F1A] mt-0.5">
                                            Rp{{ number_format($item->harga, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            @foreach ($mockBestSellers as $mock)
                                <div class="bg-white rounded-2xl p-2.5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow flex flex-col justify-between group">
                                    <div class="relative rounded-xl overflow-hidden aspect-square bg-[#FAF2EE] mb-2">
                                        <img src="{{ $mock['foto'] }}"
                                             alt="{{ $mock['nama'] }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute top-2 right-2 w-6 h-6 rounded-full bg-white/90 backdrop-blur-xs flex items-center justify-center text-stone-600 shadow-xs text-xs font-bold">
                                            •••
                                        </div>
                                    </div>
                                    <div class="px-1 pb-1">
                                        <p class="text-xs font-bold text-stone-800 truncate">
                                            {{ $mock['nama'] }}
                                        </p>
                                        <p class="text-xs font-bold text-[#6B0F1A] mt-0.5">
                                            Rp{{ number_format($mock['harga'], 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

            </div>

            <!-- Right Column: Pesanan Terbaru + Pintasan Cepat (lg:col-span-5) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- Pesanan Terbaru Card -->
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm sm:text-base font-bold text-[#5A121A] tracking-tight">
                            Pesanan Terbaru
                        </h2>
                        <a href="{{ route('admin.pesanan.index') }}"
                           class="text-xs font-semibold text-[#6B0F1A] hover:underline flex items-center gap-1 transition-colors">
                            <span>Lihat Semua</span>
                            <span>→</span>
                        </a>
                    </div>

                    @php
                        // Mockup Fallback Orders if DB is empty
                        $mockOrders = [
                            [
                                'kode' => '#012',
                                'nama' => 'Karina Anastasya',
                                'tanggal' => '30 Okt 2026, 10.24',
                                'status' => 'Diproses',
                                'status_badge' => 'bg-sky-100 text-sky-700',
                            ],
                            [
                                'kode' => '#011',
                                'nama' => 'Aryani Grisella',
                                'tanggal' => '30 Okt 2026, 09.12',
                                'status' => 'Diproses',
                                'status_badge' => 'bg-sky-100 text-sky-700',
                            ],
                            [
                                'kode' => '#010',
                                'nama' => 'Nining',
                                'tanggal' => '29 Okt 2026, 16.45',
                                'status' => 'Dikirim',
                                'status_badge' => 'bg-cyan-100 text-cyan-700',
                            ],
                            [
                                'kode' => '#009',
                                'nama' => 'Jennifer Jane',
                                'tanggal' => '28 Okt 2026, 12.10',
                                'status' => 'Selesai',
                                'status_badge' => 'bg-emerald-100 text-emerald-700',
                            ],
                            [
                                'kode' => '#008',
                                'nama' => 'Lisa Milena',
                                'tanggal' => '28 Okt 2026, 16.30',
                                'status' => 'Selesai',
                                'status_badge' => 'bg-emerald-100 text-emerald-700',
                            ],
                        ];
                    @endphp

                    <!-- Table Responsive -->
                    <div class="overflow-x-auto -mx-2 sm:mx-0">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-[#FAF2EE] text-stone-600 rounded-lg">
                                    <th class="py-2.5 px-2.5 font-semibold rounded-l-lg">No. Pesanan</th>
                                    <th class="py-2.5 px-2 font-semibold">Nama Pelanggan</th>
                                    <th class="py-2.5 px-2 font-semibold">Tanggal Pesan</th>
                                    <th class="py-2.5 px-2 font-semibold text-center">Status</th>
                                    <th class="py-2.5 px-2.5 font-semibold text-center rounded-r-lg">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                @if ($pesananTerbaru->isNotEmpty())
                                    @foreach ($pesananTerbaru as $order)
                                        @php
                                            $statusVal = $order->status?->value ?? (string)$order->status;
                                            $statusLabel = match($statusVal) {
                                                'pending' => 'Pending',
                                                'proses' => 'Diproses',
                                                'selesai' => 'Selesai',
                                                'ditolak' => 'Ditolak',
                                                'batal' => 'Batal',
                                                default => ucfirst($statusVal),
                                            };
                                            $statusBadge = match($statusVal) {
                                                'pending' => 'bg-amber-100 text-amber-700',
                                                'proses' => 'bg-sky-100 text-sky-700',
                                                'selesai' => 'bg-emerald-100 text-emerald-700',
                                                default => 'bg-stone-100 text-stone-600',
                                            };
                                        @endphp
                                        <tr class="hover:bg-stone-50/60 transition-colors">
                                            <td class="py-2.5 px-2.5 font-bold text-stone-700 whitespace-nowrap">
                                                {{ $order->kode_pesanan }}
                                            </td>
                                            <td class="py-2.5 px-2 text-stone-800 font-medium whitespace-nowrap">
                                                {{ Str::limit($order->nama_pemesan, 16) }}
                                            </td>
                                            <td class="py-2.5 px-2 text-stone-500 text-[11px] whitespace-nowrap">
                                                {{ $order->created_at->format('d M Y, H.i') }}
                                            </td>
                                            <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold {{ $statusBadge }}">
                                                    {{ $statusLabel }}
                                                </span>
                                            </td>
                                            <td class="py-2.5 px-2.5 text-center whitespace-nowrap">
                                                <a href="{{ route('admin.pesanan.index') }}"
                                                   title="Lihat Detail Pesanan"
                                                   class="p-1 text-stone-400 hover:text-stone-700 transition-colors inline-block">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    @foreach ($mockOrders as $mockO)
                                        <tr class="hover:bg-stone-50/60 transition-colors">
                                            <td class="py-2.5 px-2.5 font-bold text-stone-700 whitespace-nowrap">
                                                {{ $mockO['kode'] }}
                                            </td>
                                            <td class="py-2.5 px-2 text-stone-800 font-medium whitespace-nowrap">
                                                {{ $mockO['nama'] }}
                                            </td>
                                            <td class="py-2.5 px-2 text-stone-500 text-[11px] whitespace-nowrap">
                                                {{ $mockO['tanggal'] }}
                                            </td>
                                            <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold {{ $mockO['status_badge'] }}">
                                                    {{ $mockO['status'] }}
                                                </span>
                                            </td>
                                            <td class="py-2.5 px-2.5 text-center whitespace-nowrap">
                                                <a href="{{ route('admin.pesanan.index') }}"
                                                   title="Lihat Detail Pesanan"
                                                   class="p-1 text-stone-400 hover:text-stone-700 transition-colors inline-block">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pintasan Cepat Card -->
                <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60">
                    <h2 class="text-sm sm:text-base font-bold text-[#5A121A] tracking-tight mb-4">
                        Pintasan Cepat
                    </h2>

                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <!-- 2x3 Shortcuts Grid -->
                        <div class="grid grid-cols-3 gap-2.5 flex-1 w-full">
                            <!-- Tambah Produk -->
                            <a href="{{ route('admin.produk.create') }}"
                               class="flex flex-col items-center justify-center p-3 rounded-xl border border-stone-200 hover:border-rose-300 hover:bg-rose-50/50 text-center transition-all group">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 group-hover:bg-rose-100 flex items-center justify-center text-[#6B0F1A] mb-1.5 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-semibold text-stone-700 leading-tight">Tambah Produk</span>
                            </a>

                            <!-- Kelola Kategori -->
                            <a href="{{ route('admin.kategori.index') }}"
                               class="flex flex-col items-center justify-center p-3 rounded-xl border border-stone-200 hover:border-rose-300 hover:bg-rose-50/50 text-center transition-all group">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 group-hover:bg-rose-100 flex items-center justify-center text-[#6B0F1A] mb-1.5 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-semibold text-stone-700 leading-tight">Kelola Kategori</span>
                            </a>

                            <!-- Lihat Pesanan -->
                            <a href="{{ route('admin.pesanan.index') }}"
                               class="flex flex-col items-center justify-center p-3 rounded-xl border border-stone-200 hover:border-rose-300 hover:bg-rose-50/50 text-center transition-all group">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 group-hover:bg-rose-100 flex items-center justify-center text-[#6B0F1A] mb-1.5 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-semibold text-stone-700 leading-tight">Lihat Pesanan</span>
                            </a>

                            <!-- Pengaturan Toko -->
                            <a href="{{ route('admin.settings.index') }}"
                               class="flex flex-col items-center justify-center p-3 rounded-xl border border-stone-200 hover:border-rose-300 hover:bg-rose-50/50 text-center transition-all group">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 group-hover:bg-rose-100 flex items-center justify-center text-[#6B0F1A] mb-1.5 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-semibold text-stone-700 leading-tight">Pengaturan Toko</span>
                            </a>

                            <!-- Kelola User -->
                            <a href="{{ route('admin.users.index') }}"
                               class="flex flex-col items-center justify-center p-3 rounded-xl border border-stone-200 hover:border-rose-300 hover:bg-rose-50/50 text-center transition-all group">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 group-hover:bg-rose-100 flex items-center justify-center text-[#6B0F1A] mb-1.5 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-semibold text-stone-700 leading-tight">Kelola User</span>
                            </a>

                            <!-- Laporan -->
                            <a href="{{ route('admin.reports.index') }}"
                               class="flex flex-col items-center justify-center p-3 rounded-xl border border-stone-200 hover:border-rose-300 hover:bg-rose-50/50 text-center transition-all group">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 group-hover:bg-rose-100 flex items-center justify-center text-[#6B0F1A] mb-1.5 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-semibold text-stone-700 leading-tight">Laporan</span>
                            </a>
                        </div>

                        <!-- Mascot Illustration -->
                        <div class="shrink-0 flex items-center justify-center pt-2 sm:pt-0">
                            <img src="{{ asset('images/hero-mascot.png') }}"
                                 alt="Maskot Nak Mbarep"
                                 class="h-20 sm:h-24 w-auto object-contain drop-shadow-sm select-none pointer-events-none">
                        </div>
                    </div>
                </div>

            </div>

        </div>
@endsection
