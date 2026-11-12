<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Panel Staf' }} - Nak Mbarep</title>
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
    @stack('styles')
</head>
<body class="bg-[#FAF2EE] text-stone-800 antialiased min-h-screen flex flex-col lg:flex-row">

    <!-- Mobile Topbar (< lg) -->
    <header class="lg:hidden bg-[#4D626C] text-white px-4 py-3 flex items-center justify-between sticky top-0 z-40 shadow-sm">
        <div class="flex items-center gap-2.5">
            <button type="button" id="openMobileSidebarBtn" aria-label="Buka menu"
                    class="p-2 rounded-lg hover:bg-white/10 text-white focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/logo-nav.png') }}" alt="Nak Mbarep Logo" class="h-6 w-auto brightness-0 invert">
                <div class="leading-tight">
                    <span class="block font-bold text-sm tracking-tight">PANEL STAF</span>
                    <span class="block text-[9px] text-stone-300 font-medium tracking-wider uppercase">Nak Mbarep</span>
                </div>
            </div>
        </div>

        <a href="{{ route('staff.profile.edit') }}" title="Lihat & Edit Profil" class="flex items-center gap-2 hover:opacity-85 transition-opacity">
            <span class="text-xs text-stone-200">Staf</span>
            <div class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center font-bold text-xs text-white">
                {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
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
                        <div class="leading-tight">
                            <span class="block text-white font-bold text-base">PANEL STAF</span>
                            <span class="block text-[10px] text-stone-300 font-medium tracking-wider uppercase">Nak Mbarep</span>
                        </div>
                    </div>
                    <button type="button" id="closeMobileSidebarBtn" aria-label="Tutup menu"
                            class="p-1.5 rounded-lg text-stone-300 hover:text-white hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <nav class="p-4 space-y-1.5">
                    @include('staff.partials.navigation', ['active' => $active ?? 'dashboard', 'mobile' => true])
                </nav>
            </div>

            <div class="p-4 border-t border-white/10 space-y-2">
                <a href="{{ route('staff.profile.edit') }}"
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-stone-200 text-xs font-medium transition-colors">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-7 h-7 rounded-full bg-stone-700/80 border border-white/20 flex items-center justify-center font-bold text-white text-xs shrink-0">
                            {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                        </div>
                        <div class="min-w-0 truncate text-left">
                            <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'Staf' }}</p>
                            <p class="text-[10px] text-stone-400 truncate">Staf Toko</p>
                        </div>
                    </div>
                    <span class="text-stone-400 text-xs">Profil →</span>
                </a>

                <a href="{{ route('customer.index') }}" target="_blank"
                   class="block text-center py-2 text-xs text-stone-200 bg-white/10 rounded-lg">
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
        @include('staff.partials.sidebar', ['active' => $active ?? 'dashboard'])
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8 xl:p-10 overflow-y-auto">
        @yield('content')
    </main>

    <!-- Vanilla JS for Mobile Sidebar Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const openBtn = document.getElementById('openMobileSidebarBtn');
            const closeBtn = document.getElementById('closeMobileSidebarBtn');
            const backdrop = document.getElementById('mobileSidebarBackdrop');
            const drawer = document.getElementById('mobileSidebarDrawer');

            function openSidebar() {
                backdrop.classList.remove('hidden');
                setTimeout(() => {
                    drawer.classList.remove('-translate-x-full');
                }, 10);
            }

            function closeSidebar() {
                drawer.classList.add('-translate-x-full');
                setTimeout(() => {
                    backdrop.classList.add('hidden');
                }, 300);
            }

            if (openBtn) openBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (backdrop) backdrop.addEventListener('click', function (e) {
                if (e.target === backdrop) closeSidebar();
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
