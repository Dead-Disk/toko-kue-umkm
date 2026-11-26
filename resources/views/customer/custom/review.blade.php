<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Pesanan Custom - {{ $storeSetting->store_name ?? 'Nak Mbarep' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-nav.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Instrument Sans', system-ui, sans-serif;
        }
    </style>
</head>
<body class="bg-[#FAF2EE] text-gray-800 min-h-screen antialiased flex flex-col font-sans">
    {{-- Top Navigation Bar --}}
    @include('customer.partials.navbar')

    {{-- Main Container Card --}}
    <main class="flex-1 flex items-start justify-center px-3 py-6 sm:px-6 sm:py-8 lg:py-10">
        <div class="w-full max-w-lg md:max-w-2xl lg:max-w-5xl xl:max-w-6xl mx-auto bg-white rounded-2xl sm:rounded-3xl shadow-xl border border-stone-200/70 p-4 sm:p-6 lg:p-8 space-y-6">

            {{-- Header & Link Kembali --}}
            <div class="border-b border-stone-100 pb-4 sm:pb-5">
                <a href="{{ route('customer.custom.create') }}" class="inline-flex items-center gap-1.5 text-xs text-[#6B0F1A] hover:text-[#540F18] font-semibold transition mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali / Edit Pesanan</span>
                </a>
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-serif font-bold text-[#6B0F1A] tracking-tight">Review Pesanan Custom</h1>
                        <p class="text-xs sm:text-sm text-stone-500 mt-1">Periksa kembali rincian pesanan kamu sebelum dikirim ke penjual.</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 w-fit">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                        Tahap Terakhir / Review
                    </span>
                </div>
            </div>

            {{-- Two-Column Grid on Desktop, Single-Column on Mobile --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

                {{-- ==================== KOLOM KIRI (lg:col-span-7): Data Pemesan & Spesifikasi Kue ==================== --}}
                <div class="lg:col-span-7 space-y-6 min-w-0">

                    {{-- Section 1: Informasi Pemesan --}}
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 pb-1.5 border-b border-stone-100">
                            <span class="w-6 h-6 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-2xs">1</span>
                            <h2 class="text-sm font-bold text-stone-800">Data Pemesan</h2>
                        </div>

                        <div class="rounded-2xl border border-stone-200/80 overflow-hidden bg-stone-50/30 divide-y divide-stone-100 text-xs sm:text-sm shadow-2xs">
                            {{-- Nama Pemesan --}}
                            <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-white">
                                <span class="text-stone-500 font-medium shrink-0">Nama Pemesan</span>
                                <span class="font-semibold text-stone-800 text-left sm:text-right break-words min-w-0">{{ $draft['nama_pemesan'] }}</span>
                            </div>

                            {{-- No WA --}}
                            <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-stone-50/40">
                                <span class="text-stone-500 font-medium shrink-0">No WA</span>
                                <span class="font-semibold text-stone-800 text-left sm:text-right break-words min-w-0">{{ $draft['nomor_wa'] }}</span>
                            </div>

                            {{-- Alamat Lengkap --}}
                            <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1.5 sm:gap-4 bg-white">
                                <span class="text-stone-500 font-medium shrink-0">Alamat Lengkap</span>
                                <span class="font-semibold text-stone-800 text-left sm:text-right break-words min-w-0 flex-1 whitespace-pre-line">{{ $draft['alamat_lengkap'] }}</span>
                            </div>

                            {{-- Opsi Pengiriman --}}
                            <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-stone-50/40">
                                <span class="text-stone-500 font-medium shrink-0">Opsi Pengiriman</span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-stone-100 text-stone-700 w-fit">
                                    {{ $draft['opsi_pengiriman'] === 'kurir_toko' ? 'Kurir Toko' : 'Ambil Sendiri' }}
                                </span>
                            </div>

                            {{-- Tanggal Ambil --}}
                            @if (!empty($draft['tanggal_ambil']))
                                <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-white">
                                    <span class="text-stone-500 font-medium shrink-0">Tanggal Ambil</span>
                                    <span class="font-semibold text-[#6B0F1A] text-left sm:text-right">
                                        {{ \Carbon\Carbon::parse($draft['tanggal_ambil'])->translatedFormat('d F Y') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Section 2: Spesifikasi & Desain Kue --}}
                    <div class="space-y-3 pt-1">
                        <div class="flex items-center gap-2 pb-1.5 border-b border-stone-100">
                            <span class="w-6 h-6 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-2xs">2</span>
                            <h2 class="text-sm font-bold text-stone-800">Spesifikasi &amp; Desain Kue</h2>
                        </div>

                        <div class="rounded-2xl border border-stone-200/80 overflow-hidden bg-stone-50/30 divide-y divide-stone-100 text-xs sm:text-sm shadow-2xs">
                            {{-- Ukuran Kue --}}
                            <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-white">
                                <span class="text-stone-500 font-medium shrink-0">Ukuran Kue</span>
                                <span class="font-semibold text-stone-800 text-left sm:text-right">{{ $cakeOption->label() }}</span>
                            </div>

                            {{-- Produk Acuan (opsional) --}}
                            @if ($baseProduct)
                                <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-stone-50/40">
                                    <span class="text-stone-500 font-medium shrink-0">Produk Acuan:</span>
                                    <span class="font-semibold text-[#6B0F1A] text-left sm:text-right">{{ $baseProduct->nama_kue }}</span>
                                </div>
                            @endif

                            {{-- Harga Dasar / Acuan --}}
                            <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-white">
                                <div class="flex flex-col">
                                    <span class="text-stone-500 font-medium">Harga Dasar / Acuan</span>
                                    <span class="text-[11px] text-stone-400">({{ $baseProduct ? 'Harga Produk Acuan' : 'Harga Ukuran Kue' }})</span>
                                </div>
                                <span class="font-semibold text-stone-800 text-left sm:text-right">
                                    Rp {{ number_format($hargaAcuan, 0, ',', '.') }}
                                </span>
                            </div>

                            {{-- Jumlah Beli --}}
                            <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-stone-50/40">
                                <span class="text-stone-500 font-medium shrink-0">Jumlah Beli (Pcs / Biji)</span>
                                <span class="font-semibold text-stone-800 text-left sm:text-right">{{ $draft['jumlah'] }}</span>
                            </div>

                            {{-- Deskripsi Request --}}
                            <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1.5 sm:gap-4 bg-white">
                                <span class="text-stone-500 font-medium shrink-0">Deskripsi Request</span>
                                <span class="font-semibold text-stone-800 text-left sm:text-right break-words min-w-0 flex-1 whitespace-pre-line">{{ $draft['deskripsi_custom'] }}</span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ==================== KOLOM KANAN (lg:col-span-5): Foto, Pembayaran, Estimasi Total & Aksi ==================== --}}
                <div class="lg:col-span-5 space-y-6 min-w-0 lg:sticky lg:top-24">

                    {{-- Section 3: Foto Referensi & Metode Pembayaran --}}
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 pb-1.5 border-b border-stone-100">
                            <span class="w-6 h-6 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-2xs">3</span>
                            <h2 class="text-sm font-bold text-stone-800">Foto Referensi &amp; Pembayaran</h2>
                        </div>

                        <div class="rounded-2xl border border-stone-200/80 overflow-hidden bg-stone-50/30 divide-y divide-stone-100 text-xs sm:text-sm shadow-2xs">
                            {{-- Foto Referensi --}}
                            <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1.5 bg-white">
                                <span class="text-stone-500 font-medium shrink-0">Foto Referensi</span>
                                <div class="text-left sm:text-right">
                                    @if(session('temp_foto'))
                                        <span class="text-emerald-700 font-medium inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Foto referensi sudah diunggah.
                                        </span>
                                    @else
                                        <span class="text-stone-400 italic"><span>Tidak ada foto referensi</span></span>
                                    @endif
                                </div>
                            </div>

                            {{-- Metode Pembayaran --}}
                            <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-stone-50/40">
                                <span class="text-stone-500 font-medium shrink-0">Metode Pembayaran</span>
                                <span class="font-bold text-[#6B0F1A] text-left sm:text-right">
                                    {{ \App\Enums\MetodePembayaran::from($draft['metode_pembayaran'])->label() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Section 4: Estimasi Total Box --}}
                    <div class="rounded-2xl bg-gradient-to-br from-[#FAF2EE] to-rose-50/70 border border-[#DE9DA7]/60 p-4 sm:p-5 space-y-2.5 shadow-2xs">
                        <div class="flex items-center justify-between text-xs text-stone-600">
                            <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Estimasi Total Harga</span>
                            <span class="text-[11px] text-stone-400 font-medium">(Harga Acuan &times; Jumlah)</span>
                        </div>
                        <div class="flex items-baseline justify-between gap-2">
                            <strong class="text-2xl sm:text-3xl font-bold text-[#6B0F1A] tracking-tight">
                                Rp {{ number_format($estimasiHarga, 0, ',', '.') }}
                            </strong>
                        </div>
                        <p class="text-[10px] sm:text-[11px] text-stone-500 italic pt-1.5 border-t border-[#DE9DA7]/30">
                            * Estimasi harga dapat berbeda setelah penjual mengonfirmasi detail desain.
                        </p>
                    </div>

                    {{-- Section 5: Action Buttons --}}
                    <div class="space-y-3 pt-1">
                        <form action="{{ route('customer.custom.store') }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full py-3.5 px-6 bg-[#6B0F1A] hover:bg-[#540F18] active:bg-[#3A0A0F] text-white font-bold text-sm sm:text-base rounded-full shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-[#6B0F1A]/30 text-center cursor-pointer flex items-center justify-center gap-2">
                                <span>Konfirmasi &amp; Pesan Sekarang</span>
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </form>

                        <a href="{{ route('customer.custom.create') }}"
                           class="block w-full py-3 px-6 bg-white hover:bg-stone-50 text-stone-700 font-semibold text-xs sm:text-sm rounded-full border border-stone-200 shadow-2xs hover:shadow-xs transition text-center">
                            Kembali / Edit Pesanan
                        </a>

                        {{-- Tombol Chat Penjual bila ingin konsultasi custom cake --}}
                        <div class="pt-2 text-center">
                            <a href="{{ route('customer.chat.index', ['phone' => $draft['nomor_wa'] ?? null, 'name' => $draft['nama_pemesan'] ?? null]) }}"
                               class="inline-flex items-center gap-1.5 text-xs text-[#6B0F1A] hover:underline font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <span>Butuh konsultasi desain? Chat dengan Kami</span>
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </main>
</body>
</html>
