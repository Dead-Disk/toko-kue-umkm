@props(['active' => 'dashboard'])

@php
    $currentRoute = request()->route()?->getName() ?? '';
    $user = auth()->user();
@endphp

<!-- Sidebar Desktop -->
<aside class="w-64 bg-[#4D626C] text-stone-200 flex flex-col justify-between shrink-0 min-h-screen select-none transition-all">
    <div>
        <!-- Brand Header -->
        <div class="px-6 py-6 border-b border-white/10 flex items-center gap-3">
            <img src="{{ asset('images/logo-nav.png') }}" alt="Nak Mbarep Logo" class="h-8 w-auto brightness-0 invert drop-shadow-sm">
            <div>
                <span class="block text-white font-bold text-lg tracking-tight leading-none">Nak Mbarep</span>
                <span class="text-[10px] text-stone-300 font-medium tracking-wider uppercase">Admin Panel</span>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="p-4 space-y-1.5">
            <!-- Beranda -->
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition-all {{ $active === 'dashboard' || $currentRoute === 'admin.dashboard' ? 'bg-white text-[#6B0F1A] shadow-md shadow-black/5' : 'text-stone-300 hover:text-white hover:bg-white/10' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Beranda</span>
            </a>

            <!-- Pesanan -->
            <a href="{{ route('admin.pesanan.index') }}"
               class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition-all {{ $active === 'pesanan' || str_starts_with($currentRoute, 'admin.pesanan') ? 'bg-white text-[#6B0F1A] shadow-md shadow-black/5' : 'text-stone-300 hover:text-white hover:bg-white/10' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Pesanan</span>
            </a>

            <!-- Produk -->
            <a href="{{ route('admin.produk.index') }}"
               class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition-all {{ $active === 'produk' || str_starts_with($currentRoute, 'admin.produk') ? 'bg-white text-[#6B0F1A] shadow-md shadow-black/5' : 'text-stone-300 hover:text-white hover:bg-white/10' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span>Produk</span>
            </a>

            <!-- Kategori -->
            <a href="{{ route('admin.kategori.index') }}"
               class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition-all {{ $active === 'kategori' || str_starts_with($currentRoute, 'admin.kategori') ? 'bg-white text-[#6B0F1A] shadow-md shadow-black/5' : 'text-stone-300 hover:text-white hover:bg-white/10' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <span>Kategori</span>
            </a>

            <!-- Pelanggan / User -->
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition-all {{ $active === 'pelanggan' || str_starts_with($currentRoute, 'admin.users') ? 'bg-white text-[#6B0F1A] shadow-md shadow-black/5' : 'text-stone-300 hover:text-white hover:bg-white/10' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Pelanggan</span>
            </a>

            <!-- Laporan -->
            <a href="{{ route('admin.kasir.index') }}"
               class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition-all {{ $active === 'laporan' || str_starts_with($currentRoute, 'admin.kasir') ? 'bg-white text-[#6B0F1A] shadow-md shadow-black/5' : 'text-stone-300 hover:text-white hover:bg-white/10' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                <span>Laporan & Kasir</span>
            </a>

            <!-- Pengaturan -->
            <a href="{{ route('admin.settings.index') }}"
               class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition-all {{ $active === 'pengaturan' || str_starts_with($currentRoute, 'admin.settings') || str_starts_with($currentRoute, 'admin.pengaturan') ? 'bg-white text-[#6B0F1A] shadow-md shadow-black/5' : 'text-stone-300 hover:text-white hover:bg-white/10' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Pengaturan</span>
            </a>
        </nav>
    </div>

    <!-- Bottom Actions & User Profile -->
    <div class="p-4 border-t border-white/10 space-y-3">
        <!-- Ke Web Pembeli -->
        <a href="{{ route('customer.index') }}" target="_blank"
           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-stone-200 text-xs font-medium transition-colors">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Lihat Web Toko
            </span>
            <span class="text-stone-400">↗</span>
        </a>

        <!-- User info and Logout -->
        <div class="flex items-center justify-between pt-1">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-full bg-stone-700/80 border border-white/20 flex items-center justify-center font-bold text-white text-xs shrink-0">
                    {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                </div>
                <div class="min-w-0 truncate">
                    <p class="text-xs font-semibold text-white truncate">{{ $user->name ?? 'Admin' }}</p>
                    <p class="text-[10px] text-stone-400 capitalize truncate">{{ $user->role ?? 'admin' }}</p>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                @csrf
                <button type="submit" title="Logout"
                        class="p-2 text-stone-300 hover:text-rose-300 hover:bg-white/10 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
