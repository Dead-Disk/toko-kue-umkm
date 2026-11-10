@php
    $storeSetting = \App\Models\StoreSetting::current();
    $storeName = $storeSetting?->store_name ?: 'Nak Mbarep';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - {{ $storeName }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-nav.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Instrument Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-[#F8D6D4] min-h-screen flex items-center justify-center antialiased text-stone-800 p-0 sm:p-4">

    <!-- Mobile Container (375px - 430px on desktop, 100% on mobile) -->
    <div class="w-full max-w-[420px] min-h-screen sm:min-h-[820px] sm:h-[844px] sm:rounded-[36px] sm:shadow-2xl sm:border sm:border-white/40 relative flex flex-col justify-between overflow-hidden bg-cover bg-center bg-no-repeat"
         style="background-image: url('{{ asset('backgrounds/login-background.png') }}'); background-color: #F8D6D4;">

        <!-- Top Content Section -->
        <div class="pt-10 sm:pt-14 px-6 flex flex-col items-center relative z-10">

            <!-- Logo & Brand Header -->
            <a href="{{ route('customer.index') }}" title="Ke Halaman Utama" class="flex flex-col items-center group transition-transform active:scale-95">
                <div class="w-[72px] h-[72px] rounded-full p-1 bg-white/70 border-2 border-white/90 shadow-sm flex items-center justify-center">
                    <img src="{{ asset('images/logo-login.png') }}" alt="Logo {{ $storeName }}" class="w-full h-full rounded-full object-cover">
                </div>

                <h1 class="mt-3 text-2xl sm:text-[26px] font-extrabold tracking-tight text-[#4E0E14] text-center">
                    {{ $storeName }}
                </h1>

                <div class="flex items-center gap-2 mt-1">
                    <span class="w-3.5 h-[1.5px] bg-[#6E1C23]/60 inline-block"></span>
                    <span class="text-xs font-semibold tracking-wider text-[#6E1C23]">Toko Kue</span>
                    <span class="w-3.5 h-[1.5px] bg-[#6E1C23]/60 inline-block"></span>
                </div>
            </a>

            <!-- Floating Login Card -->
            <div class="w-full max-w-[330px] mt-6 sm:mt-7 p-6 sm:p-7 rounded-[30px] bg-[#FFF5F2]/85 backdrop-blur-md border border-white/60 shadow-lg shadow-[#4E0E14]/5">
                
                <h2 class="text-xl font-bold text-[#4E0E14] text-center tracking-tight">
                    Selamat Datang!
                </h2>
                <p class="text-xs text-[#826A67] text-center mt-1 mb-5">
                    Silakan masuk ke akun Anda
                </p>

                <!-- Validation Errors Alert -->
                @if ($errors->any())
                    <div class="mb-4 p-2.5 rounded-2xl bg-rose-100/90 border border-rose-300 text-rose-900 text-xs text-center font-medium shadow-xs">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Login Form -->
                <form action="{{ route('login.store') }}" method="POST" class="space-y-3.5">
                    @csrf

                    <!-- Username Field -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4.5 flex items-center pointer-events-none text-[#5A1218]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input type="text"
                               id="username"
                               name="username"
                               value="{{ old('username') }}"
                               placeholder="Username"
                               required
                               autofocus
                               class="w-full pl-12 pr-4 py-3 rounded-full text-xs sm:text-sm text-[#4E0E14] placeholder-[#A48B88] bg-[#FDF0ED]/75 border border-[#DDBBB6] focus:outline-none focus:border-[#5A1218] focus:bg-white focus:ring-2 focus:ring-[#5A1218]/15 transition-all shadow-2xs">
                    </div>

                    <!-- Password Field -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4.5 flex items-center pointer-events-none text-[#5A1218]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password"
                               id="password"
                               name="password"
                               placeholder="Password"
                               required
                               class="w-full pl-12 pr-4 py-3 rounded-full text-xs sm:text-sm text-[#4E0E14] placeholder-[#A48B88] bg-[#FDF0ED]/75 border border-[#DDBBB6] focus:outline-none focus:border-[#5A1218] focus:bg-white focus:ring-2 focus:ring-[#5A1218]/15 transition-all shadow-2xs">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full py-3 px-4 rounded-full bg-[#5D131A] hover:bg-[#4E0E14] active:scale-[0.99] text-white text-xs sm:text-sm font-bold tracking-wide transition-all shadow-md shadow-[#5D131A]/20 cursor-pointer">
                        Masuk
                    </button>
                </form>
            </div>
        </div>

        <!-- Spacer / Bottom Area to let the cake illustration shine -->
        <div class="pb-6 px-6 relative z-10 flex justify-center">
            <a href="{{ route('customer.index') }}" class="text-[11px] font-medium text-[#7D2930]/80 hover:text-[#5D131A] transition underline decoration-[#7D2930]/30 hover:decoration-[#5D131A]">
                Kembali ke Beranda
            </a>
        </div>
    </div>

</body>
</html>
