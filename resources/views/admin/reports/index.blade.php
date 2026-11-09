@extends('admin.layouts.app', ['title' => 'Laporan Penjualan', 'active' => 'reports'])

@section('content')
    <div class="mb-7 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#5A121A] tracking-tight">
                Laporan Penjualan
            </h1>
            <p class="text-stone-500 text-xs sm:text-sm mt-1">
                Pantau ringkasan penjualan dan performa Toko Kue Nak Mbarep.
            </p>
        </div>

        <span class="inline-flex items-center gap-1.5 self-start px-3 py-1.5 rounded-full bg-amber-50 text-amber-700 border border-amber-100 text-xs font-semibold">
            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
            Dalam Pengembangan
        </span>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-stone-100">
            <h2 class="text-sm sm:text-base font-bold text-[#5A121A] tracking-tight">
                Ringkasan Laporan
            </h2>
            <p class="text-xs text-stone-500 mt-0.5">Data laporan penjualan toko</p>
        </div>

        <div class="px-6 py-14 sm:py-16 flex flex-col items-center text-center">
            <div class="w-16 h-16 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-[#6B0F1A] mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>

            <h2 class="text-base sm:text-lg font-bold text-[#5A121A] tracking-tight">
                Belum ada data laporan
            </h2>
            <p class="text-sm text-stone-500 mt-2 max-w-md leading-relaxed">
                Halaman Laporan Penjualan sedang dalam pengembangan. Ringkasan penjualan akan tampil di sini setelah fitur selesai dibuat.
            </p>

            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center gap-2 mt-6 px-4 py-2.5 rounded-xl bg-[#6B0F1A] hover:bg-[#520A13] text-white text-xs font-semibold shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali ke Beranda</span>
            </a>
        </div>
    </div>
@endsection
