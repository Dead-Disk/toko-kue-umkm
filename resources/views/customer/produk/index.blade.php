<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($product) ? $product->nama_kue.' - Detail Produk' : 'Katalog Kue - '.($storeSetting->store_name ?? 'Nak Mbarep') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;0,700;1,400;1,600&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FBF7F2] text-[#2C1810] min-h-screen antialiased flex flex-col font-sans">

    {{-- Top Navigation Bar --}}
    @include('customer.partials.navbar')

    <main class="max-w-md md:max-w-2xl lg:max-w-5xl xl:max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 sm:pt-8 lg:pt-10 pb-12 lg:pb-16 w-full flex-1">
        {{-- Flash messages & Toasts --}}
        @if (session('error'))
            <div data-toast-message="{{ session('error') }}" data-toast-type="error" hidden></div>
        @endif

        @if (session('success'))
            <div class="mb-6 p-3.5 sm:p-4 bg-[#F2F7F4] border border-[#C6DFCF] text-[#1E4D32] rounded-xl text-xs sm:text-sm flex items-center gap-2">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#2E7D52] shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-3.5 sm:p-4 bg-[#FDF2F2] border border-[#ECCACA] text-[#7A1C1C] rounded-xl text-xs sm:text-sm">
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

            <section class="mb-10 lg:mb-14 bg-white rounded-2xl lg:rounded-3xl p-5 sm:p-7 lg:p-10 border border-[#E6D9CC]">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-6 pb-4 border-b border-[#EFE6DD]">
                    <div>
                        <a href="{{ route('customer.produk.index') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm text-[#6B0F1A] font-medium hover:underline mb-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            <span>Kembali ke Katalog</span>
                        </a>
                        <h2 class="text-2xl sm:text-3xl font-serif font-semibold text-[#2C1810]">Detail Produk</h2>
                    </div>
                    <span class="text-xs sm:text-sm px-3 py-1 rounded-full font-medium {{ $product->status_stok === \App\Enums\StatusStok::Ready ? 'bg-[#F2F7F4] text-[#1E4D32] border border-[#C6DFCF]' : 'bg-[#FDF2F2] text-[#7A1C1C] border border-[#ECCACA]' }}">
                        @if ($product->status_stok === \App\Enums\StatusStok::Ready)
                            Ready ({{ $product->stok }})
                        @else
                            Habis
                        @endif
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-6 lg:gap-10 mb-8">
                    <div class="lg:col-span-5 aspect-square rounded-2xl overflow-hidden bg-[#F4ECE3] border border-[#EAE0D5] flex items-center justify-center">
                        @if ($product->foto_url)
                            <img src="{{ $product->foto_url }}" alt="{{ $product->nama_kue }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full p-8 sm:p-12 flex flex-col items-center justify-center text-center">
                                <x-category-icon :category="$product->category" class="w-32 h-32 sm:w-40 sm:h-40 max-w-full max-h-full mb-3" />
                                <span class="text-xs sm:text-sm text-[#8C6D62] font-medium">Foto kue belum diunggah</span>
                            </div>
                        @endif
                    </div>

                    <div class="lg:col-span-7 space-y-4 flex flex-col justify-between">
                        <div class="space-y-2.5">
                            <p class="text-xs uppercase tracking-[0.18em] text-[#8C6D62] font-semibold">
                                {{ $product->category?->nama_kategori ?? 'Umum' }}
                            </p>
                            <h3 class="text-2xl sm:text-3xl lg:text-4xl font-serif font-semibold text-[#2C1810] leading-tight">{{ $product->nama_kue }}</h3>
                            <p class="text-xl sm:text-2xl lg:text-3xl font-semibold text-[#6B0F1A]">Rp{{ number_format($product->harga, 0, ',', '.') }}</p>
                            <p class="text-sm sm:text-base text-[#5E463B] pt-1 leading-relaxed">
                                {{ $product->deskripsi ?: 'Kue lezat dengan bahan berkualitas tinggi, cocok untuk berbagai momen istimewa.' }}
                            </p>
                        </div>

                        @if ($product->foto_url)
                            <div>
                                <a href="{{ $product->foto_url }}" target="_blank" rel="noopener" class="text-xs sm:text-sm text-[#6B0F1A] underline underline-offset-4 font-medium">Lihat Foto Ukuran Asli</a>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($product->status_stok === \App\Enums\StatusStok::Ready)
                    {{-- Form Pesanan Langsung (aksi utama) --}}
                    <div class="pt-6 border-t border-[#EFE6DD]">
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div>
                                <h3 class="flex items-center gap-2.5 text-base sm:text-lg font-serif font-semibold text-[#2C1810]">
                                    <span class="w-6 h-6 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    </span>
                                    Form Pesanan Langsung
                                </h3>
                                <p class="text-xs text-[#6E564C] mt-1 ml-8.5">Isi data pemesan, lalu lanjut ke ringkasan pesanan.</p>
                            </div>
                            <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full bg-[#F2F7F4] text-[#1E4D32] border border-[#C6DFCF] text-[11px] font-medium shrink-0">
                                Pesan Sekarang
                            </span>
                        </div>

                        <form id="direct-order-form" action="{{ route('customer.pesanan.review') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="nama_pemesan" class="block text-xs sm:text-sm font-medium text-[#2C1810] mb-1.5">Nama Pemesan</label>
                                    <input type="text" id="nama_pemesan" name="nama_pemesan" value="{{ old('nama_pemesan', $draft['nama_pemesan'] ?? '') }}" required class="w-full bg-[#FBF7F2] border border-[#DDD0C4] rounded-xl px-3.5 py-2.5 text-sm text-[#2C1810] focus:bg-white focus:ring-2 focus:ring-[#6B0F1A]/15 focus:border-[#6B0F1A] outline-none transition">
                                </div>

                                <div>
                                    <label for="nomor_wa" class="block text-xs sm:text-sm font-medium text-[#2C1810] mb-1.5">No HP / WhatsApp</label>
                                    <input type="tel" id="nomor_wa" name="nomor_wa" value="{{ old('nomor_wa', $draft['nomor_wa'] ?? '') }}" placeholder="08xxxxxxxxxx" required class="w-full bg-[#FBF7F2] border border-[#DDD0C4] rounded-xl px-3.5 py-2.5 text-sm text-[#2C1810] focus:bg-white focus:ring-2 focus:ring-[#6B0F1A]/15 focus:border-[#6B0F1A] outline-none transition">
                                </div>
                            </div>

                            <div>
                                <label for="alamat_lengkap" class="block text-xs sm:text-sm font-medium text-[#2C1810] mb-1.5">Alamat Lengkap</label>
                                <textarea id="alamat_lengkap" name="alamat_lengkap" rows="2" required class="w-full bg-[#FBF7F2] border border-[#DDD0C4] rounded-xl px-3.5 py-2.5 text-sm text-[#2C1810] focus:bg-white focus:ring-2 focus:ring-[#6B0F1A]/15 focus:border-[#6B0F1A] outline-none transition">{{ old('alamat_lengkap', $draft['alamat_lengkap'] ?? '') }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="jumlah" class="block text-xs sm:text-sm font-medium text-[#2C1810] mb-1.5">Jumlah Beli</label>
                                    <input type="number" id="jumlah" name="jumlah" value="{{ old('jumlah', $draft['jumlah'] ?? 1) }}" min="1" max="{{ $maxPerOrder }}" data-max-per-order="{{ $maxPerOrder }}" data-max-order-message="Produk ini hanya dapat dipesan maksimal {{ $maxPerOrder }} pcs per transaksi." required class="w-full bg-[#FBF7F2] border border-[#DDD0C4] rounded-xl px-3.5 py-2.5 text-sm text-[#2C1810] focus:bg-white focus:ring-2 focus:ring-[#6B0F1A]/15 focus:border-[#6B0F1A] outline-none transition">
                                </div>

                                <div>
                                    <label for="tanggal_ambil" class="block text-xs sm:text-sm font-medium text-[#2C1810] mb-1.5">Tanggal Ambil</label>
                                    <input type="date" id="tanggal_ambil" name="tanggal_ambil" value="{{ old('tanggal_ambil', $draft['tanggal_ambil'] ?? date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" class="w-full bg-[#FBF7F2] border border-[#DDD0C4] rounded-xl px-3.5 py-2.5 text-sm text-[#2C1810] focus:bg-white focus:ring-2 focus:ring-[#6B0F1A]/15 focus:border-[#6B0F1A] outline-none transition">
                                </div>
                            </div>

                            <fieldset class="p-4 bg-[#FBF7F2] rounded-xl border border-[#E6D9CC]">
                                <legend class="text-xs sm:text-sm font-semibold text-[#2C1810] px-1">Opsi Pengiriman</legend>
                                <div class="mt-2 flex gap-5 text-xs sm:text-sm text-[#2C1810]">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="opsi_pengiriman" value="kurir_toko" @checked(old('opsi_pengiriman', $draft['opsi_pengiriman'] ?? 'kurir_toko') === 'kurir_toko') class="text-[#6B0F1A] focus:ring-[#6B0F1A]">
                                        <span>Diantar Kurir</span>
                                    </label>
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="opsi_pengiriman" value="ambil_sendiri" @checked(old('opsi_pengiriman', $draft['opsi_pengiriman'] ?? '') === 'ambil_sendiri') class="text-[#6B0F1A] focus:ring-[#6B0F1A]">
                                        <span>Ambil Sendiri</span>
                                    </label>
                                </div>
                            </fieldset>

                            @php
                                $bankSiap = $storeSetting->hasBankDetails();
                                $qrisSiap = $storeSetting->hasQrisImage();
                            @endphp

                            <div data-payment-group class="p-4 bg-[#FBF7F2] rounded-xl border border-[#E6D9CC]">
                                <fieldset>
                                    <legend class="text-xs sm:text-sm font-semibold text-[#2C1810] px-1">Metode Pembayaran</legend>
                                    <div class="mt-2 space-y-2.5 text-xs sm:text-sm text-[#2C1810]">
                                        <div>
                                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="metode_pembayaran" value="transfer_bank" @checked($bankSiap && old('metode_pembayaran', $draft['metode_pembayaran'] ?? 'transfer_bank') === 'transfer_bank') @disabled(! $bankSiap) class="text-[#6B0F1A] focus:ring-[#6B0F1A]">
                                                <span>Transfer Bank</span>
                                            </label>
                                            @unless ($bankSiap)
                                                <p class="text-[11px] text-amber-800 ml-6">Transfer Bank belum dikonfigurasi oleh penjual.</p>
                                            @endunless
                                        </div>

                                        <div>
                                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="metode_pembayaran" value="qris" @checked(old('metode_pembayaran', $draft['metode_pembayaran'] ?? '') === 'qris') @disabled(! $qrisSiap) class="text-[#6B0F1A] focus:ring-[#6B0F1A]">
                                                <span>QRIS</span>
                                            </label>
                                            @unless ($qrisSiap)
                                                <p class="text-[11px] text-amber-800 ml-6">QRIS belum dikonfigurasi oleh penjual.</p>
                                            @endunless
                                        </div>

                                        <div>
                                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                                <input type="radio" name="metode_pembayaran" value="cash" @checked(old('metode_pembayaran', $draft['metode_pembayaran'] ?? '') === 'cash') class="text-[#6B0F1A] focus:ring-[#6B0F1A]">
                                                <span>Tunai / Bayar di Toko</span>
                                            </label>
                                        </div>
                                    </div>
                                </fieldset>

                                @if ($bankSiap)
                                    <div data-payment-detail="transfer_bank" hidden class="mt-3 p-3.5 bg-white rounded-xl border border-[#E6D9CC] text-xs sm:text-sm">
                                        <p class="font-semibold text-[#2C1810]">Transfer Bank:</p>
                                        <p class="text-[#5E463B]">{{ $storeSetting->bank_name }} - {{ $storeSetting->bank_account_number }} a.n. {{ $storeSetting->bank_account_holder }}</p>
                                    </div>
                                @endif

                                @if ($qrisSiap)
                                    <div data-payment-detail="qris" hidden class="mt-3 p-3.5 bg-white rounded-xl border border-[#E6D9CC] text-xs sm:text-sm">
                                        <p class="font-semibold text-[#2C1810] mb-2">Scan QRIS berikut untuk membayar:</p>
                                        <img src="{{ asset('storage/'.$storeSetting->qris_image_path) }}" alt="QRIS {{ $storeSetting->store_name }}" class="max-w-[180px] sm:max-w-[220px] rounded-lg border border-[#E6D9CC]">
                                    </div>
                                @endif
                            </div>

                            <div>
                                <label for="catatan" class="block text-xs sm:text-sm font-medium text-[#2C1810] mb-1.5">Catatan Khusus (opsional)</label>
                                <textarea id="catatan" name="catatan" rows="2" maxlength="1000" placeholder="Catatan khusus (misal: tulisan ucapan, lilin angka, dll)" class="w-full bg-[#FBF7F2] border border-[#DDD0C4] rounded-xl px-3.5 py-2.5 text-sm text-[#2C1810] focus:bg-white focus:ring-2 focus:ring-[#6B0F1A]/15 focus:border-[#6B0F1A] outline-none transition">{{ old('catatan', $draft['catatan'] ?? '') }}</textarea>
                            </div>

                            <button type="submit" class="w-full py-3.5 px-6 bg-[#6B0F1A] hover:bg-[#520912] text-white font-semibold text-xs sm:text-sm lg:text-base rounded-full transition flex items-center justify-center gap-2 cursor-pointer">
                                <span>Lanjut ke Ringkasan Pesanan</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </form>
                    </div>

                    {{-- Tambah ke Keranjang (aksi sekunder) --}}
                    <div class="relative pt-7 mt-7 border-t border-[#EFE6DD]">
                        <span class="absolute -top-2.5 left-1/2 -translate-x-1/2 bg-white px-3 text-[10px] font-semibold uppercase tracking-widest text-[#8C6D62]">
                            atau
                        </span>

                        <div class="rounded-2xl bg-[#FBF7F2] border border-[#E6D9CC] p-4 sm:p-5">
                            <div class="mb-3">
                                <h3 class="flex items-center gap-2 text-sm sm:text-base font-serif font-semibold text-[#2C1810]">
                                    <span class="w-6 h-6 rounded-full bg-white border border-[#DDD0C4] text-[#6B0F1A] flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                    </span>
                                    Tambah ke Keranjang
                                </h3>
                                <p class="text-xs text-[#6E564C] mt-1 ml-8">Simpan dulu, lalu pesan bersama kue lainnya nanti.</p>
                            </div>

                            <form id="cart-form" action="{{ route('cart.add', $product) }}" method="POST" class="space-y-3">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label for="cart_quantity" class="block text-xs sm:text-sm font-medium text-[#2C1810] mb-1">Jumlah</label>
                                        <input type="number" id="cart_quantity" name="quantity" value="1" min="1" max="{{ $maxPerOrder }}" data-max-per-order="{{ $maxPerOrder }}" data-max-order-message="Produk ini hanya dapat dipesan maksimal {{ $maxPerOrder }} pcs per transaksi." required class="w-full bg-white border border-[#DDD0C4] rounded-xl px-3.5 py-2 text-sm text-[#2C1810] focus:ring-2 focus:ring-[#6B0F1A]/15 focus:border-[#6B0F1A] outline-none">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="cart_catatan" class="block text-xs sm:text-sm font-medium text-[#2C1810] mb-1">Catatan untuk Kue Ini (opsional)</label>
                                        <input type="text" id="cart_catatan" name="catatan" value="{{ old('catatan') }}" maxlength="1000" placeholder="Catatan khusus (misal: tulisan ucapan, lilin, dll)" class="w-full bg-white border border-[#DDD0C4] rounded-xl px-3.5 py-2 text-sm text-[#2C1810] focus:ring-2 focus:ring-[#6B0F1A]/15 focus:border-[#6B0F1A] outline-none">
                                    </div>
                                </div>
                                <button type="submit" class="w-full py-3 px-6 bg-white hover:bg-[#F5EDE4] text-[#6B0F1A] font-semibold text-xs sm:text-sm rounded-full border border-[#6B0F1A] transition flex items-center justify-center gap-2 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    <span>Tambah ke Keranjang</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="pt-5 border-t border-[#EFE6DD] text-center">
                        <p class="text-xs sm:text-sm text-[#7A1C1C] font-medium">Maaf, produk ini sedang habis dan belum bisa dipesan.</p>
                    </div>
                @endif
            </section>
        @endisset

        {{-- Editorial Catalog Header --}}
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-5 pb-6 border-b border-[#E6D9CC]">
            <div class="space-y-1.5">
                <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-[0.2em] text-[#8C6D62]">
                    Koleksi Pilihan
                </p>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-semibold text-[#2C1810] tracking-tight">
                    Katalog Kue
                </h1>
                <p class="text-xs sm:text-sm lg:text-base text-[#5E463B]">
                    Maniskan harimu dengan pilihan kue istimewa!
                </p>
            </div>

            {{-- Search & Filter Toggle Bar --}}
            <div class="flex items-center gap-2.5 w-full lg:w-auto lg:min-w-[380px]">
                <div class="flex-1 relative bg-white rounded-xl border border-[#DDD0C4] px-3.5 py-2.5 flex items-center gap-2.5 focus-within:ring-2 focus-within:ring-[#6B0F1A]/15 focus-within:border-[#6B0F1A] transition">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#8C6D62] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="7"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 20l-3.5-3.5"/>
                    </svg>
                    <input
                        type="text"
                        id="catalog-search"
                        value="{{ $search ?? '' }}"
                        placeholder="Cari kue favoritmu..."
                        autocomplete="off"
                        class="w-full bg-transparent border-0 p-0 text-sm text-[#2C1810] placeholder-[#A8968C] focus:outline-none focus:ring-0"
                    >
                    <button type="button" id="search-clear-btn" class="hidden text-[#8C6D62] hover:text-[#2C1810] p-0.5 cursor-pointer" aria-label="Hapus pencarian">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Filter Toggle Button (opens Sidebar Drawer) --}}
                <button
                    type="button"
                    id="filter-toggle-btn"
                    title="Buka Filter Katalog"
                    aria-label="Filter Katalog"
                    class="relative px-3.5 py-2.5 sm:px-4 bg-white hover:bg-[#F5EDE4] border border-[#DDD0C4] text-[#2C1810] rounded-xl transition flex items-center justify-center gap-2 cursor-pointer shrink-0"
                >
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#6B0F1A]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M3 12h18M3 20h18M7 2v4M17 10v4M11 18v4" />
                    </svg>
                    <span class="hidden sm:inline font-medium text-xs sm:text-sm">Filter</span>
                    <span id="filter-active-dot" class="hidden absolute top-2 right-2 w-2 h-2 rounded-full bg-[#6B0F1A]"></span>
                </button>
            </div>
        </div>

        {{-- Category Filter Tabs --}}
        <div class="flex items-center gap-2 overflow-x-auto lg:flex-wrap py-4 sm:py-5 scrollbar-none" id="category-pills">
            <button
                type="button"
                data-category-id="all"
                class="category-pill shrink-0 px-4 py-2 rounded-full text-xs sm:text-sm font-medium transition cursor-pointer {{ empty($currentCategory) ? 'bg-[#6B0F1A] text-white' : 'bg-white hover:bg-[#F5EDE4] text-[#5E463B] border border-[#E2D5C8]' }}"
            >
                Semua
            </button>

            @foreach ($categories as $cat)
                <button
                    type="button"
                    data-category-id="{{ $cat->id }}"
                    class="category-pill shrink-0 px-4 py-2 rounded-full text-xs sm:text-sm font-medium transition cursor-pointer {{ ($currentCategory ?? null) === $cat->id ? 'bg-[#6B0F1A] text-white' : 'bg-white hover:bg-[#F5EDE4] text-[#5E463B] border border-[#E2D5C8]' }}"
                >
                    {{ $cat->nama_kategori }}
                </button>
            @endforeach
        </div>

        {{-- Product Cards Grid (Curated Bakery Collection) --}}
        <div id="catalog-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3.5 sm:gap-5 lg:gap-6 mt-2">
            @forelse ($products as $item)
                @php
                    $isReady = $item->status_stok === \App\Enums\StatusStok::Ready;
                    $categoryName = $item->category?->nama_kategori ?? 'Umum';
                    $hasDescription = filled($item->deskripsi);
                @endphp
                <article
                    class="product-card group relative bg-white rounded-2xl border border-[#EAE0D5] hover:border-[#D4B8AE] transition-colors flex flex-col justify-between overflow-hidden {{ $isReady ? 'cursor-pointer' : '' }}"
                    data-product-name="{{ strtolower($item->nama_kue) }}"
                    data-category-id="{{ $item->category_id ?? '' }}"
                    data-status-stok="{{ $isReady ? 'ready' : 'habis' }}"
                    data-harga="{{ (int) $item->harga }}"
                >
                    <div>
                        {{-- Cake Image Container --}}
                        <div class="aspect-square w-full overflow-hidden bg-[#F4ECE3] relative flex items-center justify-center">
                            @if ($item->foto_url)
                                @if ($isReady)
                                    <img src="{{ $item->foto_url }}" alt="{{ $item->nama_kue }}" class="w-full h-full object-cover group-hover:scale-103 transition-transform duration-500">
                                @else
                                    <img src="{{ $item->foto_url }}" alt="{{ $item->nama_kue }}" class="w-full h-full object-cover grayscale opacity-70">
                                @endif
                            @else
                                @if ($isReady)
                                    <div class="w-full h-full p-6 flex items-center justify-center group-hover:scale-105 transition-transform duration-300">
                                        <x-category-icon :category="$item->category" class="w-full h-full max-w-[80px] max-h-[80px] lg:max-w-[96px] lg:max-h-[96px]" />
                                    </div>
                                @else
                                    <div class="w-full h-full p-6 flex items-center justify-center grayscale opacity-60">
                                        <x-category-icon :category="$item->category" class="w-full h-full max-w-[80px] max-h-[80px] lg:max-w-[96px] lg:max-h-[96px]" />
                                    </div>
                                @endif
                            @endif

                            @unless ($isReady)
                                <span class="absolute top-2.5 right-2.5 bg-[#2C1810]/85 text-[#FBF7F2] text-[10px] sm:text-xs font-medium tracking-wide px-2.5 py-0.5 rounded-full pointer-events-none">
                                    Habis
                                </span>
                            @endunless
                        </div>

                        <div class="p-3 sm:p-4 pb-2 space-y-1">
                            {{-- Category Label (cleanly below photo, preserves title attribute) --}}
                            <p class="text-[10px] sm:text-[11px] uppercase tracking-[0.14em] text-[#8C6D62] truncate" title="{{ $categoryName }}">
                                {{ $categoryName }}
                            </p>

                            {{-- Cake Title (Stretched link covering the entire card when ready) --}}
                            @if ($isReady)
                                <a href="{{ route('customer.produk.show', $item) }}"
                                   class="block font-serif font-semibold text-base sm:text-lg lg:text-xl text-[#2C1810] leading-snug line-clamp-1 group-hover:text-[#6B0F1A] transition focus:outline-none after:absolute after:inset-0 after:z-10 focus-visible:after:ring-2 focus-visible:after:ring-[#6B0F1A]/40 focus-visible:after:ring-inset"
                                   title="{{ $item->nama_kue }}">
                                    {{ $item->nama_kue }}
                                </a>
                            @else
                                <span class="block font-serif font-semibold text-base sm:text-lg lg:text-xl text-[#6E564C] leading-snug line-clamp-1" title="{{ $item->nama_kue }}">
                                    {{ $item->nama_kue }}
                                </span>
                            @endif

                            {{-- Short Description --}}
                            <p class="text-xs font-normal line-clamp-1 leading-relaxed {{ $hasDescription ? 'text-[#6E564C]' : 'text-[#A8968C] italic' }}"
                               title="{{ $hasDescription ? $item->deskripsi : 'Tidak ada deskripsi' }}">
                                {{ $hasDescription ? Str::limit($item->deskripsi, 60) : 'Tidak ada deskripsi' }}
                            </p>
                        </div>
                    </div>

                    {{-- Bottom Row: Price & Shopping Cart / Order Button --}}
                    <div class="px-3 sm:px-4 pb-3 sm:pb-4 pt-2 mt-1 border-t border-[#F2EAE2] flex items-center justify-between gap-2">
                        <span class="text-sm sm:text-base font-semibold text-[#6B0F1A]">
                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                        </span>

                        @if ($isReady)
                            <form action="{{ route('cart.add', $item) }}" method="POST" class="relative z-20 inline" onclick="event.stopPropagation()">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button
                                    type="submit"
                                    title="Tambah ke Keranjang"
                                    aria-label="Tambah {{ $item->nama_kue }} ke keranjang"
                                    class="bg-[#6B0F1A] hover:bg-[#520912] active:scale-95 text-white p-2 sm:p-2.5 rounded-xl transition flex items-center justify-center cursor-pointer"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </button>
                            </form>
                        @else
                            <button
                                type="button"
                                disabled
                                class="bg-[#EFE8E0] text-[#9E8B82] text-[11px] sm:text-xs font-medium px-2.5 py-1.5 rounded-lg cursor-not-allowed"
                            >
                                Pesan (Habis)
                            </button>
                        @endif
                    </div>
                </article>
            @empty
                <div class="col-span-2 sm:col-span-3 lg:col-span-4 text-center py-12 bg-white rounded-2xl border border-[#EAE0D5] p-6">
                    <p class="text-sm sm:text-base text-[#5E463B] font-medium">Belum ada produk yang tersedia.</p>
                </div>
            @endforelse
        </div>

        {{-- No results message when filtering via search, category, or sidebar --}}
        <div id="no-search-results" class="hidden text-center py-14 bg-white rounded-2xl border border-[#EAE0D5] p-6 mt-4">
            <svg class="w-12 h-12 mx-auto text-[#D4B8AE] mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" stroke-linejoin="round" d="M20 20l-3.5-3.5"/></svg>
            <p class="text-base sm:text-lg font-serif font-semibold text-[#2C1810]">Kue favoritmu tidak ditemukan</p>
            <p class="text-xs sm:text-sm text-[#8C6D62] mt-1">Coba sesuaikan kata kunci atau atur ulang filter katalog.</p>
        </div>
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

    {{-- Filter Sidebar Drawer & Backdrop --}}
    <div
        id="filter-backdrop"
        class="fixed inset-0 bg-[#2C1810]/40 z-50 transition-opacity duration-300 opacity-0 pointer-events-none"
    ></div>

    <aside
        id="filter-drawer"
        class="fixed top-0 right-0 bottom-0 w-80 sm:w-96 max-w-[85vw] bg-[#FBF7F2] z-50 border-l border-[#E6D9CC] flex flex-col justify-between transform transition-transform duration-300 ease-in-out translate-x-full"
        aria-label="Filter Katalog"
    >
        {{-- Drawer Top & Content --}}
        <div>
            {{-- Header --}}
            <div class="flex items-center justify-between p-5 pb-4 border-b border-[#E6D9CC] bg-white">
                <div class="flex items-center gap-2.5 text-[#2C1810]">
                    <svg class="w-5 h-5 text-[#6B0F1A] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M3 12h18M3 20h18M7 2v4M17 10v4M11 18v4" />
                    </svg>
                    <h2 class="text-xl font-serif font-semibold">Filter Katalog</h2>
                </div>
                <button
                    type="button"
                    id="filter-close-btn"
                    class="text-[#8C6D62] hover:text-[#2C1810] p-1.5 rounded-lg transition cursor-pointer"
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
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8C6D62] mb-3">
                        Ketersediaan
                    </p>

                    <button
                        type="button"
                        id="toggle-ready-only"
                        data-active="{{ $readyOnly ? 'true' : 'false' }}"
                        class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs sm:text-sm font-medium flex items-center gap-2.5 transition cursor-pointer select-none {{ $readyOnly ? 'bg-[#6B0F1A] text-white' : 'bg-white text-[#2C1810] border border-[#DDD0C4] hover:border-[#6B0F1A]/40' }}"
                    >
                        <span id="ready-checkbox-box" class="w-4 h-4 rounded flex items-center justify-center shrink-0 {{ $readyOnly ? 'bg-white text-[#6B0F1A]' : 'border border-[#A8968C] bg-white' }}">
                            <svg id="ready-check-svg" class="w-3 h-3 {{ $readyOnly ? '' : 'hidden' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        <span>Tampilkan kue yang ready di toko</span>
                    </button>
                </div>

                <hr class="border-[#E6D9CC]">

                {{-- Section 2: Rentang Harga --}}
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8C6D62] mb-3">
                        Rentang Harga
                    </p>

                    <div class="space-y-3 text-sm" id="price-radio-group">
                        @php
                            $activePrice = $priceRange ?? 'all';
                        @endphp
                        <label class="flex items-center gap-3 cursor-pointer select-none text-[#2C1810] font-medium">
                            <input type="radio" name="filter_price_range" value="all" @checked($activePrice === 'all') class="w-4 h-4 text-[#6B0F1A] border-[#A8968C] focus:ring-[#6B0F1A]">
                            <span>Semua harga</span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer select-none text-[#2C1810] font-medium">
                            <input type="radio" name="filter_price_range" value="under_25k" @checked($activePrice === 'under_25k') class="w-4 h-4 text-[#6B0F1A] border-[#A8968C] focus:ring-[#6B0F1A]">
                            <span>Di bawah Rp25.000</span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer select-none text-[#2C1810] font-medium">
                            <input type="radio" name="filter_price_range" value="25k_50k" @checked($activePrice === '25k_50k') class="w-4 h-4 text-[#6B0F1A] border-[#A8968C] focus:ring-[#6B0F1A]">
                            <span>Rp25.000 - Rp50.000</span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer select-none text-[#2C1810] font-medium">
                            <input type="radio" name="filter_price_range" value="above_50k" @checked($activePrice === 'above_50k') class="w-4 h-4 text-[#6B0F1A] border-[#A8968C] focus:ring-[#6B0F1A]">
                            <span>Di atas Rp50.000</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Drawer Footer: Reset & Terapkan buttons --}}
        <div class="p-5 border-t border-[#E6D9CC] flex items-center gap-3 bg-white">
            <button
                type="button"
                id="filter-reset-btn"
                class="flex-1 py-2.5 px-4 bg-white border border-[#6B0F1A] text-[#6B0F1A] font-semibold text-sm rounded-full hover:bg-[#F5EDE4] transition cursor-pointer text-center"
            >
                Reset
            </button>
            <button
                type="button"
                id="filter-apply-btn"
                class="flex-1 py-2.5 px-4 bg-[#6B0F1A] hover:bg-[#520912] text-white font-semibold text-sm rounded-full transition cursor-pointer text-center"
            >
                Terapkan
            </button>
        </div>
    </aside>

    {{-- Interactive Client-side script for Instant Filtering & Sidebar Drawer --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('catalog-search');
            const clearBtn = document.getElementById('search-clear-btn');
            const pills = document.querySelectorAll('.category-pill');
            const cards = document.querySelectorAll('.product-card');
            const noResults = document.getElementById('no-search-results');

            const toggleDrawerBtn = document.getElementById('filter-toggle-btn');
            const closeDrawerBtn = document.getElementById('filter-close-btn');
            const drawer = document.getElementById('filter-drawer');
            const backdrop = document.getElementById('filter-backdrop');
            const activeDot = document.getElementById('filter-active-dot');

            const toggleReadyBtn = document.getElementById('toggle-ready-only');
            const readyCheckboxBox = document.getElementById('ready-checkbox-box');
            const readyCheckSvg = document.getElementById('ready-check-svg');
            const priceRadios = document.querySelectorAll('input[name="filter_price_range"]');
            const resetBtn = document.getElementById('filter-reset-btn');
            const applyBtn = document.getElementById('filter-apply-btn');

            let activeCategory = 'all';
            let isReadyOnly = toggleReadyBtn ? toggleReadyBtn.getAttribute('data-active') === 'true' : false;
            let activePriceRange = 'all';

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

            function syncReadyButtonVisual(active) {
                if (!toggleReadyBtn) return;
                toggleReadyBtn.setAttribute('data-active', active ? 'true' : 'false');
                if (active) {
                    toggleReadyBtn.classList.remove('bg-white', 'text-[#2C1810]', 'border', 'border-[#DDD0C4]', 'hover:border-[#6B0F1A]/40');
                    toggleReadyBtn.classList.add('bg-[#6B0F1A]', 'text-white');
                    readyCheckboxBox.classList.remove('border', 'border-[#A8968C]', 'bg-white');
                    readyCheckboxBox.classList.add('bg-white', 'text-[#6B0F1A]');
                    readyCheckSvg.classList.remove('hidden');
                } else {
                    toggleReadyBtn.classList.remove('bg-[#6B0F1A]', 'text-white');
                    toggleReadyBtn.classList.add('bg-white', 'text-[#2C1810]', 'border', 'border-[#DDD0C4]', 'hover:border-[#6B0F1A]/40');
                    readyCheckboxBox.classList.remove('bg-white', 'text-[#6B0F1A]');
                    readyCheckboxBox.classList.add('border', 'border-[#A8968C]', 'bg-white');
                    readyCheckSvg.classList.add('hidden');
                }
            }

            if (toggleReadyBtn) {
                toggleReadyBtn.addEventListener('click', function () {
                    isReadyOnly = !isReadyOnly;
                    syncReadyButtonVisual(isReadyOnly);
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

                    const matchesQuery = query === '' || name.includes(query);
                    const matchesCategory = activeCategory === 'all' || categoryId === activeCategory;
                    const matchesReady = !isReadyOnly || statusStok === 'ready';

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
                        p.classList.remove('bg-[#6B0F1A]', 'text-white');
                        p.classList.add('bg-white', 'hover:bg-[#F5EDE4]', 'text-[#5E463B]', 'border', 'border-[#E2D5C8]');
                    });

                    this.classList.remove('bg-white', 'hover:bg-[#F5EDE4]', 'text-[#5E463B]', 'border', 'border-[#E2D5C8]');
                    this.classList.add('bg-[#6B0F1A]', 'text-white');

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
                    isReadyOnly = false;
                    syncReadyButtonVisual(false);

                    activePriceRange = 'all';
                    priceRadios.forEach(r => {
                        r.checked = r.value === 'all';
                    });

                    filterProducts();
                    closeDrawer();
                });
            }

            filterProducts();
        });
    </script>
</body>
</html>
