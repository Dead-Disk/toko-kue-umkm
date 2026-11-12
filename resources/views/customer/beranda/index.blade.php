<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - {{ $storeSetting->store_name ?? 'Nak Mbarep' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FAF2EE] text-gray-800 min-h-screen antialiased flex flex-col font-sans">

    {{-- Top Navigation Bar --}}
    @include('customer.partials.navbar')

    {{-- Main Content Container --}}
    <main class="max-w-md md:max-w-2xl lg:max-w-5xl xl:max-w-7xl mx-auto px-4 lg:px-8 py-3.5 sm:py-5 lg:py-8 w-full flex-1 space-y-4 sm:space-y-6 lg:space-y-10">

        {{-- Hidden semantic heading for SEO & test compatibility --}}
        <h1 class="sr-only">Selamat Datang di Toko Kue {{ $storeSetting->store_name ?? 'Nak Mbarep' }}</h1>

        {{-- Flash Notification Alerts --}}
        @if (session('success'))
            <div class="p-2.5 sm:p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs sm:text-sm flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <a href="{{ route('cart.index') }}" class="underline font-semibold text-emerald-900 text-[11px] sm:text-xs ml-2">Lihat Keranjang →</a>
            </div>
        @endif

        @if (session('error') || $errors->has('cart'))
            <div class="p-2.5 sm:p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs sm:text-sm flex items-center gap-1.5 sm:gap-2 shadow-2xs">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span class="font-medium">{{ session('error') ?? $errors->first('cart') }}</span>
            </div>
        @endif

        {{-- 1. Hero Promo Banner --}}
        <section class="relative overflow-hidden rounded-[24px] lg:rounded-[32px] bg-[#FAF0EB] border border-[#F4DDD5] shadow-xs">
            <div class="p-3.5 sm:p-5 lg:p-10 xl:p-14 grid grid-cols-12 gap-3 sm:gap-6 lg:gap-10 items-center relative z-10">
                {{-- Left Text Info --}}
                <div class="col-span-7 lg:col-span-6 space-y-2 sm:space-y-3 lg:space-y-5 xl:space-y-6">
                    {{-- Mini Brand Tag --}}
                    <div class="inline-flex items-center gap-1.5 lg:gap-2 text-[#6B0F1A]">
                        <img src="{{ asset('images/logo-nav.png') }}" alt="" class="w-4.5 h-4.5 sm:w-5 sm:h-5 lg:w-7 lg:h-7 rounded-full object-cover">
                        <span class="font-serif font-bold text-[11px] sm:text-xs lg:text-base tracking-tight">Nak Mbarep <span class="text-rose-500 font-sans">Kue</span></span>
                        <span class="text-[9px] sm:text-[10px] lg:text-sm text-rose-800/80 font-normal">· Kue Lezat, Momen Lebih Manis ♡</span>
                    </div>

                    {{-- Main Headline --}}
                    <h2 class="text-base sm:text-xl md:text-2xl lg:text-4xl xl:text-5xl font-serif font-bold text-[#540F18] leading-tight">
                        Kue Spesial untuk<br>Setiap Momen Berharga
                    </h2>

                    {{-- Quote Badge --}}
                    <div class="inline-block bg-white/70 border border-dashed border-[#DE9DA7] text-[#6B0F1A] text-[10px] sm:text-xs lg:text-base px-2.5 py-0.5 lg:px-5 lg:py-1.5 rounded-full font-medium shadow-2xs">
                        Karena setiap kebahagiaan layak dirayakan dengan kue terbaik ♡
                    </div>

                    {{-- Cute Chef Mascot --}}
                    <div class="pt-0.5 lg:pt-2 flex items-center gap-1.5 lg:gap-3">
                        <img src="{{ asset('images/hero-mascot.png') }}" alt="Maskot Nak Mbarep" class="h-11 sm:h-13 lg:h-20 xl:h-24 w-auto object-contain">
                        <span class="text-[10px] sm:text-xs lg:text-base text-[#7A2832] italic">Dari dapur kecil, untuk kebahagiaan besar ♡</span>
                    </div>
                </div>

                {{-- Right Hero Cake Image with Floating Badges --}}
                <div class="col-span-5 lg:col-span-6 relative flex items-center justify-center">
                    <div class="relative w-full max-w-[175px] sm:max-w-[220px] lg:max-w-none xl:max-w-[480px] aspect-square rounded-2xl lg:rounded-3xl overflow-hidden shadow-xs lg:shadow-md bg-white/50 p-0.5 lg:p-2">
                        <img src="{{ asset('images/hero-strawberry-cake.png') }}" alt="Strawberry Cake Spesial Nak Mbarep" class="w-full h-full object-cover rounded-xl lg:rounded-2xl">

                        {{-- Floating Badge Top Right --}}
                        <div class="absolute -top-1 -right-1 lg:top-3 lg:right-3 bg-white/95 border border-[#DE9DA7] text-[#6B0F1A] text-[8px] sm:text-[9px] lg:text-sm px-1.5 py-0.5 lg:px-4 lg:py-1.5 rounded-full font-semibold shadow-2xs lg:shadow-sm">
                            Rasa Lezat Kualitas Terbaik Selalu ♡
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- 2. Section: Katalog Kue (Data Murni dari $categories) --}}
        <section class="space-y-2.5 sm:space-y-3.5 lg:space-y-5">
            <div class="flex items-center justify-between">
                <h2 class="text-sm sm:text-base md:text-lg lg:text-2xl font-serif font-bold text-[#540F18]">
                    Katalog Kue
                </h2>
                <a href="{{ route('customer.produk.index') }}" class="inline-flex items-center gap-1 lg:gap-1.5 bg-[#6B0F1A] hover:bg-[#520912] text-white text-[11px] sm:text-xs lg:text-sm font-semibold px-3 py-1 sm:px-4 sm:py-1.5 lg:px-5 lg:py-2 rounded-full transition shadow-xs">
                    <span>Lihat Semua</span>
                    <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 lg:w-4 lg:h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            {{-- Category Grid: 4 cols mobile, 6 cols on lg+ --}}
            <div class="grid grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-3 lg:gap-5 xl:gap-6">
                @forelse ($categories as $cat)
                    <a href="{{ route('customer.produk.index', ['kategori' => $cat->slug]) }}" class="group flex flex-col items-center">
                        <div class="w-full aspect-[4/3] lg:aspect-square rounded-xl lg:rounded-2xl overflow-hidden bg-white shadow-xs lg:shadow-sm border border-[#F4E1D9] mb-1 lg:mb-2.5 flex items-center justify-center">
                            @if ($cat->products->first()?->foto)
                                <img src="{{ asset('storage/' . $cat->products->first()->foto) }}" alt="{{ $cat->nama_kategori }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-rose-300 bg-[#FAF2EE]">
                                    <svg class="w-7 h-7 sm:w-9 sm:h-9 lg:w-12 lg:h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.69 2 6 4.69 6 8v2H4c-1.1 0-2 .9-2 2v9c0 .55.45 1 1 1h18c.55 0 1-.45 1-1v-9c0-1.1-.9-2-2-2h-2V8c0-3.31-2.69-6-6-6zm0 2c2.21 0 4 1.79 4 4v2H8V8c0-2.21 1.79-4 4-4zm8 8v7H4v-7h16z"/></svg>
                                </div>
                            @endif
                        </div>
                        <span class="text-[11px] sm:text-xs lg:text-sm xl:text-base font-semibold text-gray-800 group-hover:text-[#6B0F1A] transition text-center line-clamp-1">
                            {{ $cat->nama_kategori }}
                        </span>
                    </a>
                @empty
                    <div class="col-span-4 lg:col-span-6 text-center py-4 lg:py-8 text-xs sm:text-sm lg:text-base text-gray-400">
                        Belum ada kategori kue.
                    </div>
                @endforelse
            </div>
        </section>

        {{-- 3. Section: Rekomendasi (Data Murni dari $produks) --}}
        <section class="space-y-2.5 sm:space-y-3.5 lg:space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm sm:text-base md:text-lg lg:text-2xl font-serif font-bold text-[#540F18] inline-flex items-center gap-1.5">
                        <span>Rekomendasi</span>
                        <span class="sr-only">Kue Terlaris / Favorit</span>
                    </h2>
                </div>
                <a href="{{ route('customer.produk.index') }}" class="inline-flex items-center gap-1 lg:gap-1.5 bg-[#6B0F1A] hover:bg-[#520912] text-white text-[11px] sm:text-xs lg:text-sm font-semibold px-3 py-1 sm:px-4 sm:py-1.5 lg:px-5 lg:py-2 rounded-full transition shadow-xs">
                    <span>Pesan Sekarang</span>
                    <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 lg:w-4 lg:h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            {{-- Product Grid: responsive columns --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 sm:gap-3 lg:gap-5 xl:gap-6">
                @forelse ($produks as $produk)
                    <div class="bg-white rounded-2xl lg:rounded-3xl p-2 sm:p-3 lg:p-4 xl:p-5 border border-[#F3E1DC] shadow-2xs hover:shadow-md transition flex flex-col justify-between group">
                        <div>
                            <a href="{{ route('customer.produk.show', $produk) }}" class="block aspect-square rounded-xl lg:rounded-2xl overflow-hidden bg-[#FAF2EE] mb-1.5 sm:mb-2.5 lg:mb-3 relative">
                                @if ($produk->foto)
                                    <img src="{{ asset('storage/'.$produk->foto) }}" alt="{{ $produk->nama_kue }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-rose-300 p-1 text-center bg-[#FAF2EE]">
                                        <svg class="w-6 h-6 sm:w-8 sm:h-8 lg:w-10 lg:h-10 mb-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.69 2 6 4.69 6 8v2H4c-1.1 0-2 .9-2 2v9c0 .55.45 1 1 1h18c.55 0 1-.45 1-1v-9c0-1.1-.9-2-2-2h-2V8c0-3.31-2.69-6-6-6zm0 2c2.21 0 4 1.79 4 4v2H8V8c0-2.21 1.79-4 4-4zm8 8v7H4v-7h16z"/></svg>
                                        <span class="text-[9px] sm:text-[10px] leading-tight text-gray-400">{{ $storeSetting->store_name ?? 'Nak Mbarep' }}</span>
                                    </div>
                                @endif

                                @if ($produk->status_stok === \App\Enums\StatusStok::Habis || $produk->stok < 1)
                                    <span class="absolute top-1 right-1 lg:top-2 lg:right-2 bg-rose-600/90 text-white text-[8px] sm:text-[9px] lg:text-xs font-bold px-1.5 py-0.5 lg:px-2.5 lg:py-1 rounded-full">
                                        Habis
                                    </span>
                                @endif
                            </a>

                            <a href="{{ route('customer.produk.show', $produk) }}" class="block font-semibold text-[11px] sm:text-xs lg:text-sm xl:text-base text-gray-900 group-hover:text-[#6B0F1A] transition leading-tight line-clamp-1" title="{{ $produk->nama_kue }}">
                                {{ $produk->nama_kue }}
                            </a>
                            <p class="text-[9px] sm:text-[10px] lg:text-xs text-gray-500 mt-0.5 sm:mt-1 line-clamp-1">
                                {{ $produk->category?->nama_kategori ?? 'Kue Pilihan' }}
                            </p>
                            <p class="text-[9px] sm:text-[10px] lg:text-xs text-gray-400 mt-0.5">
                                Stok: {{ $produk->stok }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between pt-1.5 sm:pt-2.5 mt-1 sm:mt-2 border-t border-gray-100">
                            <span class="text-[11px] sm:text-xs lg:text-sm xl:text-base font-bold text-[#540F18]">
                                Rp {{ number_format($produk->harga, 0, ',', '.') }}
                            </span>

                            @if ($produk->status_stok === \App\Enums\StatusStok::Ready && $produk->stok > 0)
                                <form action="{{ route('cart.add', $produk->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="bg-[#6B0F1A] hover:bg-[#520912] active:scale-95 text-white p-1 sm:p-1.5 lg:p-2 rounded-md sm:rounded-lg lg:rounded-xl transition shadow-2xs cursor-pointer flex items-center justify-center" title="Tambah {{ $produk->nama_kue }} ke Keranjang">
                                        <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 lg:w-4 lg:h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <button type="button" disabled class="bg-gray-200 text-gray-400 p-1 sm:p-1.5 lg:p-2 rounded-md sm:rounded-lg lg:rounded-xl cursor-not-allowed flex items-center justify-center" title="Stok Habis">
                                    <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 lg:w-4 lg:h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 sm:col-span-3 lg:col-span-4 xl:col-span-5 text-center py-6 lg:py-10 text-xs sm:text-sm lg:text-base text-gray-400">
                        Belum ada produk rekomendasi saat ini.
                    </div>
                @endforelse
            </div>
        </section>

        {{-- 4. Section: Custom Cake Banner ("Punya Desain Kue Impian?") --}}
        <section class="rounded-[24px] lg:rounded-[32px] bg-gradient-to-r from-[#DFD3E3] via-[#E7DBE6] to-[#EFE4E8] border border-[#DACCDC] p-3.5 sm:p-5 lg:p-10 xl:p-14 shadow-xs relative overflow-hidden">
            <div class="grid grid-cols-12 gap-3 sm:gap-6 lg:gap-10 items-center">
                {{-- Left Text & Buttons --}}
                <div class="col-span-8 lg:col-span-7 space-y-2 sm:space-y-3 lg:space-y-5">
                    <div>
                        <h2 class="text-base sm:text-xl md:text-2xl lg:text-4xl xl:text-5xl font-serif font-bold text-[#3B1422] leading-tight">
                            Punya Desain Kue Impian?
                        </h2>
                        <p class="font-serif italic text-[11px] sm:text-xs lg:text-base text-[#5C2335] mt-0.5 sm:mt-1 lg:mt-2">
                            Kami Wujudkan dengan Desain Terbaik
                        </p>
                        <p class="font-serif font-bold text-[11px] sm:text-xs lg:text-base text-[#3B1422] mt-0.5 lg:mt-1">
                            Dibuat dengan Rasa Cinta &amp; Bahan Berkualitas
                        </p>
                    </div>

                    {{-- Call-to-Action Buttons --}}
                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2.5 pt-0.5">
                        <a href="{{ route('customer.custom.create') }}" class="bg-white hover:bg-rose-50 text-[#540F18] font-semibold text-[10px] sm:text-xs lg:text-base px-3 py-1.5 sm:px-4 sm:py-2 lg:px-6 lg:py-3 rounded-full border border-[#D5C2CF] shadow-xs transition">
                            Custom Cake
                        </a>
                    </div>

                    {{-- Bear Mascot Note --}}
                    <div class="flex items-center gap-1.5 sm:gap-2 lg:gap-3 pt-0.5 lg:pt-2">
                        <img src="{{ asset('images/mascot-bear-new.png') }}" alt="Maskot Nak Mbarep" class="h-10 sm:h-12 lg:h-16 xl:h-20 w-auto object-contain">
                        <span class="text-[9px] sm:text-[10px] lg:text-sm xl:text-base text-[#521C2F] font-medium">
                            Jangan ragu untuk bertanya ya! ♡
                        </span>
                    </div>
                </div>

                {{-- Right Card Photo Frame --}}
                <div class="col-span-4 lg:col-span-5 flex justify-end">
                    <div class="bg-white p-1 lg:p-2 xl:p-2.5 rounded-2xl lg:rounded-3xl shadow-sm lg:shadow-md border border-white/80 w-full max-w-[120px] sm:max-w-[140px] lg:max-w-[280px] xl:max-w-[340px]">
                        <div class="aspect-square rounded-xl lg:rounded-2xl overflow-hidden bg-gray-100">
                            <img src="{{ asset('images/custom-card-new.png') }}" alt="Kue Custom Nak Mbarep" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    {{-- Footer --}}
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
                        $cleanPhone = preg_replace('/[^0-9]/', '', (string) ($storeSetting?->store_phone ?? ''));
                        $hasContacts = filled($storeSetting?->instagram_url)
                            || filled($storeSetting?->facebook_url)
                            || filled($cleanPhone)
                            || filled($storeSetting?->tiktok_url);
                    @endphp

                    @if (filled($storeSetting?->instagram_url))
                        {{-- Instagram --}}
                        <a href="{{ $storeSetting->instagram_url }}" target="_blank" rel="noopener noreferrer" class="text-white hover:text-amber-200 transition" aria-label="Instagram">
                            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 lg:w-5 lg:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    @endif

                    @if (filled($storeSetting?->facebook_url))
                        {{-- Facebook --}}
                        <a href="{{ $storeSetting->facebook_url }}" target="_blank" rel="noopener noreferrer" class="text-white hover:text-amber-200 transition" aria-label="Facebook">
                            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 lg:w-5 lg:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                    @endif

                    @if (filled($cleanPhone))
                        {{-- WhatsApp --}}
                        <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" rel="noopener noreferrer" class="text-white hover:text-amber-200 transition" aria-label="WhatsApp">
                            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 lg:w-5 lg:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        </a>
                    @endif

                    @if (filled($storeSetting?->tiktok_url))
                        {{-- TikTok --}}
                        <a href="{{ $storeSetting->tiktok_url }}" target="_blank" rel="noopener noreferrer" class="text-white hover:text-amber-200 transition" aria-label="TikTok">
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
                        {{ $storeSetting->store_address ?? 'Jl. Letjend Suprapto Gang 1 No. 4, Kepatihan, Tulungagung, Jawa Timur' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Bottom Copyright Bar --}}
        <div class="border-t border-white/10 py-3 text-center text-[10px] sm:text-xs text-slate-300">
            &copy; {{ date('Y') }} {{ $storeSetting->store_name ?? 'Nak Mbarep' }}. Semua Hak Dilindungi.
        </div>
    </footer>

</body>
</html>
