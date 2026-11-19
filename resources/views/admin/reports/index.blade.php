@extends('admin.layouts.app', ['title' => 'Laporan Penjualan', 'active' => 'reports'])

@section('content')
    <div class="print:m-0 space-y-6">
        <!-- Header & Filter Bar -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-[#5A121A] tracking-tight">
                        Laporan {{ $tab === 'stok' ? 'Pergerakan Stok' : 'Penjualan' }}
                    </h1>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-[#6B0F1A]/10 text-[#6B0F1A] border border-[#6B0F1A]/20">
                        {{ ucfirst($periode) }}
                    </span>
                </div>
                <p class="text-stone-500 text-xs sm:text-sm mt-1">
                    Pantau kinerja penjualan, omzet toko, dan pergerakan stok barang Nak Mbarep secara dinamis.
                </p>
            </div>

            <!-- Periode Filter Buttons & Print Action -->
            <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
                <!-- Segmented Periode Selector -->
                <div class="inline-flex p-1 rounded-xl bg-white border border-stone-200/80 shadow-xs text-xs font-semibold">
                    <a href="{{ route('admin.reports.index', ['periode' => 'harian', 'tab' => $tab]) }}"
                       class="px-3.5 py-1.5 rounded-lg transition-all {{ $periode === 'harian' ? 'bg-[#6B0F1A] text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-50' }}">
                        Harian
                    </a>
                    <a href="{{ route('admin.reports.index', ['periode' => 'mingguan', 'tab' => $tab]) }}"
                       class="px-3.5 py-1.5 rounded-lg transition-all {{ $periode === 'mingguan' ? 'bg-[#6B0F1A] text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-50' }}">
                        Mingguan
                    </a>
                    <a href="{{ route('admin.reports.index', ['periode' => 'bulanan', 'tab' => $tab]) }}"
                       class="px-3.5 py-1.5 rounded-lg transition-all {{ $periode === 'bulanan' ? 'bg-[#6B0F1A] text-white shadow-xs' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-50' }}">
                        Bulanan
                    </a>
                </div>

                <!-- Custom Date Range Form Toggle -->
                <details class="relative">
                    <summary class="list-none inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white border border-stone-200/80 hover:bg-stone-50 text-stone-700 text-xs font-semibold shadow-xs cursor-pointer select-none">
                        <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Pilih Tanggal</span>
                        <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>

                    <div class="absolute right-0 mt-2 w-72 p-4 bg-white rounded-2xl shadow-xl border border-stone-200/80 z-30 space-y-3">
                        <form method="GET" action="{{ route('admin.reports.index') }}" class="space-y-3">
                            <input type="hidden" name="tab" value="{{ $tab }}">
                            <input type="hidden" name="periode" value="kustom">

                            <div>
                                <label class="block text-[11px] font-bold text-stone-600 uppercase mb-1">Mulai Dari</label>
                                <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}"
                                       class="w-full text-xs px-3 py-2 border border-stone-200 rounded-xl focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-stone-600 uppercase mb-1">Sampai Dengan</label>
                                <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                                       class="w-full text-xs px-3 py-2 border border-stone-200 rounded-xl focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                            </div>
                            <button type="submit"
                                    class="w-full py-2 bg-[#6B0F1A] hover:bg-[#520A13] text-white text-xs font-semibold rounded-xl shadow-xs transition-colors text-center">
                                Terapkan Filter
                            </button>
                        </form>
                    </div>
                </details>

                <!-- Tombol Cetak (Print) -->
                <button type="button" onclick="window.print()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-stone-200/80 hover:bg-stone-50 text-stone-700 text-xs font-semibold shadow-xs transition-colors print:hidden">
                    <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak Laporan</span>
                </button>
            </div>
        </div>

        <!-- Range Info Banner -->
        <div class="flex items-center justify-between p-3.5 sm:px-4 rounded-xl bg-amber-50/70 border border-amber-200/60 text-amber-900 text-xs font-medium">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Menampilkan data periode: <strong class="font-bold text-[#6B0F1A]">{{ $periodeLabel }}</strong></span>
            </div>
            @if ($periode === 'kustom')
                <a href="{{ route('admin.reports.index', ['periode' => 'bulanan', 'tab' => $tab]) }}"
                   class="text-amber-700 hover:text-amber-900 underline text-[11px] font-semibold">
                    Reset ke Bulanan
                </a>
            @endif
        </div>

        <!-- Tab Navigasi: Laporan Penjualan vs Laporan Stok -->
        <div class="border-b border-stone-200/80 print:hidden">
            <nav class="flex gap-4 sm:gap-8 -mb-px" aria-label="Tabs">
                <a href="{{ route('admin.reports.index', ['tab' => 'penjualan', 'periode' => $periode, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                   class="inline-flex items-center gap-2.5 py-3 border-b-2 text-sm font-bold transition-all {{ $tab === 'penjualan' ? 'border-[#6B0F1A] text-[#6B0F1A]' : 'border-transparent text-stone-500 hover:text-stone-800 hover:border-stone-300' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Laporan Penjualan</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] {{ $tab === 'penjualan' ? 'bg-[#6B0F1A] text-white' : 'bg-stone-200 text-stone-700' }}">
                        {{ $totalTransaksi }} Transaksi
                    </span>
                </a>

                <a href="{{ route('admin.reports.index', ['tab' => 'stok', 'periode' => $periode, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                   class="inline-flex items-center gap-2.5 py-3 border-b-2 text-sm font-bold transition-all {{ $tab === 'stok' ? 'border-[#6B0F1A] text-[#6B0F1A]' : 'border-transparent text-stone-500 hover:text-stone-800 hover:border-stone-300' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span>Laporan Stok</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] {{ $tab === 'stok' ? 'bg-[#6B0F1A] text-white' : 'bg-stone-200 text-stone-700' }}">
                        {{ $totalSisaStok }} pcs
                    </span>
                </a>
            </nav>
        </div>

        <!-- 4 Top Stat Cards (Sesuai Ketentuan Feature Request: Omzet, Produk Terjual, Sisa Stok, dll) -->
        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <!-- Stat 1: Total Omzet -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-[#6B0F1A] shrink-0 font-bold text-lg">
                        Rp
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-stone-500 font-medium">Total Omzet</p>
                        <p class="text-xl sm:text-2xl font-extrabold text-stone-900 leading-tight truncate">
                            Rp{{ number_format($totalOmzet, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <div class="mt-3.5 pt-3 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                    <span>Pendapatan periode terpilih</span>
                    <span class="font-semibold text-[#6B0F1A]">{{ $totalTransaksi }} pesanan</span>
                </div>
            </div>

            <!-- Stat 2: Produk Terjual -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-stone-500 font-medium">Produk Terjual</p>
                        <p class="text-xl sm:text-2xl font-extrabold text-stone-900 leading-tight truncate">
                            {{ number_format($totalProdukTerjual, 0, ',', '.') }} <span class="text-sm font-semibold text-stone-500">pcs</span>
                        </p>
                    </div>
                </div>
                <div class="mt-3.5 pt-3 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                    <span>Total kuantitas barang keluar</span>
                    <span class="font-semibold text-emerald-600">Stok Keluar</span>
                </div>
            </div>

            <!-- Stat 3: Sisa Stok Fisik -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-stone-500 font-medium">Total Sisa Stok</p>
                        <p class="text-xl sm:text-2xl font-extrabold text-stone-900 leading-tight truncate">
                            {{ number_format($totalSisaStok, 0, ',', '.') }} <span class="text-sm font-semibold text-stone-500">pcs</span>
                        </p>
                    </div>
                </div>
                <div class="mt-3.5 pt-3 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                    <span>Stok tersedia saat ini</span>
                    <span class="font-semibold {{ $totalProdukHabis > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                        {{ $totalProdukHabis }} kritis / habis
                    </span>
                </div>
            </div>

            <!-- Stat 4: Kontekstual Berdasarkan Tab -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                @if ($tab === 'stok')
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-700 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-stone-500 font-medium">Stok Masuk (Restok)</p>
                            <p class="text-xl sm:text-2xl font-extrabold text-stone-900 leading-tight truncate">
                                +{{ number_format($totalStokMasuk, 0, ',', '.') }} <span class="text-sm font-semibold text-stone-500">pcs</span>
                            </p>
                        </div>
                    </div>
                    <div class="mt-3.5 pt-3 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                        <span>Penambahan di periode ini</span>
                        <span class="font-semibold text-teal-700">{{ $totalProdukReady }} produk ready</span>
                    </div>
                @else
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-700 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs text-stone-500 font-medium">Rata-rata Transaksi</p>
                            <p class="text-xl sm:text-2xl font-extrabold text-stone-900 leading-tight truncate">
                                Rp{{ number_format($rataRataTransaksi, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-3.5 pt-3 border-t border-stone-100 flex items-center justify-between text-[11px] text-stone-500">
                        <span>Nilai pesanan per transaksi</span>
                        <span class="font-semibold text-indigo-700">{{ $totalTransaksi }} pesanan</span>
                    </div>
                @endif
            </div>
        </section>

        <!-- KONTEN TAB PENJUALAN -->
        @if ($tab === 'penjualan')
            <!-- Bagian Grafik Penjualan & Breakdown Pembayaran -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Grafik Utama Omzet & Penjualan -->
                <div class="lg:col-span-8 bg-white rounded-2xl p-5 sm:p-6 shadow-xs border border-stone-200/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                        <div>
                            <h2 class="text-base font-bold text-[#5A121A] tracking-tight">
                                Grafik Tren Penjualan & Omzet
                            </h2>
                            <p class="text-xs text-stone-500 mt-0.5">
                                Visualisasi omzet pendapatan sesuai filter periode ({{ ucfirst($periode) }})
                            </p>
                        </div>
                        <div class="flex items-center gap-3 text-xs">
                            <span class="inline-flex items-center gap-1.5 font-semibold text-stone-600">
                                <span class="w-3 h-3 rounded bg-[#6B0F1A]"></span>
                                <span>Omzet (Rp)</span>
                            </span>
                        </div>
                    </div>

                    <!-- SVG Bar Chart -->
                    @php
                        $chartPoints = $salesChart['points'];
                        $maxOmzet = max($salesChart['maxOmzet'], 1);
                    @endphp

                    @if (count($chartPoints) > 0 && $salesChart['maxOmzet'] > 0)
                        <div class="overflow-x-auto pb-2 custom-scrollbar">
                            <div class="min-w-[480px] h-64 flex items-end gap-2 sm:gap-3 pt-6 pb-2 border-b border-stone-200">
                                @foreach ($chartPoints as $point)
                                    @php
                                        $heightPercent = round(($point['omzet'] / $maxOmzet) * 100);
                                        $barHeight = max($heightPercent, $point['omzet'] > 0 ? 8 : 2);
                                    @endphp
                                    <div class="flex-1 flex flex-col items-center justify-end h-full group relative">
                                        <!-- Tooltip Hover -->
                                        <div class="absolute bottom-full mb-2 hidden group-hover:flex flex-col items-center z-20 pointer-events-none">
                                            <div class="bg-stone-900 text-white text-[11px] rounded-lg py-1 px-2.5 shadow-lg whitespace-nowrap text-center">
                                                <p class="font-bold">{{ $point['label'] }}</p>
                                                <p class="text-amber-300">Rp{{ number_format($point['omzet'], 0, ',', '.') }}</p>
                                                <p class="text-stone-300">{{ $point['terjual'] }} pcs ({{ $point['transaksi'] }} trx)</p>
                                            </div>
                                            <div class="w-2 h-2 bg-stone-900 rotate-45 -mt-1"></div>
                                        </div>

                                        <!-- Bar -->
                                        <div class="w-full max-w-[36px] rounded-t-lg transition-all duration-300 {{ $point['omzet'] > 0 ? 'bg-gradient-to-t from-[#6B0F1A] to-[#8B2635] group-hover:from-[#520A13] group-hover:to-[#6B0F1A]' : 'bg-stone-100 group-hover:bg-stone-200' }}"
                                             style="height: {{ $barHeight }}%;">
                                        </div>

                                        <!-- Label Sumbu X -->
                                        <span class="text-[10px] sm:text-[11px] font-medium text-stone-500 mt-2 truncate max-w-full text-center" title="{{ $point['label'] }}">
                                            {{ $point['short_label'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <!-- Empty State Chart -->
                        <div class="h-56 flex flex-col items-center justify-center text-center p-6 bg-stone-50/50 rounded-xl border border-dashed border-stone-200">
                            <div class="w-10 h-10 rounded-full bg-rose-50 text-[#6B0F1A] flex items-center justify-center mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-stone-700">Belum ada transaksi penjualan pada periode {{ $periodeLabel }}</p>
                            <p class="text-[11px] text-stone-400 mt-0.5">Grafik penjualan akan otomatis muncul setelah ada pesanan berstatus selesai atau diproses.</p>
                        </div>
                    @endif
                </div>

                <!-- Ringkasan Metode Pembayaran -->
                <div class="lg:col-span-4 bg-white rounded-2xl p-5 sm:p-6 shadow-xs border border-stone-200/60 flex flex-col justify-between">
                    <div>
                        <h2 class="text-base font-bold text-[#5A121A] tracking-tight">
                            Metode Pembayaran
                        </h2>
                        <p class="text-xs text-stone-500 mt-0.5">Distribusi pembayaran pesanan</p>

                        <div class="mt-5 space-y-4">
                            @php
                                $totalTrx = max($totalTransaksi, 1);
                            @endphp

                            @forelse ($metodePembayaran as $metodeName => $dataMetode)
                                @php
                                    $pct = round(($dataMetode['count'] / $totalTrx) * 100);
                                @endphp
                                <div>
                                    <div class="flex justify-between text-xs font-semibold mb-1">
                                        <span class="text-stone-700">{{ $metodeName }}</span>
                                        <span class="text-stone-900">Rp{{ number_format($dataMetode['total'], 0, ',', '.') }} ({{ $pct }}%)</span>
                                    </div>
                                    <div class="w-full bg-stone-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-[#6B0F1A] h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <p class="text-[10px] text-stone-400 mt-0.5">{{ $dataMetode['count'] }} kali transaksi</p>
                                </div>
                            @empty
                                <div class="py-8 text-center text-xs text-stone-400">
                                    Belum ada transaksi pada periode ini.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-stone-100 bg-[#FAF2EE]/60 -mx-5 -mb-5 sm:-mx-6 sm:-mb-6 p-4 rounded-b-2xl">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-stone-600 font-medium">Total Pesanan Diproses:</span>
                            <span class="font-extrabold text-[#5A121A]">{{ $totalTransaksi }} pesanan</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel 1: Ringkasan Penjualan per Produk -->
            <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h2 class="text-base font-bold text-[#5A121A] tracking-tight">
                            Produk Terlaris & Penjualan per Produk
                        </h2>
                        <p class="text-xs text-stone-500 mt-0.5">Rincian produk yang terjual dalam periode {{ $periodeLabel }}</p>
                    </div>
                    <span class="text-xs text-stone-500 font-medium">
                        Total {{ $productSales->count() }} jenis produk terjual
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-stone-50 text-[11px] font-bold text-stone-600 uppercase tracking-wider border-b border-stone-200/60">
                                <th class="py-3 px-4 sm:px-6">No</th>
                                <th class="py-3 px-4">Nama Produk</th>
                                <th class="py-3 px-4">Kategori</th>
                                <th class="py-3 px-4 text-right">Harga Satuan</th>
                                <th class="py-3 px-4 text-center">Terjual</th>
                                <th class="py-3 px-4 text-right">Total Omzet</th>
                                <th class="py-3 px-4 sm:px-6 text-center">Kontribusi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-xs">
                            @forelse ($productSales as $index => $prod)
                                @php
                                    $kontribusi = $totalOmzet > 0 ? round(($prod['omzet'] / $totalOmzet) * 100, 1) : 0;
                                @endphp
                                <tr class="hover:bg-stone-50/60 transition-colors">
                                    <td class="py-3.5 px-4 sm:px-6 font-semibold text-stone-500">{{ $index + 1 }}</td>
                                    <td class="py-3.5 px-4 font-bold text-stone-900">
                                        {{ $prod['nama_kue'] }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-stone-100 text-stone-700">
                                            {{ $prod['kategori'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right text-stone-700">
                                        Rp{{ number_format($prod['harga'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-[#6B0F1A]">
                                        {{ number_format($prod['terjual'], 0, ',', '.') }} pcs
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-bold text-stone-900">
                                        Rp{{ number_format($prod['omzet'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 sm:px-6 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="w-16 bg-stone-100 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-[#6B0F1A] h-1.5 rounded-full" style="width: {{ $kontribusi }}%"></div>
                                            </div>
                                            <span class="text-[10px] font-semibold text-stone-600">{{ $kontribusi }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-10 px-6 text-center text-stone-400">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-8 h-8 text-stone-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                            <p class="font-medium">Belum ada data penjualan produk untuk periode ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel 2: Rincian Transaksi Pesanan dalam Periode -->
            <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-stone-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-[#5A121A] tracking-tight">
                            Rincian Transaksi Penjualan
                        </h2>
                        <p class="text-xs text-stone-500 mt-0.5">Daftar transaksi pesanan berstatus valid pada periode {{ $periodeLabel }}</p>
                    </div>
                    <span class="text-xs font-semibold text-stone-600">
                        Total: {{ $salesOrders->count() }} transaksi
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-stone-50 text-[11px] font-bold text-stone-600 uppercase tracking-wider border-b border-stone-200/60">
                                <th class="py-3 px-4 sm:px-6">Kode Pesanan</th>
                                <th class="py-3 px-4">Tanggal & Jam</th>
                                <th class="py-3 px-4">Pemesan</th>
                                <th class="py-3 px-4">Tipe & Pembayaran</th>
                                <th class="py-3 px-4 text-center">Item</th>
                                <th class="py-3 px-4 text-right">Total Belanja</th>
                                <th class="py-3 px-4 sm:px-6 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-xs">
                            @forelse ($salesOrders as $order)
                                <tr class="hover:bg-stone-50/60 transition-colors">
                                    <td class="py-3.5 px-4 sm:px-6 font-bold text-[#6B0F1A]">
                                        #{{ $order->kode_pesanan }}
                                    </td>
                                    <td class="py-3.5 px-4 text-stone-600 whitespace-nowrap">
                                        {{ $order->created_at?->format('d/m/Y H:i') ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-4 font-medium text-stone-900">
                                        {{ $order->nama_pemesan }}
                                    </td>
                                    <td class="py-3.5 px-4 text-stone-600">
                                        <div class="flex items-center gap-1.5">
                                            <span class="capitalize">{{ $order->tipe_pesanan?->value ?? 'Reguler' }}</span>
                                            <span class="text-stone-300">•</span>
                                            <span class="font-medium text-stone-800">{{ $order->metode_pembayaran?->label() ?? 'Cash' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-semibold text-stone-700">
                                        {{ $order->items->sum('jumlah') }} pcs
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-bold text-stone-900 whitespace-nowrap">
                                        Rp{{ number_format($order->total_harga, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 sm:px-6 text-center">
                                        <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold {{ $order->status === \App\Enums\StatusPesanan::Selesai ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-sky-50 text-sky-700 border border-sky-200' }}">
                                            {{ $order->status->label() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 px-6 text-center text-stone-400">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-8 h-8 text-stone-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <p class="font-semibold text-stone-700">Belum ada transaksi pesanan</p>
                                            <p class="text-xs text-stone-400 mt-0.5">Tidak ada riwayat transaksi pada rentang periode yang dipilih.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        <!-- KONTEN TAB LAPORAN STOK -->
        @else
            <!-- Bagian Grafik Pergerakan Stok (Stok Masuk vs Stok Keluar) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Grafik Perbandingan Stok -->
                <div class="lg:col-span-8 bg-white rounded-2xl p-5 sm:p-6 shadow-xs border border-stone-200/60">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
                        <div>
                            <h2 class="text-base font-bold text-[#5A121A] tracking-tight">
                                Grafik Pergerakan Stok Barang
                            </h2>
                            <p class="text-xs text-stone-500 mt-0.5">
                                Perbandingan stok keluar (terjual) dan stok masuk (restok/produk baru) periode {{ ucfirst($periode) }}
                            </p>
                        </div>
                        <div class="flex items-center gap-4 text-xs font-semibold">
                            <span class="inline-flex items-center gap-1.5 text-rose-700">
                                <span class="w-3 h-3 rounded bg-rose-600"></span>
                                <span>Stok Keluar</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-emerald-700">
                                <span class="w-3 h-3 rounded bg-emerald-600"></span>
                                <span>Stok Masuk</span>
                            </span>
                        </div>
                    </div>

                    @php
                        $stockPoints = $stockChart['points'];
                        $maxStock = max($stockChart['maxMovement'], 1);
                    @endphp

                    @if (count($stockPoints) > 0 && $stockChart['maxMovement'] > 0)
                        <div class="overflow-x-auto pb-2 custom-scrollbar">
                            <div class="min-w-[480px] h-64 flex items-end gap-2 sm:gap-3 pt-6 pb-2 border-b border-stone-200">
                                @foreach ($stockPoints as $pt)
                                    @php
                                        $hKeluar = round(($pt['keluar'] / $maxStock) * 100);
                                        $hMasuk = round(($pt['masuk'] / $maxStock) * 100);
                                        $barHKeluar = max($hKeluar, $pt['keluar'] > 0 ? 8 : 2);
                                        $barHMasuk = max($hMasuk, $pt['masuk'] > 0 ? 8 : 2);
                                    @endphp
                                    <div class="flex-1 flex flex-col items-center justify-end h-full group relative">
                                        <!-- Tooltip Hover -->
                                        <div class="absolute bottom-full mb-2 hidden group-hover:flex flex-col items-center z-20 pointer-events-none">
                                            <div class="bg-stone-900 text-white text-[11px] rounded-lg py-1 px-2.5 shadow-lg whitespace-nowrap text-center">
                                                <p class="font-bold">{{ $pt['label'] }}</p>
                                                <p class="text-rose-300">Keluar: -{{ $pt['keluar'] }} pcs</p>
                                                <p class="text-emerald-300">Masuk: +{{ $pt['masuk'] }} pcs</p>
                                            </div>
                                            <div class="w-2 h-2 bg-stone-900 rotate-45 -mt-1"></div>
                                        </div>

                                        <!-- Dual Bars Container -->
                                        <div class="w-full flex items-end justify-center gap-1">
                                            <!-- Bar Keluar -->
                                            <div class="w-full max-w-[16px] rounded-t-md transition-all duration-300 {{ $pt['keluar'] > 0 ? 'bg-rose-600 group-hover:bg-rose-700' : 'bg-stone-100' }}"
                                                 style="height: {{ $barHKeluar }}%;"></div>
                                            <!-- Bar Masuk -->
                                            <div class="w-full max-w-[16px] rounded-t-md transition-all duration-300 {{ $pt['masuk'] > 0 ? 'bg-emerald-600 group-hover:bg-emerald-700' : 'bg-stone-100' }}"
                                                 style="height: {{ $barHMasuk }}%;"></div>
                                        </div>

                                        <!-- Label Sumbu X -->
                                        <span class="text-[10px] sm:text-[11px] font-medium text-stone-500 mt-2 truncate max-w-full text-center" title="{{ $pt['label'] }}">
                                            {{ $pt['short_label'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <!-- Empty State Chart -->
                        <div class="h-56 flex flex-col items-center justify-center text-center p-6 bg-stone-50/50 rounded-xl border border-dashed border-stone-200">
                            <div class="w-10 h-10 rounded-full bg-teal-50 text-teal-700 flex items-center justify-center mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-stone-700">Belum ada aktivitas keluar/masuk stok pada periode {{ $periodeLabel }}</p>
                            <p class="text-[11px] text-stone-400 mt-0.5">Grafik stok akan menampilkan fluktuasi saat terjadi transaksi penjualan atau restok barang.</p>
                        </div>
                    @endif
                </div>

                <!-- Ringkasan Kesehatan Inventaris -->
                <div class="lg:col-span-4 bg-white rounded-2xl p-5 sm:p-6 shadow-xs border border-stone-200/60 flex flex-col justify-between">
                    <div>
                        <h2 class="text-base font-bold text-[#5A121A] tracking-tight">
                            Kesehatan Inventaris
                        </h2>
                        <p class="text-xs text-stone-500 mt-0.5">Status ketersediaan stok fisik</p>

                        <div class="mt-5 space-y-4">
                            <!-- Ready Stock -->
                            <div class="p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-100">
                                <div class="flex items-center justify-between text-xs font-bold text-emerald-900">
                                    <span>Produk Siap Jual (Ready)</span>
                                    <span>{{ $totalProdukReady }} Produk</span>
                                </div>
                                <p class="text-[11px] text-emerald-700 mt-1">Stok aman dan tersedia untuk dipesan pelanggan.</p>
                            </div>

                            <!-- Habis / Kritis -->
                            <div class="p-3.5 rounded-xl bg-rose-50/60 border border-rose-100">
                                <div class="flex items-center justify-between text-xs font-bold text-rose-900">
                                    <span>Stok Habis / Kritis (&le; 5)</span>
                                    <span>{{ $totalProdukHabis }} Produk</span>
                                </div>
                                <p class="text-[11px] text-rose-700 mt-1">Perlu perhatian untuk dilakukan proses restok ulang.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-stone-100 bg-[#FAF2EE]/60 -mx-5 -mb-5 sm:-mx-6 sm:-mb-6 p-4 rounded-b-2xl">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-stone-600 font-medium">Total Seluruh Item Stok:</span>
                            <span class="font-extrabold text-[#5A121A]">{{ number_format($totalSisaStok, 0, ',', '.') }} pcs</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel 1: Tabel Ringkasan Stok Produk -->
            <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-stone-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h2 class="text-base font-bold text-[#5A121A] tracking-tight">
                            Ringkasan Stok Produk
                        </h2>
                        <p class="text-xs text-stone-500 mt-0.5">Daftar sisa stok dan pergerakan per produk pada periode {{ $periodeLabel }}</p>
                    </div>
                    <span class="text-xs text-stone-500 font-medium">
                        Total {{ $productsInventory->count() }} katalog produk
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-stone-50 text-[11px] font-bold text-stone-600 uppercase tracking-wider border-b border-stone-200/60">
                                <th class="py-3 px-4 sm:px-6">No</th>
                                <th class="py-3 px-4">Nama Produk</th>
                                <th class="py-3 px-4">Kategori</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-center">Sisa Stok</th>
                                <th class="py-3 px-4 text-center">Stok Keluar</th>
                                <th class="py-3 px-4 text-center">Stok Masuk</th>
                                <th class="py-3 px-4 sm:px-6 text-right">Nilai Aset Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-xs">
                            @forelse ($productsInventory as $index => $p)
                                <tr class="hover:bg-stone-50/60 transition-colors">
                                    <td class="py-3.5 px-4 sm:px-6 font-semibold text-stone-500">{{ $index + 1 }}</td>
                                    <td class="py-3.5 px-4 font-bold text-stone-900">
                                        {{ $p['nama_kue'] }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-stone-100 text-stone-700">
                                            {{ $p['kategori'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $p['status_stok'] === \App\Enums\StatusStok::Ready && $p['stok'] > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                            {{ $p['status_stok']->value }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-extrabold {{ $p['stok'] <= 5 ? 'text-rose-600' : 'text-stone-900' }}">
                                        {{ number_format($p['stok'], 0, ',', '.') }} pcs
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-rose-700">
                                        {{ $p['terjual_periode'] > 0 ? '-' . number_format($p['terjual_periode'], 0, ',', '.') . ' pcs' : '0' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold text-emerald-700">
                                        {{ $p['masuk_periode'] > 0 ? '+' . number_format($p['masuk_periode'], 0, ',', '.') . ' pcs' : '0' }}
                                    </td>
                                    <td class="py-3.5 px-4 sm:px-6 text-right font-semibold text-stone-800">
                                        Rp{{ number_format($p['nilai_stok'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-10 px-6 text-center text-stone-400">
                                        Belum ada katalog produk di toko.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel 2: Riwayat Pergerakan Stok (Audit Trail) -->
            <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-stone-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-[#5A121A] tracking-tight">
                            Riwayat Log Pergerakan Stok
                        </h2>
                        <p class="text-xs text-stone-500 mt-0.5">Catatan riwayat keluar-masuk stok barang pada periode {{ $periodeLabel }}</p>
                    </div>
                    <span class="text-xs font-semibold text-stone-600">
                        Total: {{ $stockMovements->count() }} riwayat
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-stone-50 text-[11px] font-bold text-stone-600 uppercase tracking-wider border-b border-stone-200/60">
                                <th class="py-3 px-4 sm:px-6">Waktu</th>
                                <th class="py-3 px-4">Nama Produk</th>
                                <th class="py-3 px-4 text-center">Jenis Pergerakan</th>
                                <th class="py-3 px-4 text-center">Jumlah</th>
                                <th class="py-3 px-4">Keterangan / Sumber</th>
                                <th class="py-3 px-4 sm:px-6">Pencatat / Pemesan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-xs">
                            @forelse ($stockMovements as $move)
                                <tr class="hover:bg-stone-50/60 transition-colors">
                                    <td class="py-3.5 px-4 sm:px-6 text-stone-600 whitespace-nowrap">
                                        {{ $move['waktu'] instanceof \Carbon\Carbon ? $move['waktu']->format('d/m/Y H:i') : $move['waktu'] }}
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-stone-900">
                                        {{ $move['produk'] }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if ($move['tipe'] === 'masuk')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span>↓</span> Masuk (Restok)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span>↑</span> Keluar (Penjualan)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-bold {{ $move['tipe'] === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                                        {{ $move['tipe'] === 'masuk' ? '+' : '-' }}{{ number_format($move['jumlah'], 0, ',', '.') }} pcs
                                    </td>
                                    <td class="py-3.5 px-4 text-stone-700">
                                        {{ $move['keterangan'] }}
                                    </td>
                                    <td class="py-3.5 px-4 sm:px-6 text-stone-600 font-medium">
                                        {{ $move['actor'] ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 px-6 text-center text-stone-400">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-8 h-8 text-stone-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <p class="font-semibold text-stone-700">Tidak ada riwayat pergerakan stok</p>
                                            <p class="text-xs text-stone-400 mt-0.5">Belum ada barang masuk atau keluar pada rentang waktu ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- Print Specific Header / Footer Info -->
    <div class="hidden print:block mt-8 pt-6 border-t border-stone-300 text-xs text-stone-500">
        <div class="flex justify-between items-center">
            <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} oleh {{ auth()->user()->name ?? 'Admin' }}</p>
            <p>Nak Mbarep - Toko Kue UMKM</p>
        </div>
    </div>
@endsection
