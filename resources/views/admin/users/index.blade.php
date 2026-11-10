<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User / Staf - Admin Nak Mbarep</title>
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
</head>
<body class="bg-[#FAF2EE] text-stone-800 antialiased min-h-screen flex flex-col lg:flex-row">

    <!-- Mobile Topbar (< lg) -->
    <header class="lg:hidden bg-[#4D626C] text-white px-4 py-3 flex items-center justify-between sticky top-0 z-40 shadow-sm">
        <div class="flex items-center gap-2.5">
            <button type="button" id="openMobileSidebarBtn" class="p-2 rounded-lg hover:bg-white/10 text-white focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/logo-nav.png') }}" alt="Nak Mbarep Logo" class="h-6 w-auto brightness-0 invert">
                <span class="font-bold text-base tracking-tight">Nak Mbarep</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs text-stone-200">Admin</span>
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
                    <button type="button" id="closeMobileSidebarBtn" class="p-1.5 rounded-lg text-stone-300 hover:text-white hover:bg-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <nav class="p-4 space-y-1.5">
                    @include('admin.partials.navigation', ['active' => 'users', 'mobile' => true])
                </nav>
            </div>

            <div class="p-4 border-t border-white/10 space-y-2">
                <a href="{{ route('customer.index') }}" target="_blank" class="block text-center py-2 text-xs text-stone-200 bg-white/10 rounded-lg">
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
        @include('admin.partials.sidebar', ['active' => 'users'])
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8 xl:p-10 overflow-y-auto">

        <!-- Top Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#5A121A] tracking-tight">
                    Kelola User &amp; Staf
                </h1>
                <p class="text-stone-500 text-xs sm:text-sm mt-1">
                    Atur akun administrator dan staf yang memiliki akses ke sistem toko kue.
                </p>
            </div>

            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-1.5 bg-[#6B0F1A] hover:bg-[#520A13] text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-xs transition-colors shrink-0 self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah User</span>
            </a>
        </div>

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">✕</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-xs">
                <p class="font-bold mb-1.5 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Terdapat kesalahan:
                </p>
                <ul class="list-disc list-inside space-y-1 text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- 4 Stat Cards Row -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Stat 1: Total Pengguna -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-[#6B0F1A] shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-stone-500 font-medium">Total Akun</p>
                        <p class="text-2xl font-bold text-stone-900 leading-tight">
                            {{ $totalUsers ?? count($users) }}
                        </p>
                    </div>
                </div>
                <div class="mt-3.5 pt-3 border-t border-stone-100 flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600">
                    <span>Pengguna terdaftar</span>
                </div>
            </div>

            <!-- Stat 2: Administrator -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-stone-500 font-medium">Administrator</p>
                        <p class="text-2xl font-bold text-stone-900 leading-tight">
                            {{ $totalAdmin ?? $users->where('role', 'admin')->count() }}
                        </p>
                    </div>
                </div>
                <div class="mt-3.5 pt-3 border-t border-stone-100 flex items-center gap-1.5 text-[11px] font-semibold text-purple-600">
                    <span>Akses penuh sistem</span>
                </div>
            </div>

            <!-- Stat 3: Staf Toko -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-stone-500 font-medium">Staf Toko</p>
                        <p class="text-2xl font-bold text-stone-900 leading-tight">
                            {{ $totalStaff ?? $users->where('role', 'staff')->count() }}
                        </p>
                    </div>
                </div>
                <div class="mt-3.5 pt-3 border-t border-stone-100 flex items-center gap-1.5 text-[11px] font-semibold text-sky-600">
                    <span>Kasir &amp; operasional</span>
                </div>
            </div>

            <!-- Stat 4: Akun Anda -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-stone-200/60 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-stone-500 font-medium">Akun Anda Saat Ini</p>
                        <p class="text-sm font-bold text-stone-900 leading-tight truncate">
                            {{ auth()->user()->name ?? 'Admin' }}
                        </p>
                    </div>
                </div>
                <div class="mt-3.5 pt-3 border-t border-stone-100 flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600">
                    <span class="capitalize">Role: {{ auth()->user()->role ?? 'admin' }}</span>
                </div>
            </div>
        </section>

        <!-- Main Table Container -->
        <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 overflow-hidden">

            <!-- Toolbar & Filter Bar -->
            <div class="p-4 sm:p-5 border-b border-stone-100 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </span>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari nama, username, email..."
                               class="w-full pl-10 pr-3.5 py-2 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                    </div>

                    <!-- Filter Role -->
                    <select name="role"
                            onchange="this.form.submit()"
                            class="py-2 px-3 text-xs rounded-xl border border-stone-200 bg-stone-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] text-stone-700 font-medium">
                        <option value="">Semua Role</option>
                        <option value="admin" @selected(request('role') === 'admin')>Administrator</option>
                        <option value="staff" @selected(request('role') === 'staff')>Staf</option>
                    </select>

                    @if (request()->hasAny(['search', 'role']))
                        <a href="{{ route('admin.users.index') }}" class="px-3 py-2 text-xs font-semibold text-stone-500 hover:text-stone-800 text-center">
                            Reset
                        </a>
                    @endif
                </form>

                <div class="text-xs text-stone-400 font-medium shrink-0 self-center sm:self-auto">
                    Total: <strong class="text-stone-700">{{ count($users) }}</strong> akun
                </div>
            </div>

            <!-- Users Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-[#FAF2EE]/80 text-stone-600 font-semibold border-b border-stone-100">
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Nama Lengkap</th>
                            <th class="py-3 px-4">Username</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4 text-center">Role</th>
                            <th class="py-3 px-4">Dibuat</th>
                            <th class="py-3 px-4 text-center w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse ($users as $user)
                            @php
                                $isSelf = $user->is(auth()->user());
                                $initial = strtoupper(substr($user->name, 0, 1));
                                $avatarBg = $user->role === 'admin' ? 'bg-[#6B0F1A] text-white' : 'bg-sky-100 text-sky-800';
                            @endphp
                            <tr class="hover:bg-stone-50/70 transition-colors {{ $isSelf ? 'bg-amber-50/30' : '' }}">
                                <!-- No -->
                                <td class="py-3.5 px-4 text-center text-stone-500 font-mono text-[11px]">
                                    {{ $loop->iteration }}
                                </td>

                                <!-- Nama & Avatar -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs shrink-0 shadow-xs {{ $avatarBg }}">
                                            {{ $initial }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-stone-900 leading-tight">
                                                {{ $user->name }}
                                                @if ($isSelf)
                                                    <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800 border border-amber-200/80">
                                                        (akun Anda)
                                                    </span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Username -->
                                <td class="py-3.5 px-4 font-mono text-stone-700 whitespace-nowrap text-[11px]">
                                    <span>{{ $user->username }}</span>
                                </td>

                                <!-- Email -->
                                <td class="py-3.5 px-4 text-stone-600 whitespace-nowrap">
                                    {{ $user->email }}
                                </td>

                                <!-- Role -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if ($user->role === 'admin')
                                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200/60 uppercase tracking-wide">
                                            Admin
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-200/60 uppercase tracking-wide">
                                            Staff
                                        </span>
                                    @endif
                                    <span class="hidden">{{ $user->role }}</span>
                                </td>

                                <!-- Waktu Dibuat -->
                                <td class="py-3.5 px-4 text-stone-500 text-[11px] whitespace-nowrap">
                                    <span class="block text-stone-800 font-medium">{{ $user->created_at->format('d M Y') }}</span>
                                    <span class="text-stone-400">{{ $user->created_at->format('H:i') }} WIB</span>
                                    <span class="hidden">{{ $user->created_at->format('d-m-Y H:i') }}</span>
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="px-2.5 py-1 text-[11px] font-semibold text-stone-700 bg-stone-100 hover:bg-stone-200 rounded-lg transition-colors">
                                            Edit
                                        </a>

                                        @if ($isSelf)
                                            <span class="text-[11px] text-stone-400 italic px-1">
                                                <em>(akun Anda)</em>
                                            </span>
                                        @else
                                            <form action="{{ route('admin.users.destroy', $user) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $user->name }}?')"
                                                  class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-2.5 py-1 text-[11px] font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-stone-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-2xl bg-stone-100 flex items-center justify-center text-stone-400 mb-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-stone-600">Tidak ada user ditemukan</p>
                                        <p class="text-xs text-stone-400 mt-0.5">Silakan tambahkan user baru atau sesuaikan pencarian Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Mobile Sidebar JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const openMobileBtn = document.getElementById('openMobileSidebarBtn');
            const closeMobileBtn = document.getElementById('closeMobileSidebarBtn');
            const mobileBackdrop = document.getElementById('mobileSidebarBackdrop');
            const mobileDrawer = document.getElementById('mobileSidebarDrawer');

            if (openMobileBtn) {
                openMobileBtn.addEventListener('click', () => {
                    mobileBackdrop.classList.remove('hidden');
                    setTimeout(() => mobileDrawer.classList.remove('-translate-x-full'), 10);
                });
            }
            if (closeMobileBtn) {
                closeMobileBtn.addEventListener('click', closeMobileSidebar);
            }
            if (mobileBackdrop) {
                mobileBackdrop.addEventListener('click', (e) => {
                    if (e.target === mobileBackdrop) closeMobileSidebar();
                });
            }
            function closeMobileSidebar() {
                mobileDrawer.classList.add('-translate-x-full');
                setTimeout(() => mobileBackdrop.classList.add('hidden'), 300);
            }
        });
    </script>
</body>
</html>
