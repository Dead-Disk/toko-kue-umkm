@extends('staff.layouts.app', ['title' => 'Profil Akun', 'active' => 'profile'])

@section('content')
    <!-- Top Header & Breadcrumb -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2 text-xs text-stone-500 mb-1">
                <a href="{{ route('staff.dashboard') }}" class="hover:text-[#6B0F1A] transition-colors">Beranda</a>
                <span>/</span>
                <span class="text-stone-800 font-semibold">Profil Akun</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-[#5A121A] tracking-tight">
                Profil Akun
            </h1>
            <p class="text-stone-500 text-xs sm:text-sm mt-1">
                Kelola informasi data diri, username, email, dan perbarui kata sandi akun Anda.
            </p>
        </div>

        <div class="self-start sm:self-auto">
            @if ($user->isAdmin())
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-rose-50 text-[#6B0F1A] border border-rose-200/80 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-[#6B0F1A]"></span>
                    Administrator
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-sky-50 text-sky-700 border border-sky-200/80 text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    Staf Toko
                </span>
            @endif
        </div>
    </div>

    <!-- Flash Notifications -->
    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs sm:text-sm flex items-start gap-3 shadow-xs">
            <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0 text-emerald-700 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-emerald-900">Berhasil Disimpan!</p>
                <p class="mt-0.5 text-emerald-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-800 text-xs sm:text-sm shadow-xs">
            <div class="flex items-center gap-2 font-bold text-rose-900 mb-2">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Mohon periksa kembali formulir berikut:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-rose-700 pl-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left Column: User Summary Card (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Profile Identity Card -->
            <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 p-6 text-center">
                <!-- Avatar Large -->
                <div class="w-20 h-20 rounded-2xl {{ $user->isAdmin() ? 'bg-gradient-to-tr from-[#6B0F1A] to-[#8C1D2C]' : 'bg-gradient-to-tr from-sky-600 to-sky-500' }} text-white text-2xl font-extrabold flex items-center justify-center mx-auto shadow-md shadow-stone-200 mb-4 border-2 border-white">
                    {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                </div>

                <h2 class="text-base font-bold text-stone-900 tracking-tight">{{ $user->name }}</h2>
                <p class="text-xs text-stone-400 font-mono mt-0.5">&#64;{{ $user->username }}</p>

                <!-- Role Badge -->
                <div class="mt-3">
                    @if ($user->isAdmin())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-[#6B0F1A] border border-rose-100 text-xs font-semibold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Administrator
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 text-sky-700 border border-sky-100 text-xs font-semibold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Staf Toko
                        </span>
                    @endif
                </div>

                <div class="mt-6 pt-5 border-t border-stone-100 text-left space-y-3">
                    <div>
                        <span class="block text-[11px] font-medium text-stone-400">Email Akun</span>
                        <span class="text-xs font-semibold text-stone-800 break-all">{{ $user->email }}</span>
                    </div>

                    <div>
                        <span class="block text-[11px] font-medium text-stone-400">Terdaftar Sejak</span>
                        <span class="text-xs font-semibold text-stone-800">
                            {{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Role Notice Card -->
            <div class="bg-amber-50/70 rounded-2xl border border-amber-200/70 p-4 text-xs text-amber-900 flex items-start gap-3">
                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="leading-relaxed">
                    <p class="font-bold text-amber-950">Informasi Kewenangan</p>
                    <p class="text-amber-800 mt-0.5">
                        Role akun dikelola secara terpusat oleh pemilik toko. Hubungi admin jika kewenangan peran Anda perlu diubah.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Column: Profile Edit Form (8 cols) -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 p-6 sm:p-8">
                <form action="{{ route('staff.profile.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Section: Informasi Dasar -->
                    <div>
                        <div class="flex items-center gap-2.5 pb-3 border-b border-stone-100 mb-5">
                            <div class="w-8 h-8 rounded-lg bg-rose-50 border border-rose-100 flex items-center justify-center text-[#6B0F1A]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-[#5A121A]">Informasi Dasar</h2>
                                <p class="text-[11px] text-stone-500">Perbarui nama lengkap, username untuk login, dan alamat email Anda</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <!-- Nama Lengkap -->
                            <div>
                                <label for="name" class="block text-xs font-bold text-stone-700 mb-1.5">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $user->name) }}"
                                       required
                                       placeholder="Nama lengkap Anda"
                                       class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                            </div>

                            <!-- Username & Email Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="username" class="block text-xs font-bold text-stone-700 mb-1.5">
                                        Username (untuk Login) <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400 text-xs">
                                            &#64;
                                        </div>
                                        <input type="text"
                                               id="username"
                                               name="username"
                                               value="{{ old('username', $user->username) }}"
                                               required
                                               placeholder="username"
                                               class="w-full text-xs sm:text-sm pl-7 pr-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all font-mono">
                                    </div>
                                    <p class="text-[11px] text-stone-400 mt-1">Hanya huruf kecil, angka, titik, atau strip.</p>
                                </div>

                                <div>
                                    <label for="email" class="block text-xs font-bold text-stone-700 mb-1.5">
                                        Alamat Email <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="email"
                                           id="email"
                                           name="email"
                                           value="{{ old('email', $user->email) }}"
                                           required
                                           placeholder="nama@tokokue.com"
                                           class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Keamanan & Kata Sandi -->
                    <div class="pt-5 border-t border-stone-100">
                        <div class="flex items-center gap-2.5 pb-3 border-b border-stone-100 mb-5">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-[#5A121A]">Ganti Kata Sandi</h2>
                                <p class="text-[11px] text-stone-500">Kosongkan jika Anda tidak ingin mengubah kata sandi akun saat ini</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <!-- Kata Sandi Saat Ini -->
                            <div>
                                <label for="current_password" class="block text-xs font-bold text-stone-700 mb-1.5">
                                    Kata Sandi Saat Ini
                                </label>
                                <div class="relative">
                                    <input type="password"
                                           id="current_password"
                                           name="current_password"
                                           placeholder="Masukkan kata sandi saat ini"
                                           class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                                    <button type="button" onclick="togglePasswordVisibility('current_password', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-stone-400 hover:text-stone-600 focus:outline-none">
                                        <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-[11px] text-stone-400 mt-1">Wajib diisi jika Anda ingin mengubah ke kata sandi baru.</p>
                            </div>

                            <!-- Grid 2: Kata Sandi Baru & Konfirmasi -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="password" class="block text-xs font-bold text-stone-700 mb-1.5">
                                        Kata Sandi Baru
                                    </label>
                                    <div class="relative">
                                        <input type="password"
                                               id="password"
                                               name="password"
                                               placeholder="Minimal 8 karakter"
                                               class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                                        <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-stone-400 hover:text-stone-600 focus:outline-none">
                                            <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-xs font-bold text-stone-700 mb-1.5">
                                        Konfirmasi Kata Sandi Baru
                                    </label>
                                    <div class="relative">
                                        <input type="password"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               placeholder="Ulangi kata sandi baru"
                                               class="w-full text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                                        <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-stone-400 hover:text-stone-600 focus:outline-none">
                                            <svg class="w-4 h-4 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Action Buttons -->
                    <div class="pt-5 border-t border-stone-100 flex flex-col sm:flex-row items-center justify-end gap-3">
                        <a href="{{ route('staff.dashboard') }}"
                           class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 text-xs sm:text-sm font-semibold transition-colors text-center shadow-xs">
                            Batal
                        </a>

                        <button type="submit"
                                class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-[#6B0F1A] hover:bg-[#520A13] text-white text-xs sm:text-sm font-semibold shadow-xs transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Toggle Password Visibility Script -->
    <script>
        function togglePasswordVisibility(fieldId, btn) {
            const input = document.getElementById(fieldId);
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                btn.classList.add('text-[#6B0F1A]');
                btn.classList.remove('text-stone-400');
            } else {
                input.type = 'password';
                btn.classList.remove('text-[#6B0F1A]');
                btn.classList.add('text-stone-400');
            }
        }
    </script>
@endsection
