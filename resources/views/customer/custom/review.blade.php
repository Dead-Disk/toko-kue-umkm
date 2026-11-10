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
    <main class="flex-1 flex items-start justify-center px-4 py-8 sm:py-10">
        <div class="w-full max-w-lg bg-white rounded-3xl shadow-xl border border-stone-200/70 p-6 sm:p-8 space-y-6">

            {{-- Header & Link Kembali --}}
            <div>
                <a href="{{ route('customer.custom.create') }}" class="inline-flex items-center gap-1.5 text-xs text-[#6B0F1A] hover:text-[#540F18] font-semibold transition mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali / Edit Pesanan</span>
                </a>
                <h1 class="text-2xl sm:text-3xl font-serif font-bold text-[#6B0F1A] tracking-tight">Review Pesanan Custom</h1>
                <p class="text-xs text-stone-500 mt-1">Periksa kembali rincian pesanan kamu sebelum dikirim ke penjual.</p>
            </div>

            {{-- Rincian Data Pesanan (List Card) --}}
            <div class="rounded-2xl border border-stone-200/80 overflow-hidden bg-stone-50/30 divide-y divide-stone-100 text-xs sm:text-sm">
                {{-- Nama Pemesan --}}
                <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-white">
                    <span class="text-stone-500 font-medium">Nama Pemesan</span>
                    <span class="font-semibold text-stone-800 text-left sm:text-right">{{ $draft['nama_pemesan'] }}</span>
                </div>

                {{-- No WA --}}
                <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-stone-50/40">
                    <span class="text-stone-500 font-medium">No WA</span>
                    <span class="font-semibold text-stone-800 text-left sm:text-right">{{ $draft['nomor_wa'] }}</span>
                </div>

                {{-- Alamat Lengkap --}}
                <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1 bg-white">
                    <span class="text-stone-500 font-medium shrink-0">Alamat Lengkap</span>
                    <span class="font-semibold text-stone-800 text-left sm:text-right max-w-xs">{{ $draft['alamat_lengkap'] }}</span>
                </div>

                {{-- Opsi Pengiriman --}}
                <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-stone-50/40">
                    <span class="text-stone-500 font-medium">Opsi Pengiriman</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-stone-100 text-stone-700">
                        {{ $draft['opsi_pengiriman'] === 'kurir_toko' ? 'Kurir Toko' : 'Ambil Sendiri' }}
                    </span>
                </div>

                {{-- Deskripsi Request --}}
                <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1 bg-white">
                    <span class="text-stone-500 font-medium shrink-0">Deskripsi Request</span>
                    <span class="font-semibold text-stone-800 text-left sm:text-right max-w-xs whitespace-pre-line">{{ $draft['deskripsi_custom'] }}</span>
                </div>

                {{-- Ukuran Kue --}}
                <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-stone-50/40">
                    <span class="text-stone-500 font-medium">Ukuran Kue</span>
                    <span class="font-semibold text-stone-800 text-left sm:text-right">{{ $cakeOption->label() }}</span>
                </div>

                {{-- Produk Acuan (opsional) --}}
                @if ($baseProduct)
                    <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-white">
                        <span class="text-stone-500 font-medium">Produk Acuan:</span>
                        <span class="font-semibold text-[#6B0F1A] text-left sm:text-right">{{ $baseProduct->nama_kue }}</span>
                    </div>
                @endif

                {{-- Harga Dasar / Acuan --}}
                <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-stone-50/40">
                    <div class="flex flex-col">
                        <span class="text-stone-500 font-medium">Harga Dasar / Acuan</span>
                        <span class="text-[11px] text-stone-400">({{ $baseProduct ? 'Harga Produk Acuan' : 'Harga Ukuran Kue' }})</span>
                    </div>
                    <span class="font-semibold text-stone-800 text-left sm:text-right">
                        Rp {{ number_format($hargaAcuan, 0, ',', '.') }}
                    </span>
                </div>

                {{-- Jumlah Beli --}}
                <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-white">
                    <span class="text-stone-500 font-medium">Jumlah Beli (Pcs / Biji)</span>
                    <span class="font-semibold text-stone-800 text-left sm:text-right">{{ $draft['jumlah'] }}</span>
                </div>

                {{-- Foto Referensi --}}
                <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-stone-50/40">
                    <span class="text-stone-500 font-medium">Foto Referensi</span>
                    <div class="text-left sm:text-right">
                        @if(session('temp_foto'))
                            <span class="text-emerald-700 font-medium inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Foto referensi sudah diunggah.
                            </span>
                        @else
                            <span class="text-stone-400 italic"><span>Tidak ada foto referensi</span></span>
                        @endif
                    </div>
                </div>

                {{-- Metode Pembayaran --}}
                <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-white">
                    <span class="text-stone-500 font-medium">Metode Pembayaran</span>
                    <span class="font-bold text-[#6B0F1A] text-left sm:text-right">
                        {{ \App\Enums\MetodePembayaran::from($draft['metode_pembayaran'])->label() }}
                    </span>
                </div>
            </div>

            {{-- Estimasi Total Box --}}
            <div class="rounded-2xl bg-[#FAF2EE]/90 border border-[#DE9DA7]/60 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                <div>
                    <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider block">Estimasi Total Harga</span>
                    <span class="text-[11px] text-stone-400">(Harga Acuan &times; Jumlah)</span>
                </div>
                <strong class="text-xl sm:text-2xl font-bold text-[#6B0F1A]">
                    Rp {{ number_format($estimasiHarga, 0, ',', '.') }}
                </strong>
            </div>

            {{-- Catatan Info --}}
            <p class="text-xs text-stone-500 italic text-center">
                Estimasi harga dapat berbeda setelah penjual mengonfirmasi detail desain.
            </p>

            {{-- Action Buttons --}}
            <div class="space-y-3 pt-2">
                <form action="{{ route('customer.custom.store') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full py-3.5 px-6 bg-[#6B0F1A] hover:bg-[#540F18] active:bg-[#3A0A0F] text-white font-bold text-sm sm:text-base rounded-full shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-[#6B0F1A]/30 text-center cursor-pointer">
                        Konfirmasi &amp; Pesan Sekarang
                    </button>
                </form>

                <a href="{{ route('customer.custom.create') }}"
                   class="block w-full py-3 px-6 bg-white hover:bg-stone-50 text-stone-700 font-semibold text-xs sm:text-sm rounded-full border border-stone-200 shadow-2xs hover:shadow-xs transition text-center">
                    Kembali / Edit Pesanan
                </a>
            </div>
        </div>
    </main>
</body>
</html>
