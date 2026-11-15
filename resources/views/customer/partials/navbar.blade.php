@php
    $storeName = $storeSetting->store_name ?? \App\Models\StoreSetting::current()?->store_name ?? 'Nak Mbarep';
    $cartCount = array_sum(array_column(session('cart', []), 'quantity'));
@endphp

{{-- Top Navigation Bar --}}
<header class="bg-[#4D626C] text-white sticky top-0 z-50 shadow-xs overflow-x-clip">
    <div id="navbar-main-content" class="max-w-md md:max-w-2xl lg:max-w-5xl xl:max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 h-13 sm:h-14 lg:h-16 flex items-center justify-between flex-nowrap gap-1.5 sm:gap-4 relative">
        {{-- Brand / Logo (Kiri) --}}
        <a href="{{ route('customer.index') }}" class="flex items-center gap-1 sm:gap-2 lg:gap-2.5 shrink-0 min-w-0 group" title="{{ $storeName }}">
            <img src="{{ asset('images/logo-nav.png') }}" alt="Logo {{ $storeName }}" class="w-7 h-7 sm:w-8 sm:h-8 lg:w-9 lg:h-9 rounded-full object-cover border border-white/30 shadow-xs shrink-0">
            <span class="font-serif font-bold text-xs sm:text-base md:text-lg lg:text-xl text-white tracking-wide group-hover:text-amber-100 transition truncate max-w-[70px] min-[360px]:max-w-[90px] sm:max-w-none">
                {{ $storeName }}
            </span>
        </a>

        {{-- Navigation Links (Tengah / Sejajar) --}}
        <nav id="navbar-nav-links" class="flex items-center justify-center gap-2 sm:gap-4 md:gap-5 lg:gap-8 text-[11px] sm:text-xs lg:text-sm font-medium shrink-0 flex-nowrap transition-all duration-300">
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
        <div class="flex items-center gap-1 sm:gap-2 lg:gap-3 shrink-0">
            {{-- Form Pencarian Langsung yang muncul dari sebelah kiri tombol search --}}
            <form
                id="navbar-search-form"
                action="{{ route('customer.produk.index') }}"
                method="GET"
                class="relative flex items-center shrink-0"
            >
                {{-- Shape Input Pencari (Muncul melebar halus dari sebelah kiri tombol search) --}}
                <div
                    id="navbar-search-shape"
                    class="overflow-hidden transition-[max-width,opacity] duration-300 ease-in-out flex items-center max-w-0 opacity-0 pointer-events-none shrink-0"
                >
                    <div class="flex items-center bg-[#FAF2EE] text-gray-800 rounded-full border border-[#E8B8AE] shadow-inner pl-2 sm:pl-3 pr-1 py-0.5 sm:py-1 mr-1 sm:mr-1.5 focus-within:ring-2 focus-within:ring-[#6B0F1A]/30 focus-within:border-[#6B0F1A] focus-within:bg-white w-36 min-[380px]:w-44 sm:w-56 md:w-64 lg:w-72">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[#6B0F1A] shrink-0 ml-0.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="7"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 20l-3.5-3.5"/>
                        </svg>

                        <input
                            type="search"
                            id="navbar-search-input"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari kue..."
                            autocomplete="off"
                            class="w-full bg-transparent border-0 px-1.5 sm:px-2 py-0.5 text-xs sm:text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-0 min-w-0"
                        >

                        {{-- Tombol Close / Batal Shape (X) --}}
                        <button
                            type="button"
                            id="navbar-search-close"
                            class="text-gray-400 hover:text-gray-600 hover:bg-gray-200/60 p-0.5 sm:p-1 rounded-full transition shrink-0 cursor-pointer"
                            title="Tutup pencarian"
                            aria-label="Tutup pencarian"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Tombol Search (Icon) --}}
                <button
                    type="button"
                    id="navbar-search-toggle"
                    class="text-white/90 hover:text-white hover:bg-white/10 p-1 lg:p-1.5 rounded-lg transition-all duration-200 shrink-0 cursor-pointer focus:outline-none focus:ring-2 focus:ring-white/40 active:scale-95"
                    title="Cari Kue"
                    aria-label="Cari Kue"
                    aria-expanded="false"
                >
                    <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 lg:w-5 lg:h-5 transition-transform duration-200 hover:scale-110" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>

                {{-- Dropdown Hasil Cepat Langsung di bawah Shape Pencarian --}}
                <div
                    id="navbar-search-dropdown"
                    class="hidden absolute top-full right-0 mt-2 w-72 sm:w-80 md:w-96 max-w-[calc(100vw-24px)] bg-white rounded-2xl shadow-xl border border-[#F4E1D9] overflow-hidden z-50 transition-all duration-200"
                >
                    {{-- Loading State --}}
                    <div id="navbar-search-loading" class="hidden p-4 text-center text-xs text-gray-500">
                        <svg class="animate-spin h-4 w-4 mx-auto text-[#6B0F1A] mb-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Mencari kue...</span>
                    </div>

                    {{-- Hasil Pencarian --}}
                    <div id="navbar-search-results" class="py-1.5 max-h-72 sm:max-h-80 overflow-y-auto divide-y divide-gray-100"></div>

                    {{-- Footer Dropdown --}}
                    <div id="navbar-search-dropdown-footer" class="p-2 sm:p-2.5 bg-[#FAF2EE]/70 border-t border-[#F4E1D9] flex items-center justify-between text-xs">
                        <span id="navbar-search-count-label" class="text-gray-500 font-medium"></span>
                        <button type="submit" class="font-semibold text-[#6B0F1A] hover:underline inline-flex items-center gap-1 cursor-pointer">
                            <span>Lihat semua di katalog</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>
            </form>

            {{-- Cart Button --}}
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
