<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - {{ $storeSetting->store_name ?? 'Nak Mbarep' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FAF2EE] text-gray-800 min-h-screen antialiased flex flex-col font-sans">

    {{-- Top Navigation Bar --}}
    @include('customer.partials.navbar')

    {{-- Main Content --}}
    <main class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-7xl mx-auto px-4 lg:px-8 py-8 lg:py-12 w-full flex-1 space-y-6 lg:space-y-10">
        <div class="bg-white rounded-3xl lg:rounded-[32px] p-6 sm:p-8 lg:p-12 xl:p-16 border border-[#F4E1D9] shadow-xs lg:shadow-sm space-y-6 lg:space-y-10">
            <div class="text-center space-y-2 lg:space-y-3">
                <img src="{{ asset('images/logo-footer.png') }}" alt="Logo Nak Mbarep" class="w-20 h-20 sm:w-24 sm:h-24 lg:w-32 lg:h-32 mx-auto rounded-full object-cover border-2 lg:border-4 border-rose-200 shadow-xs lg:shadow-sm">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-serif font-bold text-[#540F18]">
                    Tentang {{ $storeSetting->store_name ?? 'Nak Mbarep' }}
                </h1>
                <p class="text-xs sm:text-sm lg:text-base xl:text-lg text-[#7A2832] italic font-serif">
                    "Kue Lezat, Momen Lebih Manis ♡"
                </p>
            </div>

            <div class="prose max-w-none text-gray-700 text-sm sm:text-base lg:text-lg leading-relaxed space-y-4 lg:space-y-6 border-t border-b border-gray-100 py-6 lg:py-10">
                <p>
                    Selamat datang di <strong>{{ $storeSetting->store_name ?? 'Nak Mbarep' }}</strong>! Kami adalah toko kue UMKM yang berdedikasi menghadirkan aneka kue lezat dan berkualitas tinggi untuk menyempurnakan setiap momen bahagia Anda.
                </p>
                <p>
                    <em>"Dari dapur kecil, untuk kebahagiaan besar ♡"</em> — Kami percaya bahwa setiap kebahagiaan layak dirayakan dengan kue terbaik. Setiap produk dibuat dari bahan-bahan pilihan dengan penuh cinta dan ketelitian oleh tenaga terampil kami.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 lg:gap-8 pt-2 lg:pt-4">
                    <div class="bg-[#FAF2EE] p-4 sm:p-6 lg:p-8 rounded-2xl lg:rounded-3xl text-center border border-[#F4E1D9]/50 shadow-2xs">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 mx-auto mb-2.5 lg:mb-3.5 rounded-2xl bg-white border border-[#F4E1D9] flex items-center justify-center text-[#6B0F1A] shadow-2xs">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 lg:w-8 lg:h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-8a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8M4 16s.5-1 2-1 2.5 2 4 2 2.5-2 4-2 2.5 2 4 2 2-1 2-1M2 21h20M7 8v2M12 8v2M17 8v2M7 4h.01M12 4h.01M17 4h.01"/>
                            </svg>
                        </div>
                        <h3 class="font-serif font-bold text-[#540F18] text-sm sm:text-base lg:text-xl">Bahan Berkualitas</h3>
                        <p class="text-xs sm:text-sm lg:text-base text-gray-600 mt-1 lg:mt-2">Menggunakan bahan alami dan premium tanpa pengawet berbahaya.</p>
                    </div>
                    <div class="bg-[#FAF2EE] p-4 sm:p-6 lg:p-8 rounded-2xl lg:rounded-3xl text-center border border-[#F4E1D9]/50 shadow-2xs">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 mx-auto mb-2.5 lg:mb-3.5 rounded-2xl bg-white border border-[#F4E1D9] flex items-center justify-center text-[#6B0F1A] shadow-2xs">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 lg:w-8 lg:h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z"/>
                            </svg>
                        </div>
                        <h3 class="font-serif font-bold text-[#540F18] text-sm sm:text-base lg:text-xl">Custom Cake</h3>
                        <p class="text-xs sm:text-sm lg:text-base text-gray-600 mt-1 lg:mt-2">Desain kue impian Anda bisa kami wujudkan sesuai pesanan.</p>
                    </div>
                    <div class="bg-[#FAF2EE] p-4 sm:p-6 lg:p-8 rounded-2xl lg:rounded-3xl text-center border border-[#F4E1D9]/50 shadow-2xs">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 mx-auto mb-2.5 lg:mb-3.5 rounded-2xl bg-white border border-[#F4E1D9] flex items-center justify-center text-[#6B0F1A] shadow-2xs">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 lg:w-8 lg:h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                            </svg>
                        </div>
                        <h3 class="font-serif font-bold text-[#540F18] text-sm sm:text-base lg:text-xl">Penuh Rasa Cinta</h3>
                        <p class="text-xs sm:text-sm lg:text-base text-gray-600 mt-1 lg:mt-2">Dibuat secara higienis, fresh setiap hari untuk pelanggan.</p>
                    </div>
                </div>
            </div>

            <div class="bg-[#FAF0EB] rounded-2xl lg:rounded-3xl p-4 sm:p-6 lg:p-8 flex flex-col sm:flex-row items-center justify-between gap-4 lg:gap-8 border border-[#F4DDD5]">
                <div class="space-y-1 text-center sm:text-left">
                    <h4 class="font-serif font-bold text-[#540F18] text-sm sm:text-base lg:text-2xl">Kunjungi Toko Kami</h4>
                    <p class="text-xs sm:text-sm lg:text-base text-gray-600 mt-0.5 lg:mt-1">
                        {{ $storeSetting->store_address ?? 'Jl. Letjend Suprapto Gang 1 No. 4, Kepatihan, Tulungagung, Jawa Timur' }}
                    </p>
                </div>
                <a href="{{ route('customer.produk.index') }}" class="bg-[#6B0F1A] hover:bg-[#520912] text-white text-xs sm:text-sm lg:text-base font-semibold px-5 py-2.5 sm:px-6 sm:py-3 lg:px-8 lg:py-3.5 rounded-full transition shadow-xs whitespace-nowrap">
                    Lihat Katalog Kue →
                </a>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    @php
        $setting = $storeSetting ?? \App\Models\StoreSetting::current();
    @endphp
    <footer class="bg-[#4D626C] text-white mt-8 lg:mt-12">
        <div class="max-w-md md:max-w-2xl lg:max-w-5xl xl:max-w-7xl mx-auto px-4 lg:px-8 py-6 lg:py-10 xl:py-12 grid grid-cols-12 gap-3 sm:gap-6 lg:gap-8 text-xs items-start">
            {{-- Brand Column --}}
            <div class="col-span-4 sm:col-span-3 flex items-center gap-2 lg:gap-4">
                <img src="{{ asset('images/logo-footer.png') }}" alt="Nak Mbarep Logo" class="w-8 h-8 sm:w-9 sm:h-9 lg:w-12 lg:h-12 rounded-full object-cover border border-white/20 shrink-0">
                <div>
                    <p class="text-slate-100 text-[10px] sm:text-xs lg:text-base leading-tight font-medium">
                        Kue Lezat,<br>Momen Lebih Manis
                    </p>
                    <p class="text-slate-300 text-[9px] sm:text-[10px] lg:text-xs mt-1 hidden lg:block">
                        Dari dapur kecil, untuk kebahagiaan besar ♡
                    </p>
                </div>
            </div>

            {{-- Navigasi Column --}}
            <div class="col-span-3 sm:col-span-3 border-l border-white/20 pl-2.5 sm:pl-3 lg:pl-6 space-y-1 lg:space-y-2.5">
                <h4 class="font-semibold text-[11px] sm:text-xs lg:text-base text-white">Navigasi</h4>
                <ul class="space-y-1 lg:space-y-2 text-slate-200 text-[10px] sm:text-xs lg:text-sm">
                    <li><a href="{{ route('customer.index') }}" class="hover:text-amber-100 transition">Beranda</a></li>
                    <li><a href="{{ route('customer.produk.index') }}" class="hover:text-amber-100 transition">Katalog Kue</a></li>
                    <li><a href="{{ route('customer.page', 'tentang-kami') }}" class="hover:text-amber-100 transition">Tentang Kami</a></li>
                </ul>
            </div>

            {{-- Kontak Kami Column --}}
            <div class="col-span-2 sm:col-span-3 border-l border-white/20 pl-2.5 sm:pl-3 lg:pl-6 space-y-1 lg:space-y-2.5">
                <h4 class="font-semibold text-[11px] sm:text-xs lg:text-base text-white">Kontak Kami</h4>
                <div class="flex flex-wrap items-center gap-2 lg:gap-3 pt-0.5">
                    @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', (string) ($setting?->store_phone ?? ''));
                        $hasContacts = filled($setting?->instagram_url)
                            || filled($setting?->facebook_url)
                            || filled($cleanPhone)
                            || filled($setting?->tiktok_url);
                    @endphp

                    @if (filled($setting?->instagram_url))
                        <a href="{{ $setting->instagram_url }}" target="_blank" rel="noopener noreferrer" class="text-white hover:text-amber-200 transition" aria-label="Instagram">
                            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 lg:w-5 lg:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    @endif

                    @if (filled($setting?->facebook_url))
                        <a href="{{ $setting->facebook_url }}" target="_blank" rel="noopener noreferrer" class="text-white hover:text-amber-200 transition" aria-label="Facebook">
                            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 lg:w-5 lg:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                    @endif

                    @if (filled($cleanPhone))
                        <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" rel="noopener noreferrer" class="text-white hover:text-amber-200 transition" aria-label="WhatsApp">
                            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 lg:w-5 lg:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        </a>
                    @endif

                    @if (filled($setting?->tiktok_url))
                        <a href="{{ $setting->tiktok_url }}" target="_blank" rel="noopener noreferrer" class="text-white hover:text-amber-200 transition" aria-label="TikTok">
                            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 lg:w-5 lg:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.24 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                        </a>
                    @endif

                    @if (! $hasContacts)
                        <span class="text-slate-300 text-[10px] sm:text-xs">-</span>
                    @endif
                </div>
            </div>

            {{-- Informasi Column --}}
            <div class="col-span-3 sm:col-span-3 border-l border-white/20 pl-2.5 sm:pl-3 lg:pl-6 space-y-1 lg:space-y-2.5">
                <h4 class="font-semibold text-[11px] sm:text-xs lg:text-base text-white">Informasi</h4>
                <div class="flex items-start gap-1 lg:gap-2 text-slate-200 text-[10px] sm:text-xs lg:text-sm leading-snug">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 lg:w-5 lg:h-5 shrink-0 mt-0.5 text-rose-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                    <span>
                        {{ $setting?->store_address ?? 'Jl. Letjend Suprapto Gang 1 No. 4, Kepatihan, Tulungagung, Jawa Timur' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Bottom Copyright Bar --}}
        <div class="border-t border-white/10 py-3 text-center text-[10px] sm:text-xs text-slate-300">
            &copy; {{ date('Y') }} {{ $setting?->store_name ?? 'Nak Mbarep' }}. Semua Hak Dilindungi.
        </div>
    </footer>

</body>
</html>

