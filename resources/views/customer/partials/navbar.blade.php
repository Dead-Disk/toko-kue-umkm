@php
    $storeName = $storeSetting->store_name ?? \App\Models\StoreSetting::current()?->store_name ?? 'Nak Mbarep';
    $cartCount = array_sum(array_column(session('cart', []), 'quantity'));
@endphp

{{-- Top Navigation Bar --}}
<header class="bg-[#4D626C] text-white sticky top-0 z-50 shadow-xs">
    <div class="max-w-md md:max-w-2xl lg:max-w-5xl xl:max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 h-13 sm:h-14 lg:h-16 flex items-center justify-between flex-nowrap gap-2 sm:gap-4">
        {{-- Brand / Logo (Kiri) --}}
        <a href="{{ route('customer.index') }}" class="flex items-center gap-1.5 sm:gap-2 lg:gap-2.5 shrink-0 min-w-0 group" title="{{ $storeName }}">
            <img src="{{ asset('images/logo-nav.png') }}" alt="Logo {{ $storeName }}" class="w-7 h-7 sm:w-8 sm:h-8 lg:w-9 lg:h-9 rounded-full object-cover border border-white/30 shadow-xs shrink-0">
            <span class="font-serif font-bold text-xs sm:text-base md:text-lg lg:text-xl text-white tracking-wide group-hover:text-amber-100 transition truncate max-w-[75px] min-[380px]:max-w-[105px] sm:max-w-none">
                {{ $storeName }}
            </span>
        </a>

        {{-- Navigation Links (Tengah / Sejajar) --}}
        <nav class="flex items-center justify-center gap-2 sm:gap-4 md:gap-5 lg:gap-8 text-[11px] sm:text-xs lg:text-sm font-medium shrink-0 flex-nowrap">
            <a href="{{ route('customer.index') }}"
               class="whitespace-nowrap shrink-0 py-1 transition {{ request()->routeIs('customer.index') ? 'text-white font-semibold underline underline-offset-4 decoration-2 decoration-white' : 'text-white/80 hover:text-white' }}">
                Beranda
            </a>
            <a href="{{ route('customer.produk.index') }}"
               class="whitespace-nowrap shrink-0 py-1 transition {{ request()->routeIs('customer.produk.*') ? 'text-white font-semibold underline underline-offset-4 decoration-2 decoration-white' : 'text-white/80 hover:text-white' }}">
                Katalog Kue
            </a>
            <a href="{{ route('customer.page', 'tentang-kami') }}"
               class="whitespace-nowrap shrink-0 py-1 transition {{ request()->is('p/tentang-kami') ? 'text-white font-semibold underline underline-offset-4 decoration-2 decoration-white' : 'text-white/80 hover:text-white' }}">
                Tentang Kami
            </a>
        </nav>

        {{-- Action Icons: Search & Keranjang (Kanan Ujung) --}}
        <div class="flex items-center gap-1.5 sm:gap-2 lg:gap-3 shrink-0">
            <a href="{{ route('customer.produk.index') }}" class="text-white/90 hover:text-white p-1 lg:p-1.5 transition shrink-0" title="Cari Kue" aria-label="Cari Kue">
                <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 lg:w-5 lg:h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </a>

            <a href="{{ route('cart.index') }}" class="relative bg-[#6B0F1A] hover:bg-[#520912] text-white p-1.5 lg:p-2 rounded-lg lg:rounded-xl flex items-center justify-center transition shadow-2xs shrink-0" aria-label="Keranjang Belanja" title="Lihat Keranjang">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 lg:w-4.5 lg:h-4.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                @if ($cartCount > 0)
                    <span class="absolute -top-1.5 -right-1.5 bg-amber-400 text-gray-900 text-[9px] lg:text-[10px] font-bold w-4 h-4 lg:w-4.5 lg:h-4.5 rounded-full flex items-center justify-center border border-white">
                        {{ $cartCount }}
                    </span>
                @endif
            </a>
        </div>
    </div>
</header>
