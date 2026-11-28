<!DOCTYPE html>
<html lang="id" class="overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - {{ $storeSetting->store_name ?? 'Nak Mbarep' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FBF7F2] text-[#2C1810] min-h-screen antialiased flex flex-col font-sans overflow-x-clip">

    {{-- Top Navigation Bar --}}
    @include('customer.partials.navbar')

    {{-- Main Content --}}
    <main class="max-w-md md:max-w-2xl lg:max-w-5xl xl:max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-16 w-full flex-1 space-y-12 sm:space-y-16 lg:space-y-20">

        {{-- 1. Editorial Brand Story Hero --}}
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-center">
            <div class="lg:col-span-7 space-y-5">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-footer.png') }}" alt="Logo {{ $storeSetting->store_name ?? 'Nak Mbarep' }}" class="w-11 h-11 sm:w-12 sm:h-12 rounded-full object-cover border border-[#E6D9CC] shrink-0">
                    <div>
                        <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-[0.2em] text-[#8C6D62]">
                            Cerita Kami
                        </p>
                        <p class="text-xs sm:text-sm font-serif italic text-[#6B0F1A]">
                            "Kue Lezat, Momen Lebih Manis"
                        </p>
                    </div>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-serif font-semibold text-[#2C1810] leading-[1.1] tracking-tight">
                    Tentang {{ $storeSetting->store_name ?? 'Nak Mbarep' }}
                </h1>

                <div class="space-y-4 text-sm sm:text-base lg:text-lg text-[#5E463B] leading-relaxed max-w-2xl pt-1">
                    <p>
                        Selamat datang di <strong class="font-semibold text-[#2C1810]">{{ $storeSetting->store_name ?? 'Nak Mbarep' }}</strong>. Kami adalah toko kue UMKM yang berdedikasi menghadirkan aneka kue lezat dan berkualitas tinggi untuk menyempurnakan setiap momen bahagia Anda.
                    </p>
                    @if (filled($storeSetting?->store_description))
                        <p>{{ $storeSetting->store_description }}</p>
                    @endif
                    <p>
                        <em class="font-serif text-[#6B0F1A] not-italic font-medium">"Dari dapur kecil, untuk kebahagiaan besar"</em> — Kami percaya bahwa setiap kebahagiaan layak dirayakan dengan kue terbaik. Setiap produk dibuat dari bahan-bahan pilihan dengan penuh ketelitian oleh tenaga terampil kami.
                    </p>
                </div>

                <div class="pt-2 flex flex-wrap items-center gap-4">
                    <a href="{{ route('customer.produk.index') }}" class="inline-flex items-center gap-2 bg-[#6B0F1A] hover:bg-[#520912] text-white text-xs sm:text-sm font-medium px-5 py-2.5 sm:px-6 sm:py-3 rounded-full transition">
                        <span>Jelajahi Katalog Kue</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    <a href="{{ route('customer.custom.create') }}" class="text-xs sm:text-sm font-medium text-[#2C1810] hover:text-[#6B0F1A] underline underline-offset-4 decoration-[#C87D87] transition">
                        Pesan Custom Cake
                    </a>
                </div>
            </div>

            {{-- Right Visual Frame --}}
            <div class="lg:col-span-5 pt-6 sm:pt-8 lg:pt-6">
                <div class="bg-[#F4ECE3] border border-[#E6D9CC] rounded-2xl lg:rounded-3xl p-6 sm:p-8 lg:p-10 flex flex-col items-center text-center space-y-5 overflow-visible">
                    <div class="relative w-full flex items-center justify-center overflow-visible">
                        <img src="{{ asset('images/cake-no-background/multi-cake.png') }}"
                             alt="Aneka Kue Pilihan {{ $storeSetting->store_name ?? 'Nak Mbarep' }}"
                             class="relative z-10 w-[114%] sm:w-[122%] lg:w-[128%] max-w-none h-auto -mt-10 sm:-mt-14 lg:-mt-16 object-contain">
                    </div>
                    <div class="border-t border-[#E0D2C4] pt-4 w-full">
                        <p class="font-serif italic text-base sm:text-lg text-[#2C1810]">
                            Dibuat segar setiap hari dari dapur kami
                        </p>
                        <p class="text-xs text-[#8C6D62] mt-1">
                            {{ $storeSetting->store_name ?? 'Nak Mbarep' }} · Tulungagung
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- 2. Craftsmanship & Nilai Brand (Editorial Numbered Columns, Tanpa Card) --}}
        <section class="border-y border-[#E6D9CC] py-10 sm:py-12 lg:py-14 space-y-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2">
                <div>
                    <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-[0.2em] text-[#8C6D62]">
                        Prinsip Dapur Kami
                    </p>
                    <h2 class="text-2xl sm:text-3xl font-serif font-semibold text-[#2C1810] mt-1">
                        Kualitas di Setiap Lapisan
                    </h2>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
                <div class="space-y-2.5 pt-2 md:pt-0 border-t md:border-t-0 border-[#E6D9CC]">
                    <span class="text-xs font-semibold tracking-[0.18em] text-[#C87D87]">01</span>
                    <h3 class="font-serif font-semibold text-xl sm:text-2xl text-[#2C1810]">Bahan Berkualitas</h3>
                    <p class="text-sm text-[#5E463B] leading-relaxed">
                        Menggunakan bahan alami dan pilihan terbaik tanpa pengawet berbahaya untuk menjaga cita rasa autentik di setiap gigitan.
                    </p>
                </div>

                <div class="space-y-2.5 pt-6 md:pt-0 border-t md:border-t-0 md:border-l border-[#E6D9CC] md:pl-8 lg:pl-10">
                    <span class="text-xs font-semibold tracking-[0.18em] text-[#C87D87]">02</span>
                    <h3 class="font-serif font-semibold text-xl sm:text-2xl text-[#2C1810]">Custom Cake</h3>
                    <p class="text-sm text-[#5E463B] leading-relaxed">
                        Desain kue impian Anda dapat kami wujudkan sesuai tema, ukuran, dan bentuk yang diinginkan untuk hari spesial Anda.
                    </p>
                </div>

                <div class="space-y-2.5 pt-6 md:pt-0 border-t md:border-t-0 md:border-l border-[#E6D9CC] md:pl-8 lg:pl-10">
                    <span class="text-xs font-semibold tracking-[0.18em] text-[#C87D87]">03</span>
                    <h3 class="font-serif font-semibold text-xl sm:text-2xl text-[#2C1810]">Penuh Rasa Cinta</h3>
                    <p class="text-sm text-[#5E463B] leading-relaxed">
                        Dikerjakan secara higienis dan dibuat segar setiap hari agar kue sampai ke tangan Anda dalam kondisi terbaik.
                    </p>
                </div>
            </div>
        </section>

        {{-- 3. Kunjungi Toko Kami (Split Info & Maps) --}}
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            <div class="lg:col-span-5 space-y-5">
                <div>
                    <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-[0.2em] text-[#8C6D62]">
                        Lokasi Bakery
                    </p>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-serif font-semibold text-[#2C1810] mt-1">
                        Kunjungi Toko Kami
                    </h2>
                </div>

                <div class="space-y-3 text-sm sm:text-base text-[#5E463B]">
                    <p class="leading-relaxed">
                        {{ $storeSetting->store_address ?? 'Jl. Letjend Suprapto Gang 1 No. 4, Kepatihan, Tulungagung, Jawa Timur' }}
                    </p>

                    @if (filled($storeSetting?->store_phone))
                        <p class="text-sm text-[#2C1810]">
                            <span class="text-[#8C6D62]">Telepon / WhatsApp:</span>
                            <span class="font-medium">{{ $storeSetting->store_phone }}</span>
                        </p>
                    @endif
                </div>

                <div class="pt-1">
                    <a href="https://maps.app.goo.gl/ucfkVK7QnMgQ1GGo6"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center justify-center gap-2 bg-[#6B0F1A] hover:bg-[#520912] text-white text-xs sm:text-sm font-medium px-5 py-2.5 sm:px-6 sm:py-3 rounded-full transition">
                        <span>Buka di Google Maps</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="rounded-2xl overflow-hidden border border-[#E6D9CC] bg-[#F4ECE3]">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d987.5898181334694!2d111.9103228!3d-8.0647879!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e78e318ae6e60a7%3A0x190c4d002f50b06!2sToko%20Kue%20Nak%20Mbarep!5e0!3m2!1sid!2sid!4v1795066676212!5m2!1sid!2sid"
                        width="100%"
                        height="340"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="strict-origin-when-cross-origin"
                        title="Peta Lokasi {{ $storeSetting->store_name ?? 'Toko Kue Nak Mbarep' }}">
                    </iframe>
                </div>
            </div>
        </section>

    </main>

    {{-- Footer --}}
    @php
        $setting = $storeSetting ?? \App\Models\StoreSetting::current();
    @endphp
    <footer class="bg-[#2C1810] text-[#F5EDE4] mt-12 lg:mt-16">
        <div class="max-w-md md:max-w-2xl lg:max-w-5xl xl:max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 lg:py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-6 lg:gap-8 text-xs">
            {{-- Brand Column --}}
            <div class="lg:col-span-4 flex items-start gap-3.5">
                <img src="{{ asset('images/logo-footer.png') }}" alt="Nak Mbarep Logo" class="w-10 h-10 lg:w-12 lg:h-12 rounded-full object-cover border border-white/20 shrink-0">
                <div class="space-y-1">
                    <p class="font-serif text-base lg:text-lg font-semibold text-white leading-snug">
                        {{ $setting?->store_name ?? 'Nak Mbarep' }}
                    </p>
                    <p class="text-[#D8C7BA] text-xs leading-relaxed">
                        Kue Lezat, Momen Lebih Manis.<br>
                        Dari dapur kecil, untuk kebahagiaan besar.
                    </p>
                </div>
            </div>

            {{-- Navigasi Column --}}
            <div class="lg:col-span-2 lg:border-l lg:border-white/10 lg:pl-6 space-y-2">
                <h4 class="font-semibold text-xs uppercase tracking-wider text-[#D8C7BA]">Navigasi</h4>
                <ul class="space-y-1.5 text-sm text-[#F5EDE4]/90">
                    <li><a href="{{ route('customer.index') }}" class="hover:text-white transition">Beranda</a></li>
                    <li><a href="{{ route('customer.produk.index') }}" class="hover:text-white transition">Katalog Kue</a></li>
                    <li><a href="{{ route('customer.page', 'tentang-kami') }}" class="hover:text-white transition">Tentang Kami</a></li>
                </ul>
            </div>

            {{-- Kontak Kami Column --}}
            <div class="lg:col-span-3 lg:border-l lg:border-white/10 lg:pl-6 space-y-2.5">
                <h4 class="font-semibold text-xs uppercase tracking-wider text-[#D8C7BA]">Kontak Kami</h4>
                <div class="flex flex-wrap items-center gap-3 pt-0.5">
                    @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', (string) ($setting?->store_phone ?? ''));
                        $hasContacts = filled($setting?->instagram_url)
                            || filled($setting?->facebook_url)
                            || filled($cleanPhone)
                            || filled($setting?->tiktok_url);
                    @endphp

                    @if (filled($setting?->instagram_url))
                        <a href="{{ $setting->instagram_url }}" target="_blank" rel="noopener noreferrer" class="text-[#F5EDE4] hover:text-[#E5B8B0] transition" aria-label="Instagram">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    @endif

                    @if (filled($setting?->facebook_url))
                        <a href="{{ $setting->facebook_url }}" target="_blank" rel="noopener noreferrer" class="text-[#F5EDE4] hover:text-[#E5B8B0] transition" aria-label="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                    @endif

                    @if (filled($cleanPhone))
                        <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" rel="noopener noreferrer" class="text-[#F5EDE4] hover:text-[#E5B8B0] transition" aria-label="WhatsApp">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        </a>
                    @endif

                    @if (filled($setting?->tiktok_url))
                        <a href="{{ $setting->tiktok_url }}" target="_blank" rel="noopener noreferrer" class="text-[#F5EDE4] hover:text-[#E5B8B0] transition" aria-label="TikTok">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.24 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                        </a>
                    @endif

                    @if (! $hasContacts)
                        <span class="text-[#D8C7BA] text-xs">-</span>
                    @endif
                </div>
            </div>

            {{-- Informasi Column --}}
            <div class="lg:col-span-3 lg:border-l lg:border-white/10 lg:pl-6 space-y-2">
                <h4 class="font-semibold text-xs uppercase tracking-wider text-[#D8C7BA]">Alamat Toko</h4>
                <div class="flex items-start gap-2 text-[#F5EDE4]/90 text-xs sm:text-sm leading-relaxed">
                    <svg class="w-4 h-4 shrink-0 mt-0.5 text-[#C87D87]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                    <span>
                        {{ $setting?->store_address ?? 'Jl. Letjend Suprapto Gang 1 No. 4, Kepatihan, Tulungagung, Jawa Timur' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Bottom Copyright Bar --}}
        <div class="border-t border-white/10 py-4 text-center text-xs text-[#D8C7BA]">
            &copy; {{ date('Y') }} {{ $setting?->store_name ?? 'Nak Mbarep' }}. All rights reserved.
        </div>
    </footer>

</body>
</html>
