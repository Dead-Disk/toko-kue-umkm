<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin Nak Mbarep</title>
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
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/logo-nav.png') }}" alt="Nak Mbarep Logo" class="h-6 w-auto brightness-0 invert">
            <span class="font-bold text-base tracking-tight">Nak Mbarep</span>
        </div>
        <a href="{{ route('admin.users.index') }}" class="text-xs text-stone-200 hover:text-white">← Kembali</a>
    </header>

    <!-- Desktop Sidebar (>= lg) -->
    <div class="hidden lg:block shrink-0 sticky top-0 h-screen">
        @include('admin.partials.sidebar', ['active' => 'users'])
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8 xl:p-10 overflow-y-auto">
        <div class="max-w-2xl mx-auto">
            <!-- Breadcrumbs / Top Back -->
            <div class="mb-6">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-xs text-[#6B0F1A] font-semibold hover:underline mb-2">
                    ← Kembali ke Kelola User
                </a>
                <h1 class="text-2xl font-extrabold text-[#5A121A] tracking-tight">Edit User: {{ $user->name }}</h1>
                <p class="text-xs text-stone-500 mt-0.5">Perbarui profil, hak akses role, atau reset password akun.</p>
            </div>

            <!-- Error Alerts -->
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-xs">
                    <p class="font-bold mb-1.5 flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Terdapat kesalahan pengisian:
                    </p>
                    <ul class="list-disc list-inside space-y-1 text-rose-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 p-6 sm:p-8">
                <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- User Header Info Box -->
                    <div class="p-3.5 rounded-xl bg-stone-50/70 border border-stone-200/70 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#6B0F1A] text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-xs">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-stone-800 truncate">{{ $user->name }}</p>
                            <p class="text-[11px] text-stone-500 font-mono">id: #{{ $user->id }} &bull; dibuat {{ $user->created_at->format('d M Y') }}</p>
                        </div>
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-stone-700 mb-1.5">
                            Nama Lengkap
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name', $user->name) }}"
                               required
                               class="w-full text-xs px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                    </div>

                    <!-- Username & Email (2 Grid) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="username" class="block text-xs font-bold text-stone-700 mb-1.5">
                                Username (untuk login)
                            </label>
                            <input type="text"
                                   id="username"
                                   name="username"
                                   value="{{ old('username', $user->username) }}"
                                   required
                                   class="w-full text-xs px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all font-mono">
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold text-stone-700 mb-1.5">
                                Alamat Email
                            </label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $user->email) }}"
                                   required
                                   class="w-full text-xs px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                        </div>
                    </div>

                    <!-- Pilihan Role -->
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-2">
                            Pilih Role / Hak Akses
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Role Staff -->
                            <label class="relative flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer hover:bg-stone-50 transition-all border-stone-200">
                                <input type="radio"
                                       name="role"
                                       value="staff"
                                       @checked(old('role', $user->role) === 'staff')
                                       class="mt-1 text-[#6B0F1A] focus:ring-[#6B0F1A]">
                                <div>
                                    <p class="text-xs font-bold text-stone-800">Staff Toko</p>
                                    <p class="text-[11px] text-stone-500 mt-0.5 leading-relaxed">
                                        Dapat mengoperasikan kasir, melihat daftar pesanan &amp; stok produk (akses operasional).
                                    </p>
                                </div>
                            </label>

                            <!-- Role Admin -->
                            <label class="relative flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer hover:bg-stone-50 transition-all border-stone-200">
                                <input type="radio"
                                       name="role"
                                       value="admin"
                                       @checked(old('role', $user->role) === 'admin')
                                       class="mt-1 text-[#6B0F1A] focus:ring-[#6B0F1A]">
                                <div>
                                    <p class="text-xs font-bold text-stone-800">Administrator</p>
                                    <p class="text-[11px] text-stone-500 mt-0.5 leading-relaxed">
                                        Akses penuh ke semua menu: produk, kategori, pengaturan toko, dan manajemen user.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Reset Password Section (Opsional) -->
                    <div class="pt-4 border-t border-stone-100">
                        <div class="mb-3">
                            <h3 class="text-xs font-bold text-stone-800">Ganti Password (Opsional)</h3>
                            <p class="text-[11px] text-stone-400">Biarkan kedua kolom di bawah kosong jika tidak ingin mengubah password akun.</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-xs font-medium text-stone-600 mb-1">
                                    Password Baru
                                </label>
                                <input type="password"
                                       id="password"
                                       name="password"
                                       placeholder="Kosongkan bila tidak diubah"
                                       class="w-full text-xs px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-medium text-stone-600 mb-1">
                                    Ulangi Password Baru
                                </label>
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       placeholder="Kosongkan bila tidak diubah"
                                       class="w-full text-xs px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.users.index') }}"
                           class="px-4 py-2.5 text-xs font-semibold rounded-xl border border-stone-200 hover:bg-stone-50 text-stone-700 transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-6 py-2.5 text-xs font-semibold rounded-xl bg-[#6B0F1A] hover:bg-[#520A13] text-white shadow-xs transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
