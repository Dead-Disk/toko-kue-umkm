<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($product) ? $product->nama_kue.' - Detail Produk' : 'Katalog Kue - Toko Kue' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FDF2EE] text-gray-800 min-h-screen antialiased flex flex-col font-sans">
    {{-- Header Top Bar (matching beranda navbar) --}}
    @include('customer.partials.navbar')

    <main class="max-w-md md:max-w-2xl lg:max-w-5xl xl:max-w-7xl mx-auto px-4 lg:px-8 pt-4 lg:pt-8 pb-12 lg:pb-16 w-full flex-1">
        {{-- Flash messages & Toasts --}}
        @if (session('error'))
            <div data-toast-message="{{ session('error') }}" data-toast-type="error" hidden></div>
        @endif

        @if (session('success'))
            <div class="mb-4 lg:mb-6 p-3 sm:p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs sm:text-sm flex items-center gap-2 shadow-2xs">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 lg:mb-6 p-3 sm:p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs sm:text-sm shadow-2xs">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        @continue(session('error') === $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Detail Produk Section (displayed when on route customer.produk.show) --}}
        @isset($product)
            @php
                $maxPerOrder = $product->max_per_order ?? 5;
            @endphp

            <section class="mb-8 lg:mb-12 bg-white rounded-3xl lg:rounded-[32px] p-4 sm:p-6 lg:p-8 xl:p-10 shadow-sm border border-[#F4E1D9]">
                <div class="flex items-center justify-between mb-4 lg:mb-6 pb-3 lg:pb-4 border-b border-gray-100">
                    <div>
                        <a href="{{ route('customer.produk.index') }}" class="inline-flex items-center gap-1 text-xs sm:text-sm text-[#6B0F1A] font-semibold hover:underline mb-1">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            <span>Kembali ke Katalog</span>
                        </a>
                        <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900">Detail Produk</h2>
                    </div>
                    <span class="text-xs sm:text-sm px-2.5 py-1 sm:px-3 sm:py-1.5 rounded-full font-medium {{ $product->status_stok === \App\Enums\StatusStok::Ready ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                        @if ($product->status_stok === \App\Enums\StatusStok::Ready)
                            Ready ({{ $product->stok }})
                        @else
                            Habis
                        @endif
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 sm:gap-6 lg:gap-8 mb-6 lg:mb-8">
                    <div class="lg:col-span-5 aspect-square rounded-2xl lg:rounded-3xl overflow-hidden bg-[#FAF2EE] flex items-center justify-center">
                        @if ($product->foto)
                            <img src="{{ asset('storage/'.$product->foto) }}" alt="{{ $product->nama_kue }}" class="w-full h-full object-cover">
                        @else
                            <div class="text-center text-gray-400 p-4">
                                <svg class="w-16 h-16 lg:w-20 lg:h-20 mx-auto mb-2 text-rose-200" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.69 2 6 4.69 6 8v2H4c-1.1 0-2 .9-2 2v9c0 .55.45 1 1 1h18c.55 0 1-.45 1-1v-9c0-1.1-.9-2-2-2h-2V8c0-3.31-2.69-6-6-6zm0 2c2.21 0 4 1.79 4 4v2H8V8c0-2.21 1.79-4 4-4zm8 8v7H4v-7h16z"/></svg>
                                <span class="text-xs sm:text-sm">Foto kue belum diunggah</span>
                            </div>
                        @endif
                    </div>

                    <div class="lg:col-span-7 space-y-3 lg:space-y-4 flex flex-col justify-between">
                        <div>
                            <span class="inline-block text-xs sm:text-sm text-[#6B0F1A] font-medium bg-rose-50 px-2.5 py-0.5 sm:px-3 sm:py-1 rounded-full mb-1">
                                {{ $product->category?->nama_kategori ?? 'Umum' }}
                            </span>
                            <h3 class="text-lg sm:text-xl lg:text-3xl font-bold text-gray-900">{{ $product->nama_kue }}</h3>
                            <p class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-[#6B0F1A] mt-1 lg:mt-2">Rp{{ number_format($product->harga, 0, ',', '.') }}</p>
                            <p class="text-xs sm:text-sm lg:text-base text-gray-600 mt-2 lg:mt-3 leading-relaxed">
                                {{ $product->deskripsi ?: 'Kue lezat dengan bahan berkualitas tinggi, cocok untuk berbagai momen istimewa.' }}
                            </p>
                        </div>

                        @if ($product->foto)
                            <div>
                                <a href="{{ asset('storage/'.$product->foto) }}" target="_blank" rel="noopener" class="text-xs sm:text-sm text-[#6B0F1A] underline font-medium">Lihat Foto Ukuran Asli</a>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($product->status_stok === \App\Enums\StatusStok::Ready)
                    {{-- Form Tambah ke Keranjang --}}
                    <div class="pt-5 lg:pt-6 border-t border-gray-100">
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 mb-2 lg:mb-3">Tambah ke Keranjang</h3>
                        <form action="{{ route('cart.add', $product) }}" method="POST" class="space-y-3">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label for="cart_quantity" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                                    <input type="number" id="cart_quantity" name="quantity" value="1" min="1" max="{{ $maxPerOrder }}" data-max-per-order="{{ $maxPerOrder }}" data-max-order-message="Produk ini hanya dapat dipesan maksimal {{ $maxPerOrder }} pcs per transaksi." required class="w-full bg-white border border-[#E8B8AE] rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] outline-none">
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="cart_catatan" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Catatan untuk Kue Ini (opsional)</label>
                                    <input type="text" id="cart_catatan" name="catatan" value="{{ old('catatan') }}" maxlength="1000" placeholder="Catatan khusus (misal: tulisan ucapan, lilin, dll)" class="w-full bg-white border border-[#E8B8AE] rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] outline-none">
                                </div>
                            </div>
                            <button type="submit" class="w-full bg-[#6B0F1A] hover:bg-[#520B10] text-white font-semibold text-xs sm:text-sm lg:text-base py-2.5 lg:py-3 rounded-xl transition flex items-center justify-center gap-2 cursor-pointer shadow-xs">
                                <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                <span>Tambah ke Keranjang</span>
                            </button>
                        </form>
                    </div>

                    {{-- Form Pesanan Langsung --}}
                    <div class="pt-6 mt-6 border-t border-gray-100">
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 mb-3 lg:mb-4">Form Pesanan Langsung</h3>
                        <form action="{{ route('customer.pesanan.review') }}" method="POST" class="space-y-3.5 lg:space-y-4">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label for="nama_pemesan" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Nama Pemesan</label>
                                    <input type="text" id="nama_pemesan" name="nama_pemesan" value="{{ old('nama_pemesan', $draft['nama_pemesan'] ?? '') }}" required class="w-full bg-white border border-[#E8B8AE] rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] outline-none">
                                </div>

                                <div>
                                    <label for="nomor_wa" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">No HP / WhatsApp</label>
                                    <input type="tel" id="nomor_wa" name="nomor_wa" value="{{ old('nomor_wa', $draft['nomor_wa'] ?? '') }}" placeholder="08xxxxxxxxxx" required class="w-full bg-white border border-[#E8B8AE] rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] outline-none">
                                </div>
                            </div>

                            <div>
                                <label for="alamat_lengkap" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                                <textarea id="alamat_lengkap" name="alamat_lengkap" rows="2" required class="w-full bg-white border border-[#E8B8AE] rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] outline-none">{{ old('alamat_lengkap', $draft['alamat_lengkap'] ?? '') }}</textarea>
                            </div>

                            <div>
                                <label for="jumlah" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Jumlah Beli</label>
                                <input type="number" id="jumlah" name="jumlah" value="{{ old('jumlah', $draft['jumlah'] ?? 1) }}" min="1" max="{{ $maxPerOrder }}" data-max-per-order="{{ $maxPerOrder }}" data-max-order-message="Produk ini hanya dapat dipesan maksimal {{ $maxPerOrder }} pcs per transaksi." required class="w-full bg-white border border-[#E8B8AE] rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] outline-none">
                            </div>

                            <fieldset class="p-3.5 sm:p-4 bg-[#FAF2EE] rounded-2xl border border-[#F4E1D9]">
                                <legend class="text-xs sm:text-sm font-semibold text-gray-800 px-1">Opsi Pengiriman</legend>
                                <div class="mt-2 flex gap-4 text-xs sm:text-sm">
                                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                        <input type="radio" name="opsi_pengiriman" value="kurir_toko" @checked(old('opsi_pengiriman', $draft['opsi_pengiriman'] ?? 'kurir_toko') === 'kurir_toko') class="text-[#6B0F1A] focus:ring-[#6B0F1A]">
                                        <span>Diantar Kurir</span>
                                    </label>
                                    <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                        <input type="radio" name="opsi_pengiriman" value="ambil_sendiri" @checked(old('opsi_pengiriman', $draft['opsi_pengiriman'] ?? '') === 'ambil_sendiri') class="text-[#6B0F1A] focus:ring-[#6B0F1A]">
                                        <span>Ambil Sendiri</span>
                                    </label>
                                </div>
                            </fieldset>

                            @php
                                $bankSiap = $storeSetting->hasBankDetails();
                                $qrisSiap = $storeSetting->hasQrisImage();
                            @endphp

                            <div data-payment-group class="p-3.5 sm:p-4 bg-[#FAF2EE] rounded-2xl border border-[#F4E1D9]">
                                <fieldset>
                                    <legend class="text-xs sm:text-sm font-semibold text-gray-800 px-1">Metode Pembayaran</legend>
                                    <div class="mt-2 space-y-2 text-xs sm:text-sm">
                                        <div>
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                <input type="radio" name="metode_pembayaran" value="transfer_bank" @checked($bankSiap && old('metode_pembayaran', $draft['metode_pembayaran'] ?? 'transfer_bank') === 'transfer_bank') @disabled(! $bankSiap) class="text-[#6B0F1A] focus:ring-[#6B0F1A]">
                                                <span>Transfer Bank</span>
                                            </label>
                                            @unless ($bankSiap)
                                                <p class="text-[11px] text-amber-700 ml-5">Transfer Bank belum dikonfigurasi oleh penjual.</p>
                                            @endunless
                                        </div>

                                        <div>
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                <input type="radio" name="metode_pembayaran" value="qris" @checked(old('metode_pembayaran', $draft['metode_pembayaran'] ?? '') === 'qris') @disabled(! $qrisSiap) class="text-[#6B0F1A] focus:ring-[#6B0F1A]">
                                                <span>QRIS</span>
                                            </label>
                                            @unless ($qrisSiap)
                                                <p class="text-[11px] text-amber-700 ml-5">QRIS belum dikonfigurasi oleh penjual.</p>
                                            @endunless
                                        </div>

                                        <div>
                                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                                <input type="radio" name="metode_pembayaran" value="cash" @checked(old('metode_pembayaran', $draft['metode_pembayaran'] ?? '') === 'cash') class="text-[#6B0F1A] focus:ring-[#6B0F1A]">
                                                <span>Tunai / Bayar di Toko</span>
                                            </label>
                                        </div>
                                    </div>
                                </fieldset>

                                @if ($bankSiap)
                                    <div data-payment-detail="transfer_bank" hidden class="mt-3 p-3 bg-white rounded-xl border border-gray-100 text-xs sm:text-sm">
                                        <p class="font-semibold text-gray-800">Transfer Bank:</p>
                                        <p class="text-gray-600">{{ $storeSetting->bank_name }} - {{ $storeSetting->bank_account_number }} a.n. {{ $storeSetting->bank_account_holder }}</p>
                                    </div>
                                @endif

                                @if ($qrisSiap)
                                    <div data-payment-detail="qris" hidden class="mt-3 p-3 bg-white rounded-xl border border-gray-100 text-xs sm:text-sm">
                                        <p class="font-semibold text-gray-800 mb-2">Scan QRIS berikut untuk membayar:</p>
                                        <img src="{{ asset('storage/'.$storeSetting->qris_image_path) }}" alt="QRIS {{ $storeSetting->store_name }}" class="max-w-[180px] sm:max-w-[220px] rounded-lg border border-gray-100">
                                    </div>
                                @endif
                            </div>

                            <div>
                                <label for="catatan" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Catatan Khusus (opsional)</label>
                                <textarea id="catatan" name="catatan" rows="2" maxlength="1000" placeholder="Catatan khusus (misal: tulisan ucapan, lilin angka, dll)" class="w-full bg-white border border-[#E8B8AE] rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] outline-none">{{ old('catatan', $draft['catatan'] ?? '') }}</textarea>
                            </div>

                            <button type="submit" class="w-full bg-[#6B0F1A] hover:bg-[#520B10] text-white font-semibold text-xs sm:text-sm lg:text-base py-3 lg:py-3.5 rounded-xl transition cursor-pointer shadow-sm">
                                Lanjut ke Ringkasan Pesanan
                            </button>
                        </form>
                    </div>
                @else
                    <div class="pt-5 border-t border-gray-100 text-center">
                        <p class="text-xs sm:text-sm text-rose-600 font-medium">Maaf, produk ini sedang habis dan belum bisa dipesan.</p>
                    </div>
                @endif
            </section>
        @endisset

        {{-- Search & Filter Toggle Bar --}}
        <div class="flex items-center gap-2.5 sm:gap-3 lg:gap-4">
            {{-- Search Input (Matching Mockup with rounded white pill and pinkish border) --}}
            <div class="flex-1 relative bg-white rounded-2xl lg:rounded-3xl border border-[#E8B8AE] shadow-xs px-3.5 py-2.5 sm:px-4 sm:py-3 lg:px-5 lg:py-3.5 flex items-center gap-2.5 lg:gap-3 focus-within:ring-2 focus-within:ring-[#6B0F1A]/20 focus-within:border-[#6B0F1A] transition">
                <svg class="w-5 h-5 lg:w-6 lg:h-6 text-gray-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 20l-3.5-3.5"/>
                </svg>
                <input
                    type="text"
                    id="catalog-search"
                    value="{{ $search ?? '' }}"
                    placeholder="Cari kue favoritmu..."
                    autocomplete="off"
                    class="w-full bg-transparent border-0 p-0 text-sm sm:text-base text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-0"
                >
                <button type="button" id="search-clear-btn" class="hidden text-gray-400 hover:text-gray-600 p-0.5" aria-label="Hapus pencarian">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Filter Toggle Button (opens Sidebar Drawer) --}}
            <button
                type="button"
                id="filter-toggle-btn"
                title="Buka Filter Katalog"
                aria-label="Filter Katalog"
                class="relative p-2.5 sm:p-3 lg:px-5 lg:py-3.5 bg-white hover:bg-rose-50 border border-[#E8B8AE] text-[#6B0F1A] rounded-2xl lg:rounded-3xl shadow-xs transition flex items-center justify-center gap-2 cursor-pointer shrink-0"
            >
                <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M3 12h18M3 20h18M7 2v4M17 10v4M11 18v4" />
                </svg>
                <span class="hidden sm:inline font-semibold text-xs sm:text-sm lg:text-base">Filter</span>
                {{-- Active filter indicator dot --}}
                <span id="filter-active-dot" class="hidden absolute top-2 right-2 sm:top-2.5 sm:right-2.5 w-2.5 h-2.5 rounded-full bg-[#6B0F1A]"></span>
            </button>
        </div>

        {{-- Heading "Katalog Kue" (as shown in the sidebar mockup preview) --}}
        <h1 class="text-2xl sm:text-3xl lg:text-4xl xl:text-5xl font-serif font-bold text-[#6B0F1A] mt-3.5 sm:mt-5 lg:mt-7">Katalog Kue</h1>

        {{-- Tagline (Matching Mockup in tone and typography) --}}
        <p class="text-[#6B0F1A] font-medium text-xs sm:text-sm lg:text-base xl:text-lg mt-0.5 sm:mt-1 mb-3 sm:mb-5 lg:mb-6 tracking-wide">
            Maniskan harimu dengan pilihan kue istimewa!
        </p>

        {{-- Category Pills / Filter Tabs (Matching Mockup "Semua" pill) --}}
        <div class="flex items-center gap-2 sm:gap-2.5 lg:gap-3 overflow-x-auto lg:flex-wrap pb-2 sm:pb-3 scrollbar-none" id="category-pills">
            <button
                type="button"
                data-category-id="all"
                class="category-pill shrink-0 px-4 py-1.5 sm:px-5 sm:py-2 lg:px-6 lg:py-2.5 rounded-full text-xs sm:text-sm font-semibold transition cursor-pointer {{ empty($currentCategory) ? 'bg-[#6B0F1A] text-white shadow-xs' : 'bg-white text-[#6B0F1A] border border-[#E8B8AE] hover:bg-rose-50' }}"
            >
                Semua
            </button>

            @foreach ($categories as $cat)
                <button
                    type="button"
                    data-category-id="{{ $cat->id }}"
                    class="category-pill shrink-0 px-4 py-1.5 sm:px-5 sm:py-2 lg:px-6 lg:py-2.5 rounded-full text-xs sm:text-sm font-medium transition cursor-pointer {{ ($currentCategory ?? null) === $cat->id ? 'bg-[#6B0F1A] text-white shadow-xs' : 'bg-white text-[#6B0F1A] border border-[#E8B8AE] hover:bg-rose-50' }}"
                >
                    {{ $cat->nama_kategori }}
                </button>
            @endforeach
        </div>

        {{-- Product Cards Grid (Responsive Multi-Column Layout) --}}
        <div id="catalog-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-3.5 sm:gap-4 lg:gap-6 mt-3 sm:mt-5 lg:mt-6">
            @forelse ($products as $item)
                @php
                    $isReady = $item->status_stok === \App\Enums\StatusStok::Ready;
                @endphp
                <div
                    class="product-card bg-white rounded-3xl p-3 sm:p-3.5 lg:p-4 xl:p-5 shadow-xs border border-[#F4E1D9] hover:shadow-md transition-all flex flex-col justify-between"
                    data-product-name="{{ strtolower($item->nama_kue) }}"
                    data-category-id="{{ $item->category_id ?? '' }}"
                    data-status-stok="{{ $isReady ? 'ready' : 'habis' }}"
                    data-harga="{{ (int) $item->harga }}"
                >
                    <div>
                        {{-- Cake Image Container (Rounded-2xl as seen in mockup) --}}
                        <div class="aspect-square w-full rounded-2xl lg:rounded-3xl overflow-hidden bg-[#FAF2EE] relative mb-2.5 lg:mb-3 flex items-center justify-center">
                            @if ($item->foto)
                                @if ($isReady)
                                    <a href="{{ route('customer.produk.show', $item) }}" class="w-full h-full block">
                                        <img src="{{ asset('storage/'.$item->foto) }}" alt="{{ $item->nama_kue }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                    </a>
                                @else
                                    <img src="{{ asset('storage/'.$item->foto) }}" alt="{{ $item->nama_kue }}" class="w-full h-full object-cover grayscale opacity-75">
                                @endif
                            @else
                                {{-- Placeholder for cakes without uploaded photo (clean soft blank like card 2 Oreo Pom-pom) --}}
                                @if ($isReady)
                                    <a href="{{ route('customer.produk.show', $item) }}" class="w-full h-full flex flex-col items-center justify-center text-rose-300/80 p-3 hover:text-[#6B0F1A] transition">
                                        <svg class="w-12 h-12 lg:w-16 lg:h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.69 2 6 4.69 6 8v2H4c-1.1 0-2 .9-2 2v9c0 .55.45 1 1 1h18c.55 0 1-.45 1-1v-9c0-1.1-.9-2-2-2h-2V8c0-3.31-2.69-6-6-6zm0 2c2.21 0 4 1.79 4 4v2H8V8c0-2.21 1.79-4 4-4zm8 8v7H4v-7h16z"/></svg>
                                        <span class="text-[10px] sm:text-xs text-gray-400 mt-1">Toko Kue</span>
                                    </a>
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-300 p-3">
                                        <svg class="w-12 h-12 lg:w-16 lg:h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.69 2 6 4.69 6 8v2H4c-1.1 0-2 .9-2 2v9c0 .55.45 1 1 1h18c.55 0 1-.45 1-1v-9c0-1.1-.9-2-2-2h-2V8c0-3.31-2.69-6-6-6zm0 2c2.21 0 4 1.79 4 4v2H8V8c0-2.21 1.79-4 4-4zm8 8v7H4v-7h16z"/></svg>
                                    </div>
                                @endif
                            @endif

                            @unless ($isReady)
                                <span class="absolute top-2 left-2 bg-[#6B0F1A]/90 text-white text-[10px] sm:text-xs font-bold px-2 py-0.5 rounded-full shadow-xs">
                                    Habis
                                </span>
                            @endunless
                        </div>

                        {{-- Cake Title --}}
                        @if ($isReady)
                            <a href="{{ route('customer.produk.show', $item) }}" class="block font-bold text-gray-800 text-sm sm:text-base lg:text-lg leading-snug line-clamp-2 hover:text-[#6B0F1A] transition">
                                {{ $item->nama_kue }}
                            </a>
                        @else
                            <span class="block font-bold text-gray-700 text-sm sm:text-base lg:text-lg leading-snug line-clamp-2">
                                {{ $item->nama_kue }}
                            </span>
                        @endif
                    </div>

                    {{-- Bottom Row: Price & Shopping Cart / Order Button --}}
                    <div class="flex items-center justify-between mt-2 pt-1.5 sm:mt-3 sm:pt-2 border-t border-gray-50">
                        <span class="text-xs sm:text-sm lg:text-base font-bold text-[#540F18]">
                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                        </span>

                        @if ($isReady)
                            <form action="{{ route('cart.add', $item) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button
                                    type="submit"
                                    title="Tambah ke Keranjang"
                                    aria-label="Tambah {{ $item->nama_kue }} ke keranjang"
                                    class="bg-[#6B0F1A] hover:bg-[#520B10] active:scale-95 text-white p-2 sm:p-2.5 lg:p-3 rounded-xl lg:rounded-2xl shadow-xs transition flex items-center justify-center cursor-pointer"
                                >
                                    <svg class="w-4 h-4 lg:w-4.5 lg:h-4.5" fill="currentColor" viewBox="0 0 24 24"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                </button>
                            </form>
                        @else
                            <button
                                type="button"
                                disabled
                                class="bg-gray-100 text-gray-400 border border-gray-200 text-[11px] sm:text-xs font-medium px-2 py-1.5 sm:px-2.5 sm:py-2 rounded-xl cursor-not-allowed"
                            >
                                Pesan (Habis)
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-2 sm:col-span-3 md:col-span-4 lg:col-span-4 xl:col-span-5 text-center py-12 lg:py-16 bg-white rounded-3xl border border-[#F4E1D9] p-6">
                    <p class="text-sm sm:text-base text-gray-500 font-medium">Belum ada produk yang tersedia.</p>
                </div>
            @endforelse
        </div>

        {{-- No results message when filtering via search, category, or sidebar --}}
        <div id="no-search-results" class="hidden text-center py-12 lg:py-16 bg-white rounded-3xl border border-[#F4E1D9] p-6 mt-3 sm:mt-5 lg:mt-6">
            <svg class="w-12 h-12 lg:w-16 lg:h-16 mx-auto text-rose-200 mb-2 lg:mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" stroke-linejoin="round" d="M20 20l-3.5-3.5"/></svg>
            <p class="text-sm sm:text-base lg:text-lg font-semibold text-gray-700">Kue favoritmu tidak ditemukan</p>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">Coba sesuaikan kata kunci atau atur ulang filter katalog.</p>
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
    </main>

    {{-- Filter Sidebar Drawer & Backdrop --}}
    <div
        id="filter-backdrop"
        class="fixed inset-0 bg-black/40 z-50 transition-opacity duration-300 opacity-0 pointer-events-none"
    ></div>

    <aside
        id="filter-drawer"
        class="fixed top-0 right-0 bottom-0 w-80 sm:w-96 max-w-[85vw] bg-white z-50 shadow-2xl rounded-l-3xl flex flex-col justify-between transform transition-transform duration-300 ease-in-out translate-x-full"
        aria-label="Filter Katalog"
    >
        {{-- Drawer Top & Content --}}
        <div>
            {{-- Header --}}
            <div class="flex items-center justify-between p-5 pb-4 border-b border-gray-200">
                <div class="flex items-center gap-2.5 text-[#6B0F1A]">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M3 12h18M3 20h18M7 2v4M17 10v4M11 18v4" />
                    </svg>
                    <h2 class="text-lg font-bold">Filter Katalog</h2>
                </div>
                <button
                    type="button"
                    id="filter-close-btn"
                    class="text-gray-400 hover:text-gray-700 p-1.5 rounded-xl transition cursor-pointer"
                    aria-label="Tutup filter"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-5 space-y-6 overflow-y-auto max-h-[calc(100vh-170px)]">
                {{-- Section 1: Ketersediaan --}}
                <div>
                    <div class="flex items-center gap-2 text-[#6B0F1A] font-bold text-sm mb-3">
                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                        <span>Ketersediaan</span>
                    </div>

                    {{-- Toggle Ready Only (Matching image: dark maroon pill with checkmark) --}}
                    <button
                        type="button"
                        id="toggle-ready-only"
                        data-active="{{ $readyOnly ? 'true' : 'false' }}"
                        class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold flex items-center gap-2.5 transition cursor-pointer select-none {{ $readyOnly ? 'bg-[#6B0F1A] text-white shadow-xs' : 'bg-gray-100 text-gray-700 border border-gray-200 hover:bg-gray-200' }}"
                    >
                        <span id="ready-checkbox-box" class="w-4 h-4 rounded flex items-center justify-center shrink-0 {{ $readyOnly ? 'bg-white text-[#6B0F1A]' : 'border-2 border-gray-400 bg-white' }}">
                            <svg id="ready-check-svg" class="w-3 h-3 {{ $readyOnly ? '' : 'hidden' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        <span>Tampilkan kue yang ready di toko</span>
                    </button>
                </div>

                <hr class="border-gray-200">

                {{-- Section 2: Rentang Harga --}}
                <div>
                    <div class="flex items-center gap-2 text-[#6B0F1A] font-bold text-sm mb-3">
                        <svg class="w-4.5 h-4.5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"/>
                        </svg>
                        <span>Rentang Harga</span>
                    </div>

                    <div class="space-y-3.5 text-sm" id="price-radio-group">
                        @php
                            $activePrice = $priceRange ?? 'all';
                        @endphp
                        <label class="flex items-center gap-3 cursor-pointer select-none text-gray-800 font-medium">
                            <input type="radio" name="filter_price_range" value="all" @checked($activePrice === 'all') class="w-4 h-4 text-[#6B0F1A] border-gray-400 focus:ring-[#6B0F1A]">
                            <span>Semua harga</span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer select-none text-gray-800 font-medium">
                            <input type="radio" name="filter_price_range" value="under_25k" @checked($activePrice === 'under_25k') class="w-4 h-4 text-[#6B0F1A] border-gray-400 focus:ring-[#6B0F1A]">
                            <span>Di bawah Rp25.000</span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer select-none text-gray-800 font-medium">
                            <input type="radio" name="filter_price_range" value="25k_50k" @checked($activePrice === '25k_50k') class="w-4 h-4 text-[#6B0F1A] border-gray-400 focus:ring-[#6B0F1A]">
                            <span>Rp25.000 - Rp50.000</span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer select-none text-gray-800 font-medium">
                            <input type="radio" name="filter_price_range" value="above_50k" @checked($activePrice === 'above_50k') class="w-4 h-4 text-[#6B0F1A] border-gray-400 focus:ring-[#6B0F1A]">
                            <span>Di atas Rp50.000</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Drawer Footer: Reset & Terapkan buttons --}}
        <div class="p-5 border-t border-gray-200 flex items-center gap-3 bg-white">
            <button
                type="button"
                id="filter-reset-btn"
                class="flex-1 py-2.5 px-4 bg-white border-2 border-[#6B0F1A] text-[#6B0F1A] font-bold text-sm rounded-xl hover:bg-rose-50 transition cursor-pointer text-center"
            >
                Reset
            </button>
            <button
                type="button"
                id="filter-apply-btn"
                class="flex-1 py-2.5 px-4 bg-[#6B0F1A] hover:bg-[#520B10] text-white font-bold text-sm rounded-xl transition cursor-pointer shadow-sm text-center"
            >
                Terapkan
            </button>
        </div>
    </aside>

    {{-- Interactive Client-side script for Instant Filtering & Sidebar Drawer --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Elements
            const searchInput = document.getElementById('catalog-search');
            const clearBtn = document.getElementById('search-clear-btn');
            const pills = document.querySelectorAll('.category-pill');
            const cards = document.querySelectorAll('.product-card');
            const noResults = document.getElementById('no-search-results');

            // Drawer elements
            const toggleDrawerBtn = document.getElementById('filter-toggle-btn');
            const closeDrawerBtn = document.getElementById('filter-close-btn');
            const drawer = document.getElementById('filter-drawer');
            const backdrop = document.getElementById('filter-backdrop');
            const activeDot = document.getElementById('filter-active-dot');

            // Filter state elements
            const toggleReadyBtn = document.getElementById('toggle-ready-only');
            const readyCheckboxBox = document.getElementById('ready-checkbox-box');
            const readyCheckSvg = document.getElementById('ready-check-svg');
            const priceRadios = document.querySelectorAll('input[name="filter_price_range"]');
            const resetBtn = document.getElementById('filter-reset-btn');
            const applyBtn = document.getElementById('filter-apply-btn');

            let activeCategory = 'all';
            let isReadyOnly = toggleReadyBtn ? toggleReadyBtn.getAttribute('data-active') === 'true' : false;
            let activePriceRange = 'all';

            // Check if any initial price radio is checked
            priceRadios.forEach(r => {
                if (r.checked) {
                    activePriceRange = r.value;
                }
            });

            function openDrawer() {
                if (!drawer || !backdrop) return;
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
                backdrop.classList.add('opacity-100', 'pointer-events-auto');
                drawer.classList.remove('translate-x-full');
                drawer.classList.add('translate-x-0');
                document.body.style.overflow = 'hidden';
            }

            function closeDrawer() {
                if (!drawer || !backdrop) return;
                backdrop.classList.remove('opacity-100', 'pointer-events-auto');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                drawer.classList.remove('translate-x-0');
                drawer.classList.add('translate-x-full');
                document.body.style.overflow = '';
            }

            if (toggleDrawerBtn) {
                toggleDrawerBtn.addEventListener('click', openDrawer);
            }

            if (closeDrawerBtn) {
                closeDrawerBtn.addEventListener('click', closeDrawer);
            }

            if (backdrop) {
                backdrop.addEventListener('click', closeDrawer);
            }

            // Toggle Ready Only state
            if (toggleReadyBtn) {
                toggleReadyBtn.addEventListener('click', function () {
                    isReadyOnly = !isReadyOnly;
                    this.setAttribute('data-active', isReadyOnly ? 'true' : 'false');

                    if (isReadyOnly) {
                        this.classList.remove('bg-gray-100', 'text-gray-700', 'border', 'border-gray-200', 'hover:bg-gray-200');
                        this.classList.add('bg-[#6B0F1A]', 'text-white', 'shadow-xs');
                        readyCheckboxBox.classList.remove('border-2', 'border-gray-400', 'bg-white');
                        readyCheckboxBox.classList.add('bg-white', 'text-[#6B0F1A]');
                        readyCheckSvg.classList.remove('hidden');
                    } else {
                        this.classList.remove('bg-[#6B0F1A]', 'text-white', 'shadow-xs');
                        this.classList.add('bg-gray-100', 'text-gray-700', 'border', 'border-gray-200', 'hover:bg-gray-200');
                        readyCheckboxBox.classList.remove('bg-white', 'text-[#6B0F1A]');
                        readyCheckboxBox.classList.add('border-2', 'border-gray-400', 'bg-white');
                        readyCheckSvg.classList.add('hidden');
                    }
                });
            }

            function updateActiveDot() {
                if (!activeDot) return;
                const hasActiveFilter = isReadyOnly || activePriceRange !== 'all';
                if (hasActiveFilter) {
                    activeDot.classList.remove('hidden');
                } else {
                    activeDot.classList.add('hidden');
                }
            }

            function filterProducts() {
                const query = (searchInput ? searchInput.value : '').trim().toLowerCase();
                let visibleCount = 0;

                if (clearBtn) {
                    if (query.length > 0) {
                        clearBtn.classList.remove('hidden');
                    } else {
                        clearBtn.classList.add('hidden');
                    }
                }

                cards.forEach(card => {
                    const name = card.getAttribute('data-product-name') || '';
                    const categoryId = card.getAttribute('data-category-id') || '';
                    const statusStok = card.getAttribute('data-status-stok') || '';
                    const harga = parseInt(card.getAttribute('data-harga') || '0', 10);

                    // 1. Search Query
                    const matchesQuery = query === '' || name.includes(query);

                    // 2. Category
                    const matchesCategory = activeCategory === 'all' || categoryId === activeCategory;

                    // 3. Ready Only
                    const matchesReady = !isReadyOnly || statusStok === 'ready';

                    // 4. Price Range
                    let matchesPrice = true;
                    if (activePriceRange === 'under_25k') {
                        matchesPrice = harga < 25000;
                    } else if (activePriceRange === '25k_50k') {
                        matchesPrice = harga >= 25000 && harga <= 50000;
                    } else if (activePriceRange === 'above_50k') {
                        matchesPrice = harga > 50000;
                    }

                    if (matchesQuery && matchesCategory && matchesReady && matchesPrice) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (noResults) {
                    if (cards.length > 0 && visibleCount === 0) {
                        noResults.classList.remove('hidden');
                    } else {
                        noResults.classList.add('hidden');
                    }
                }

                updateActiveDot();
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterProducts);
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', function () {
                    searchInput.value = '';
                    filterProducts();
                    searchInput.focus();
                });
            }

            pills.forEach(pill => {
                pill.addEventListener('click', function () {
                    activeCategory = this.getAttribute('data-category-id') || 'all';

                    pills.forEach(p => {
                        p.classList.remove('bg-[#6B0F1A]', 'text-white', 'shadow-xs');
                        p.classList.add('bg-white', 'text-[#6B0F1A]', 'border', 'border-[#E8B8AE]');
                    });

                    this.classList.remove('bg-white', 'text-[#6B0F1A]', 'border', 'border-[#E8B8AE]');
                    this.classList.add('bg-[#6B0F1A]', 'text-white', 'shadow-xs');

                    filterProducts();
                });
            });

            if (applyBtn) {
                applyBtn.addEventListener('click', function () {
                    priceRadios.forEach(r => {
                        if (r.checked) {
                            activePriceRange = r.value;
                        }
                    });

                    filterProducts();
                    closeDrawer();
                });
            }

            if (resetBtn) {
                resetBtn.addEventListener('click', function () {
                    // Reset ready only
                    isReadyOnly = false;
                    if (toggleReadyBtn) {
                        toggleReadyBtn.setAttribute('data-active', 'false');
                        toggleReadyBtn.classList.remove('bg-[#6B0F1A]', 'text-white', 'shadow-xs');
                        toggleReadyBtn.classList.add('bg-gray-100', 'text-gray-700', 'border', 'border-gray-200', 'hover:bg-gray-200');
                        readyCheckboxBox.classList.remove('bg-white', 'text-[#6B0F1A]');
                        readyCheckboxBox.classList.add('border-2', 'border-gray-400', 'bg-white');
                        readyCheckSvg.classList.add('hidden');
                    }

                    // Reset price radio
                    activePriceRange = 'all';
                    priceRadios.forEach(r => {
                        r.checked = r.value === 'all';
                    });

                    filterProducts();
                    closeDrawer();
                });
            }

            // Run initial filter check (e.g., in case page loaded with query parameters)
            filterProducts();
        });
    </script>
</body>
</html>
