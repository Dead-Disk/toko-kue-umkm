<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} - Nak Mbarep</title>
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
                <span class="font-bold text-base tracking-tight">Nak Mbarep</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs text-stone-200 capitalize">{{ auth()->user()->role ?? 'admin' }}</span>
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
                    <button type="button" id="closeMobileSidebarBtn" aria-label="Tutup menu"
                            class="p-1.5 rounded-lg text-stone-300 hover:text-white hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <nav class="p-4 space-y-1.5">
                    @include('admin.partials.navigation', ['active' => $active ?? 'dashboard', 'mobile' => true])
                </nav>
            </div>

            <div class="p-4 border-t border-white/10 space-y-2">
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
        @include('admin.partials.sidebar', ['active' => $active ?? 'dashboard'])
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
