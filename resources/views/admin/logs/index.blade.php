@extends('admin.layouts.app', ['title' => 'Log Aktivitas', 'active' => 'logs'])

@section('content')
    <!-- Page Header -->
    <div class="mb-7 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#5A121A] tracking-tight flex items-center gap-2.5">
                <svg class="w-7 h-7 text-[#6B0F1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Log Aktivitas</span>
            </h1>
            <p class="text-stone-500 text-xs sm:text-sm mt-1">
                Catatan riwayat tindakan dan aktivitas pengguna (Owner & Staff) dalam sistem toko.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-xs font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Khusus Owner Toko
            </span>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <!-- Total Log -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-xs border border-stone-200/60">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-stone-500">Total Log</p>
                <div class="w-8 h-8 rounded-lg bg-stone-100 text-stone-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-[#5A121A] mt-2">
                {{ number_format($stats['total'], 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-stone-400 mt-0.5">Semua tindakan terekam</p>
        </div>

        <!-- Hari Ini -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-xs border border-stone-200/60">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-stone-500">Hari Ini</p>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-emerald-700 mt-2">
                {{ number_format($stats['today'], 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-stone-400 mt-0.5">Aktivitas sejak 00:00</p>
        </div>

        <!-- Aktivitas Owner -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-xs border border-stone-200/60">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-stone-500">Aktivitas Owner</p>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-[#6B0F1A] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-[#6B0F1A] mt-2">
                {{ number_format($stats['owner'], 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-stone-400 mt-0.5">Tindakan oleh akun admin/owner</p>
        </div>

        <!-- Aktivitas Staff -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl shadow-xs border border-stone-200/60">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-stone-500">Aktivitas Staff</p>
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-sky-700 mt-2">
                {{ number_format($stats['staff'], 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-stone-400 mt-0.5">Tindakan oleh staf toko</p>
        </div>
    </div>

    <!-- Filter & Search Section -->
    <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 p-4 sm:p-5 mb-6">
        <form method="GET" action="{{ route('admin.logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Keyword -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-xs font-bold text-stone-600 mb-1">Cari Aktivitas</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text"
                           id="search"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari deskripsi, nama user, IP..."
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A]">
                </div>
            </div>

            <!-- Role Filter -->
            <div>
                <label for="role" class="block text-xs font-bold text-stone-600 mb-1">Role Pengguna</label>
                <select id="role" name="role" class="w-full px-3 py-2 text-xs rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A]">
                    <option value="">Semua Role</option>
                    <option value="admin" @selected($activeRole === 'admin')>Owner (Admin)</option>
                    <option value="staff" @selected($activeRole === 'staff')>Staff</option>
                </select>
            </div>

            <!-- Action Filter -->
            <div>
                <label for="action" class="block text-xs font-bold text-stone-600 mb-1">Jenis Aksi</label>
                <select id="action" name="action" class="w-full px-3 py-2 text-xs rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A]">
                    <option value="">Semua Aksi</option>
                    <option value="login" @selected($activeAction === 'login')>Login</option>
                    <option value="logout" @selected($activeAction === 'logout')>Logout</option>
                    <option value="tambah" @selected($activeAction === 'tambah')>Tambah Data</option>
                    <option value="ubah" @selected($activeAction === 'ubah')>Ubah Data</option>
                    <option value="hapus" @selected($activeAction === 'hapus')>Hapus Data</option>
                    <option value="kasir" @selected($activeAction === 'kasir')>Kasir (POS)</option>
                </select>
            </div>

            <!-- Date Filter & Submit Button -->
            <div>
                <label for="date" class="block text-xs font-bold text-stone-600 mb-1">Waktu</label>
                <div class="flex gap-2">
                    <select id="date" name="date" class="w-full px-3 py-2 text-xs rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A]">
                        <option value="">Semua Waktu</option>
                        <option value="today" @selected($activeDate === 'today')>Hari Ini</option>
                        <option value="week" @selected($activeDate === 'week')>7 Hari Terakhir</option>
                        <option value="month" @selected($activeDate === 'month')>30 Hari Terakhir</option>
                    </select>
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-[#6B0F1A] hover:bg-[#520A13] text-white text-xs font-semibold shrink-0 transition-colors shadow-2xs">
                        Filter
                    </button>
                    @if ($search || $activeRole || $activeAction || $activeDate)
                        <a href="{{ route('admin.logs.index') }}" title="Reset Filter" class="p-2 rounded-xl border border-stone-200 text-stone-500 hover:bg-stone-50 shrink-0 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Activity Log Table Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-stone-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm sm:text-base font-bold text-[#5A121A] tracking-tight">
                    Daftar Riwayat Aktivitas
                </h2>
                <p class="text-xs text-stone-500 mt-0.5">
                    Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} aktivitas
                </p>
            </div>
        </div>

        @if ($logs->isEmpty())
            <div class="px-6 py-14 sm:py-16 flex flex-col items-center text-center">
                <div class="w-14 h-14 rounded-2xl bg-stone-100 text-stone-400 flex items-center justify-center mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-sm sm:text-base font-bold text-stone-700">Belum Ada Aktivitas Tercatat</h3>
                <p class="text-xs text-stone-500 mt-1 max-w-sm">
                    @if ($search || $activeRole || $activeAction || $activeDate)
                        Tidak ditemukan aktivitas yang cocok dengan filter yang dipilih. Coba reset filter Anda.
                    @else
                        Aktivitas staf dan owner akan otomatis terekam dan muncul di sini saat melakukan tindakan di sistem.
                    @endif
                </p>
                @if ($search || $activeRole || $activeAction || $activeDate)
                    <a href="{{ route('admin.logs.index') }}" class="mt-4 px-3.5 py-1.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-semibold transition-colors">
                        Reset Filter
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-50/75 border-b border-stone-200/60 text-stone-500 font-bold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="py-3 px-4 sm:px-6">Waktu</th>
                            <th class="py-3 px-4">Pengguna</th>
                            <th class="py-3 px-4">Aksi</th>
                            <th class="py-3 px-4">Deskripsi Aktivitas</th>
                            <th class="py-3 px-4 sm:px-6">Info IP / Perangkat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach ($logs as $log)
                            <tr class="hover:bg-amber-50/20 transition-colors">
                                <!-- Timestamp -->
                                <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap text-stone-600">
                                    <div class="font-semibold text-stone-800">
                                        {{ $log->created_at->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="text-[11px] text-stone-400">
                                        {{ $log->created_at->format('H:i:s') }} WIB
                                        <span class="text-stone-300">·</span>
                                        <span class="text-stone-500">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                </td>

                                <!-- User & Role -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-stone-100 border border-stone-200 flex items-center justify-center font-bold text-stone-700 text-xs shrink-0">
                                            {{ strtoupper(substr($log->user_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-stone-800 leading-tight">
                                                {{ $log->user_name }}
                                            </p>
                                            <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded text-[10px] font-semibold border {{ $log->roleBadgeClasses() }}">
                                                {{ in_array(strtolower($log->user_role), ['admin', 'owner']) ? 'Owner' : 'Staff' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Action Badge -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $log->badgeClasses() }}">
                                        {{ $log->actionLabel() }}
                                    </span>
                                </td>

                                <!-- Description -->
                                <td class="py-3.5 px-4 text-stone-700 min-w-[240px]">
                                    <p class="leading-relaxed font-medium">
                                        {{ $log->description }}
                                    </p>
                                    @if ($log->subject_type && $log->subject_id)
                                        <p class="text-[10px] text-stone-400 mt-0.5">
                                            Objek: <span class="font-mono text-stone-600">{{ $log->subject_type }} #{{ $log->subject_id }}</span>
                                        </p>
                                    @endif
                                </td>

                                <!-- IP Address / User Agent -->
                                <td class="py-3.5 px-4 sm:px-6 whitespace-nowrap text-stone-500">
                                    <div class="font-mono text-[11px] text-stone-700">
                                        {{ $log->ip_address ?: '-' }}
                                    </div>
                                    @if ($log->user_agent)
                                        <div class="text-[10px] text-stone-400 truncate max-w-[160px]" title="{{ $log->user_agent }}">
                                            {{ Str::limit($log->user_agent, 25) }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($logs->hasPages())
                <div class="px-5 sm:px-6 py-4 border-t border-stone-100 bg-stone-50/50">
                    {{ $logs->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
