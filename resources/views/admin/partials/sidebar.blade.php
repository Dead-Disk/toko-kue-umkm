@props(['active' => 'dashboard'])

@php
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
            @include('admin.partials.navigation', ['active' => $active])
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
