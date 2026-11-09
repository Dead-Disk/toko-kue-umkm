@php
    $storeName = $storeSetting->store_name ?? \App\Models\StoreSetting::current()?->store_name ?? 'Nak Mbarep';
    $cartCount = array_sum(array_column(session('cart', []), 'quantity'));
@endphp

{{-- Top Navigation Bar --}}
<header class="bg-[#4D626C] text-white sticky top-0 z-50 shadow-xs">
    <div class="max-w-md md:max-w-2xl mx-auto px-4 h-13 flex items-center justify-between">
        {{-- Brand / Logo --}}
        <a href="{{ route('customer.index') }}" class="flex items-center gap-2 group">
            <img src="{{ asset('images/logo-nav.png') }}" alt="Logo {{ $storeName }}" class="w-8 h-8 rounded-full object-cover border border-white/30 shadow-xs">
            <span class="font-serif font-bold text-base sm:text-lg text-white tracking-wide group-hover:text-amber-100 transition truncate max-w-[130px] sm:max-w-none">
                {{ $storeName }}
            </span>
        </a>

        {{-- Navigation Links --}}
        <nav class="flex items-center gap-3 sm:gap-5 text-xs">
            <a href="{{ route('customer.index') }}" class="{{ request()->routeIs('customer.index') ? 'text-white font-semibold underline underline-offset-4 decoration-2 decoration-white' : 'text-white/80 hover:text-white font-medium transition' }}">
                Beranda
            </a>
            <a href="{{ route('customer.produk.index') }}" class="{{ request()->routeIs('customer.produk.*') ? 'text-white font-semibold underline underline-offset-4 decoration-2 decoration-white' : 'text-white/80 hover:text-white font-medium transition' }}">
                Katalog Kue
            </a>
            <a href="{{ route('customer.page', 'tentang-kami') }}" class="{{ request()->is('p/tentang-kami') ? 'text-white font-semibold underline underline-offset-4 decoration-2 decoration-white' : 'text-white/80 hover:text-white font-medium transition' }}">
                Tentang Kami
            </a>
        </nav>

        {{-- Action Icons (Search & Cart) --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('customer.produk.index') }}" class="text-white/90 hover:text-white p-1 transition" title="Cari Kue" aria-label="Cari Kue">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </a>

            <a href="{{ route('cart.index') }}" class="relative bg-[#6B0F1A] hover:bg-[#520912] text-white p-1.5 rounded-lg flex items-center justify-center transition shadow-2xs" aria-label="Keranjang Belanja" title="Lihat Keranjang">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                @if ($cartCount > 0)
                    <span class="absolute -top-1.5 -right-1.5 bg-amber-400 text-gray-900 text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center border border-white">
                        {{ $cartCount }}
                    </span>
                @endif
            </a>
        </div>
    </div>
</header>
