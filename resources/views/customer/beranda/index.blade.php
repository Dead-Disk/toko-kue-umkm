<!DOCTYPE html>
<html lang="id" class="overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - {{ $storeSetting->store_name ?? 'Nak Mbarep' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FBF7F2] text-[#2C1810] min-h-screen antialiased flex flex-col font-sans overflow-x-clip">

    {{-- Top Navigation Bar --}}
    @include('customer.partials.navbar')

    {{-- Main Content Container --}}
    <main class="max-w-md md:max-w-2xl lg:max-w-5xl xl:max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-8 lg:py-10 w-full flex-1 space-y-10 sm:space-y-14 lg:space-y-20">

        {{-- Hidden semantic heading for SEO & test compatibility --}}
        <h1 class="sr-only">Selamat Datang di Toko Kue {{ $storeSetting->store_name ?? 'Nak Mbarep' }}</h1>

        {{-- Flash Notification Alerts --}}
        @if (session('success'))
            <div class="p-3.5 sm:p-4 bg-[#F2F7F4] border border-[#C6DFCF] text-[#1E4D32] rounded-xl text-xs sm:text-sm flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#2E7D52] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <a href="{{ route('cart.index') }}" class="underline font-semibold text-[#1E4D32] text-xs ml-3 shrink-0">Lihat Keranjang →</a>
            </div>
        @endif

        @if (session('error') || $errors->has('cart'))
            <div class="p-3.5 sm:p-4 bg-[#FDF2F2] border border-[#ECCACA] text-[#7A1C1C] rounded-xl text-xs sm:text-sm flex items-center gap-2">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#B32D2D] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span class="font-medium">{{ session('error') ?? $errors->first('cart') }}</span>
            </div>
        @endif

        {{-- 1. Hero & Banner Carousel Promosi Terpadu --}}
        <section aria-label="Hero dan Banner Promosi" class="w-full sm:pt-3 lg:pt-4">
            <x-banner-carousel :banners="$banners ?? collect()" :setting="$storeSetting ?? null" preview-id="customerBanner" :with-hero="true" />
        </section>

        {{-- 2. Section: Katalog Kue / Category Discovery (Data Murni dari $categories) --}}
        <section class="space-y-5 sm:space-y-7">
            <div class="flex items-end justify-between border-b border-[#E6D9CC] pb-3 sm:pb-4">
                <div>
                    <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-[0.2em] text-[#8C6D62]">Pilihan Kategori</p>
                    <h2 class="text-xl sm:text-2xl lg:text-3xl font-serif font-semibold text-[#2C1810] mt-0.5">
                        Katalog Kue
                    </h2>
                </div>
                <a href="{{ route('customer.produk.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium text-[#6B0F1A] hover:text-[#4A0911] transition group">
                    <span>Lihat Semua</span>
                    <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            {{-- Category Grid: 3 cols mobile, 4 cols sm, 6 cols lg+ --}}
            <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4 lg:gap-6">
                @forelse ($categories as $cat)
                    <a href="{{ route('customer.produk.index', ['kategori' => $cat->slug]) }}" class="group flex flex-col items-center">
                        <div class="w-full aspect-square rounded-2xl overflow-hidden bg-[#F4ECE3] border border-[#E6D9CC] group-hover:border-[#C87D87] transition-colors mb-2.5 flex items-center justify-center p-4 sm:p-5">
                            <x-category-icon :category="$cat" class="w-full h-full max-w-[56px] max-h-[56px] sm:max-w-[68px] sm:max-h-[68px] lg:max-w-[78px] lg:max-h-[78px] group-hover:scale-105 transition-transform duration-300" />
                        </div>
                        <span class="text-xs sm:text-sm font-medium text-[#2C1810] group-hover:text-[#6B0F1A] transition text-center line-clamp-1">
                            {{ $cat->nama_kategori }}
                        </span>
                    </a>
                @empty
                    <div class="col-span-3 sm:col-span-4 lg:col-span-6 text-center py-6 text-xs sm:text-sm text-[#8C6D62]">
                        Belum ada kategori kue.
                    </div>
                @endforelse
            </div>
        </section>

        {{-- 3. Section: Rekomendasi (Data Murni dari $produks) --}}
        <section class="space-y-5 sm:space-y-7">
            <div class="flex items-end justify-between border-b border-[#E6D9CC] pb-3 sm:pb-4">
                <div>
                    <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-[0.2em] text-[#8C6D62]">Kurasi Dapur</p>
                    <h2 class="text-xl sm:text-2xl lg:text-3xl font-serif font-semibold text-[#2C1810] mt-0.5 inline-flex items-center gap-1.5">
                        <span>Rekomendasi</span>
                        <span class="sr-only">Kue Terlaris / Favorit</span>
                    </h2>
                </div>
                <a href="{{ route('customer.produk.index') }}" class="inline-flex items-center gap-1.5 bg-[#6B0F1A] hover:bg-[#520912] text-white text-xs sm:text-sm font-medium px-4 py-2 sm:px-5 sm:py-2.5 rounded-full transition">
                    <span>Pesan Sekarang</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            {{-- Product Grid: 2 cols mobile, 4 cols desktop --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-5 lg:gap-6">
                @forelse ($produks as $produk)
                    @php
                        $isAvailable = $produk->status_stok === \App\Enums\StatusStok::Ready && $produk->stok > 0;
                    @endphp
                    <article class="relative bg-white rounded-2xl border border-[#EAE0D5] hover:border-[#D4B8AE] transition-colors flex flex-col justify-between group overflow-hidden cursor-pointer">
                        <div>
                            <div class="aspect-square w-full overflow-hidden bg-[#F4ECE3] relative">
                                @if ($produk->foto_url)
                                    <img src="{{ $produk->foto_url }}" alt="{{ $produk->nama_kue }}" class="w-full h-full object-cover group-hover:scale-103 transition-transform duration-500 {{ $isAvailable ? '' : 'grayscale opacity-75' }}">
                                @else
                                    <div class="w-full h-full p-6 flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                                        <x-category-icon :category="$produk->category" class="w-full h-full max-w-[76px] max-h-[76px] sm:max-w-[92px] sm:max-h-[92px]" />
                                    </div>
                                @endif

                                @unless ($isAvailable)
                                    <span class="absolute top-2.5 right-2.5 bg-[#2C1810]/85 text-[#FBF7F2] text-[10px] sm:text-xs font-medium tracking-wide px-2.5 py-0.5 rounded-full pointer-events-none">
                                        Habis
                                    </span>
                                @endunless
                            </div>

                            <div class="p-3 sm:p-4 pb-2 space-y-1">
                                <p class="text-[10px] sm:text-[11px] uppercase tracking-[0.14em] text-[#8C6D62] line-clamp-1">
                                    {{ $produk->category?->nama_kategori ?? 'Kue Pilihan' }} · Stok {{ $produk->stok }}
                                </p>

                                <a href="{{ route('customer.produk.show', $produk) }}"
                                   class="block font-serif font-semibold text-base sm:text-lg lg:text-xl text-[#2C1810] group-hover:text-[#6B0F1A] transition leading-snug line-clamp-1 focus:outline-none after:absolute after:inset-0 after:z-10 focus-visible:after:ring-2 focus-visible:after:ring-[#6B0F1A]/40 focus-visible:after:ring-inset"
                                   title="{{ $produk->nama_kue }}">
                                    {{ $produk->nama_kue }}
                                </a>

                                @if ($produk->deskripsi)
                                    <p class="text-xs text-[#6E564C] line-clamp-1 leading-relaxed" title="{{ $produk->deskripsi }}">
                                        {{ Str::limit($produk->deskripsi, 52) }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="px-3 sm:px-4 pb-3 sm:pb-4 pt-2 mt-1 border-t border-[#F2EAE2] flex items-center justify-between gap-2">
                            <span class="text-sm sm:text-base font-semibold text-[#6B0F1A]">
                                Rp {{ number_format($produk->harga, 0, ',', '.') }}
                            </span>

                            @if ($isAvailable)
                                <form action="{{ route('cart.add', $produk->id) }}" method="POST" class="relative z-20 inline-block" onclick="event.stopPropagation()">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="bg-[#6B0F1A] hover:bg-[#520912] active:scale-95 text-white p-2 sm:p-2.5 rounded-xl transition cursor-pointer flex items-center justify-center" title="Tambah {{ $produk->nama_kue }} ke Keranjang" aria-label="Tambah {{ $produk->nama_kue }} ke Keranjang">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <button type="button" disabled class="relative z-20 bg-[#EFE8E0] text-[#9E8B82] p-2 sm:p-2.5 rounded-xl cursor-not-allowed flex items-center justify-center" title="Stok Habis" aria-label="Stok Habis">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-2 lg:col-span-4 text-center py-8 text-xs sm:text-sm text-[#8C6D62]">
                        Belum ada produk rekomendasi saat ini.
                    </div>
                @endforelse
            </div>
        </section>

        {{-- 4. Section: Editorial Brand Story Strip (Tanpa Card, Memakai Data Nyata Toko) --}}
        <section class="border-y border-[#E6D9CC] py-10 sm:py-12 lg:py-16 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            {{-- Left: Story, Editorial Quote & Address --}}
            <div class="lg:col-span-6 space-y-4">
                <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-[0.2em] text-[#8C6D62]">
                    Tentang {{ $storeSetting->store_name ?? 'Nak Mbarep' }}
                </p>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-serif font-semibold text-[#2C1810] leading-snug">
                    Dari dapur kecil, untuk kebahagiaan besar.
                </h2>
                <p class="text-sm sm:text-base text-[#5E463B] leading-relaxed max-w-xl">
                    {{ filled($storeSetting?->store_description) ? $storeSetting->store_description : 'Setiap kue dibuat segar setiap hari dari bahan-bahan pilihan dengan penuh ketelitian untuk menyempurnakan momen berharga Anda bersama orang tersayang.' }}
                </p>

                {{-- Subtle Editorial Quote & Store Address --}}
                <div class="pt-1 pl-4 border-l-2 border-[#C87D87] space-y-1">
                    <p class="font-serif italic text-base sm:text-lg text-[#6B0F1A] leading-snug">
                        "Kue Lezat, Momen Lebih Manis"
                    </p>
                    <p class="text-xs text-[#6E564C] leading-relaxed">
                        {{ $storeSetting->store_address ?? 'Jl. Letjend Suprapto Gang 1 No. 4, Kepatihan, Tulungagung, Jawa Timur' }}
                    </p>
                </div>

                <div class="pt-2">
                    <a href="{{ route('customer.page', 'tentang-kami') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-[#6B0F1A] hover:text-[#4A0911] underline underline-offset-4 decoration-[#C87D87] transition">
                        <span>Baca Cerita Kami</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>

            {{-- Right: Open Airy Cake Cluster (1 Prominent Cake + 3 Supporting Cakes) --}}
            <div class="lg:col-span-6 flex items-center justify-center lg:justify-end">
                <div class="relative w-full max-w-[330px] sm:max-w-[390px] lg:max-w-[430px] h-[220px] sm:h-[250px] lg:h-[270px] select-none" aria-hidden="true">
                    {{-- Main Prominent Cake --}}
                    <div class="absolute right-0 top-1/2 -translate-y-1/2 w-[165px] sm:w-[195px] lg:w-[215px] flex items-center justify-center z-10">
                        <img src="{{ asset('images/cake-no-background/kue-butterfly-transparan-1x1.png') }}"
                             alt="Kue Pilihan {{ $storeSetting->store_name ?? 'Nak Mbarep' }}"
                             class="w-full h-auto object-contain drop-shadow-[0_16px_24px_rgba(44,24,16,0.18)]">
                    </div>

                    {{-- Supporting Cake 1 (Top-Left) --}}
                    <div class="absolute left-[46px] sm:left-[56px] top-1 w-[68px] sm:w-[80px] lg:w-[88px] flex items-center justify-center z-10">
                        <img src="{{ asset('images/cake-no-background/kue-cokelat-ceri.png') }}"
                             alt=""
                             class="w-full h-auto object-contain drop-shadow-[0_10px_16px_rgba(44,24,16,0.15)]">
                    </div>

                    {{-- Supporting Cake 2 (Mid-Left) --}}
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-[80px] sm:w-[94px] lg:w-[104px] flex items-center justify-center z-10">
                        <img src="{{ asset('images/cake-no-background/kue-cokelat-biskuit.png') }}"
                             alt=""
                             class="w-full h-auto object-contain drop-shadow-[0_10px_16px_rgba(44,24,16,0.15)]">
                    </div>

                    {{-- Supporting Cake 3 (Bottom-Left) --}}
                    <div class="absolute left-[42px] sm:left-[52px] bottom-1 w-[72px] sm:w-[84px] lg:w-[92px] flex items-center justify-center z-10">
                        <img src="{{ asset('images/cake-no-background/kue-cokelat-kotak.png') }}"
                             alt=""
                             class="w-full h-auto object-contain drop-shadow-[0_10px_16px_rgba(44,24,16,0.15)]">
                    </div>
                </div>
            </div>
        </section>

        {{-- 5. Section: Custom Cake Invitation ("Punya Desain Kue Impian?") --}}
        <section class="relative rounded-2xl lg:rounded-3xl bg-[#F3E9E2] border border-[#E2D3C6] overflow-hidden">
            {{-- Soft Fade Scrim behind Text for High Readability --}}
            <div aria-hidden="true"
                 class="absolute inset-x-0 top-0 h-[76%] bg-gradient-to-b from-[#F3E9E2] via-[#F3E9E2]/90 to-transparent sm:inset-y-0 sm:left-0 sm:right-auto sm:h-auto sm:w-[68%] sm:bg-gradient-to-r sm:from-[#F3E9E2] sm:via-[#F3E9E2]/92 sm:to-transparent z-[5] pointer-events-none">
            </div>

            {{-- Left Text & CTA --}}
            <div class="relative z-10 w-full sm:w-[58%] lg:w-[54%] px-6 pt-7 pb-[165px] sm:py-9 sm:pl-8 sm:pr-4 lg:py-12 lg:pl-10 xl:py-14 xl:pl-12 space-y-3 sm:space-y-4 text-left">
                <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-[0.2em] text-[#7A4B3E]">
                    Pesanan Khusus
                </p>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-serif font-semibold text-[#2C1810] leading-tight">
                    Punya Desain Kue Impian?
                </h2>
                <p class="text-xs sm:text-sm lg:text-base text-[#5E463B] max-w-md leading-relaxed">
                    Kami wujudkan dengan desain terbaik — dibuat dengan rasa cinta dan bahan berkualitas sesuai tema perayaan Anda.
                </p>

                <div class="pt-1 flex flex-wrap items-center justify-start gap-3 sm:gap-4">
                    <a href="{{ route('customer.custom.create') }}" class="inline-flex items-center gap-2 bg-[#6B0F1A] hover:bg-[#520912] text-white font-medium text-xs sm:text-sm px-5 py-2.5 sm:px-6 sm:py-3 rounded-full transition">
                        <span>Custom Cake</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    <span class="text-[10px] sm:text-xs text-[#7A4B3E] italic font-serif">
                        Konsultasikan bentuk, ukuran &amp; rasa pilihan Anda
                    </span>
                </div>
            </div>

            {{-- Right/Bottom Cake Photo (Mobile: full-bleed left-right rising from bottom; Desktop: larger flush right cropped) --}}
            <img src="{{ asset('images/cake-no-background/' . rawurlencode('Chocolate Drip Cake with Candy Toppings.png')) }}"
                 alt="Kue Custom {{ $storeSetting->store_name ?? 'Nak Mbarep' }}"
                 class="absolute z-0 bottom-0 left-1/2 -translate-x-1/2 translate-y-[44%] w-[114%] h-auto sm:left-auto sm:right-0 sm:translate-x-[28%] sm:translate-y-[12%] sm:w-auto sm:h-[145%] lg:h-[160%] xl:h-[170%] max-w-none object-contain pointer-events-none select-none drop-shadow-[0_-6px_20px_rgba(44,24,16,0.14)]">
        </section>

    </main>

    {{-- Footer --}}
    <footer class="bg-[#2C1810] text-[#F5EDE4] mt-12 lg:mt-16">
        <div class="max-w-md md:max-w-2xl lg:max-w-5xl xl:max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10 lg:py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-6 lg:gap-8 text-xs">
            {{-- Brand Column --}}
            <div class="lg:col-span-4 flex items-start gap-3.5">
                <img src="{{ asset('images/logo-footer.png') }}" alt="Nak Mbarep Logo" class="w-10 h-10 lg:w-12 lg:h-12 rounded-full object-cover border border-white/20 shrink-0">
                <div class="space-y-1">
                    <p class="font-serif text-base lg:text-lg font-semibold text-white leading-snug">
                        {{ $storeSetting->store_name ?? 'Nak Mbarep' }}
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
                        $cleanPhone = preg_replace('/[^0-9]/', '', (string) ($storeSetting?->store_phone ?? ''));
                        $hasContacts = filled($storeSetting?->instagram_url)
                            || filled($storeSetting?->facebook_url)
                            || filled($cleanPhone)
                            || filled($storeSetting?->tiktok_url);
                    @endphp

                    @if (filled($storeSetting?->instagram_url))
                        <a href="{{ $storeSetting->instagram_url }}" target="_blank" rel="noopener noreferrer" class="text-[#F5EDE4] hover:text-[#E5B8B0] transition" aria-label="Instagram">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    @endif

                    @if (filled($storeSetting?->facebook_url))
                        <a href="{{ $storeSetting->facebook_url }}" target="_blank" rel="noopener noreferrer" class="text-[#F5EDE4] hover:text-[#E5B8B0] transition" aria-label="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                    @endif

                    @if (filled($cleanPhone))
                        <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" rel="noopener noreferrer" class="text-[#F5EDE4] hover:text-[#E5B8B0] transition" aria-label="WhatsApp">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        </a>
                    @endif

                    @if (filled($storeSetting?->tiktok_url))
                        <a href="{{ $storeSetting->tiktok_url }}" target="_blank" rel="noopener noreferrer" class="text-[#F5EDE4] hover:text-[#E5B8B0] transition" aria-label="TikTok">
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
                        {{ $storeSetting->store_address ?? 'Jl. Letjend Suprapto Gang 1 No. 4, Kepatihan, Tulungagung, Jawa Timur' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Bottom Copyright Bar --}}
        <div class="border-t border-white/10 py-4 text-center text-xs text-[#D8C7BA]">
            &copy; {{ date('Y') }} {{ $storeSetting->store_name ?? 'Nak Mbarep' }}. All rights reserved.
        </div>
    </footer>

</body>
</html>
