<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Pesanan - {{ \App\Models\StoreSetting::current()?->store_name ?? 'Nak Mbarep' }}</title>
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

    @php($metodePembayaran = \App\Enums\MetodePembayaran::from($draft['metode_pembayaran']))

    {{-- Main Container Card --}}
    <main class="flex-1 px-4">
        <div class="bg-white rounded-3xl shadow-lg p-6 md:p-8 max-w-xl mx-auto my-8">

            {{-- Header --}}
            <div class="mb-6">
                <a href="{{ route('customer.produk.show', $product) }}"
                   class="inline-flex items-center gap-1.5 text-xs text-[#6B0F1A] hover:text-[#540F18] font-semibold transition mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Produk</span>
                </a>
                <h1 class="text-2xl sm:text-3xl font-serif font-bold text-[#6B0F1A] tracking-tight">Ringkasan Pesanan</h1>
                <p class="text-xs text-stone-500 mt-1">Periksa kembali rincian pesanan kamu sebelum dikirim ke penjual.</p>
            </div>

            {{-- Flash / validation errors --}}
            @if (session('error'))
                <div class="mb-5 p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        @continue(session('error') === $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- ============================================================ --}}
            {{-- Section 1: Informasi Pembeli --}}
            {{-- ============================================================ --}}
            <section class="space-y-3">
                <h2 class="flex items-center gap-2 text-sm font-bold text-gray-800">
                    <span class="w-6 h-6 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center text-xs shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    Informasi Pembeli
                </h2>

                <div class="rounded-2xl border border-stone-200/80 overflow-hidden bg-stone-50/30 divide-y divide-stone-100 text-xs sm:text-sm">
                    {{-- Nama Pemesan --}}
                    <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-white">
                        <span class="text-stone-500 font-medium">Nama Pemesan</span>
                        <span class="font-semibold text-stone-800 text-left sm:text-right">{{ $draft['nama_pemesan'] }}</span>
                    </div>

                    {{-- No HP / WA --}}
                    <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-stone-50/40">
                        <span class="text-stone-500 font-medium">No HP / WA</span>
                        <span class="font-semibold text-stone-800 text-left sm:text-right">{{ $draft['nomor_wa'] }}</span>
                    </div>

                    {{-- Alamat Lengkap --}}
                    <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1 bg-white">
                        <span class="text-stone-500 font-medium shrink-0">Alamat Lengkap</span>
                        <span class="font-semibold text-stone-800 text-left sm:text-right max-w-[16rem] sm:max-w-xs break-words">{{ $draft['alamat_lengkap'] }}</span>
                    </div>

                    {{-- Opsi Pengiriman --}}
                    <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 bg-stone-50/40">
                        <span class="text-stone-500 font-medium">Opsi Pengiriman</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-stone-100 text-stone-700">
                            {{ $draft['opsi_pengiriman'] === 'kurir_toko' ? 'Diantar Kurir' : 'Ambil Sendiri' }}
                        </span>
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div class="p-3.5 bg-white">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1">
                            <span class="text-stone-500 font-medium">Metode Pembayaran</span>
                            <span class="font-bold text-[#6B0F1A] text-left sm:text-right">{{ $metodePembayaran->label() }}</span>
                        </div>

                        @if ($metodePembayaran === \App\Enums\MetodePembayaran::TransferBank && $storeSetting->hasBankDetails())
                            <div class="mt-2 sm:mt-1.5 sm:text-right text-[11px] leading-relaxed text-stone-500">
                                <p>
                                    Transfer ke
                                    <span class="font-semibold text-stone-700">{{ $storeSetting->bank_name }} - {{ $storeSetting->bank_account_number }}</span>
                                </p>
                                <p>a.n. {{ $storeSetting->bank_account_holder }}</p>
                            </div>
                        @elseif ($metodePembayaran === \App\Enums\MetodePembayaran::Qris && $storeSetting->hasQrisImage())
                            <div class="mt-3 flex sm:justify-end">
                                <img src="{{ asset('storage/'.$storeSetting->qris_image_path) }}"
                                     alt="QRIS {{ $storeSetting->store_name }}"
                                     class="w-40 rounded-xl border border-stone-200 shadow-2xs">
                            </div>
                        @endif
                    </div>

                    {{-- Catatan --}}
                    @if (! empty($draft['catatan']))
                        <div class="p-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1 bg-stone-50/40">
                            <span class="text-stone-500 font-medium shrink-0">Catatan</span>
                            <span class="font-semibold text-stone-800 text-left sm:text-right max-w-[16rem] sm:max-w-xs whitespace-pre-line">{{ $draft['catatan'] }}</span>
                        </div>
                    @endif
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 2: Detail Produk --}}
            {{-- ============================================================ --}}
            <section class="space-y-3 mt-6">
                <h2 class="flex items-center gap-2 text-sm font-bold text-gray-800">
                    <span class="w-6 h-6 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center text-xs shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </span>
                    Detail Produk
                </h2>

                <div class="rounded-2xl border border-stone-200/80 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[420px] text-left text-xs sm:text-sm">
                            <thead class="bg-stone-50/80 text-[10px] sm:text-[11px] uppercase tracking-wider text-stone-500">
                                <tr>
                                    <th scope="col" class="px-3 sm:px-4 py-2.5 font-semibold">Nama Kue</th>
                                    <th scope="col" class="px-3 sm:px-4 py-2.5 font-semibold text-right whitespace-nowrap">Harga Satuan</th>
                                    <th scope="col" class="px-3 sm:px-4 py-2.5 font-semibold text-center">Kuantitas</th>
                                    <th scope="col" class="px-3 sm:px-4 py-2.5 font-semibold text-right whitespace-nowrap">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100">
                                <tr class="align-middle">
                                    <td class="px-3 sm:px-4 py-3.5 font-semibold text-stone-800">{{ $product->nama_kue }}</td>
                                    <td class="px-3 sm:px-4 py-3.5 text-right whitespace-nowrap tabular-nums text-stone-600">
                                        Rp{{ number_format($product->harga, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 sm:px-4 py-3.5 text-center tabular-nums text-stone-600">{{ $draft['jumlah'] }}</td>
                                    <td class="px-3 sm:px-4 py-3.5 text-right whitespace-nowrap tabular-nums font-bold text-[#6B0F1A]">
                                        Rp{{ number_format($subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-stone-50/60 border-t border-stone-200">
                                <tr>
                                    <td colspan="3" class="px-3 sm:px-4 py-3 text-right text-[10px] sm:text-[11px] font-semibold uppercase tracking-wider text-stone-500">
                                        Total Harga
                                    </td>
                                    <td class="px-3 sm:px-4 py-3 text-right whitespace-nowrap tabular-nums text-sm sm:text-base font-extrabold text-[#6B0F1A]">
                                        Rp{{ number_format($subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </section>

            {{-- ============================================================ --}}
            {{-- Section 3: Instruksi Pembayaran --}}
            {{-- ============================================================ --}}
            @if ($metodePembayaran === \App\Enums\MetodePembayaran::Cash)
                <div class="mt-6 rounded-2xl bg-amber-50 border border-amber-200 p-4">
                    <p class="text-xs text-amber-700 italic">Pembayaran dilakukan secara tunai (Cash) saat pengambilan / pengantaran pesanan.</p>
                </div>
            @else
                <div class="mt-6 rounded-2xl bg-blue-50/70 border border-blue-200 p-4">
                    <p class="text-xs text-blue-800 font-medium">Silakan lakukan pembayaran sesuai nominal di atas, lalu unggah bukti transfer di bawah ini.</p>
                </div>
            @endif

            {{-- ============================================================ --}}
            {{-- Section 4: Form Konfirmasi --}}
            {{-- ============================================================ --}}
            <form action="{{ route('customer.pesanan.checkout') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-4">
                @csrf

                @unless ($metodePembayaran === \App\Enums\MetodePembayaran::Cash)
                    <div class="space-y-1.5">
                        <label for="bukti_pembayaran" class="block text-xs font-semibold text-stone-700">Unggah Bukti Pembayaran (JPG/PNG)</label>
                        <input type="file"
                               id="bukti_pembayaran"
                               name="bukti_pembayaran"
                               accept="image/*"
                               required
                               class="w-full text-xs text-stone-500 border border-stone-200 rounded-2xl p-2 file:mr-2 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#FAF2EE] file:text-[#6B0F1A] hover:file:bg-[#F4E1D9] transition">
                        <p class="text-[11px] text-stone-400">Wajib untuk Transfer Bank dan E-Wallet/QRIS. Maks. 2MB.</p>
                    </div>
                @endunless

                <button type="submit"
                        class="w-full py-3.5 px-6 bg-[#6B0F1A] hover:bg-[#540F18] active:bg-[#3A0A0F] text-white font-bold text-sm sm:text-base rounded-full shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-[#6B0F1A]/30 text-center cursor-pointer">
                    Konfirmasi &amp; Kirim Pesanan
                </button>
            </form>

            {{-- Secondary Action --}}
            <a href="{{ route('customer.produk.show', $product) }}"
               class="mt-3 block w-full py-3 px-6 bg-white hover:bg-stone-50 text-stone-700 font-semibold text-xs sm:text-sm rounded-full border border-stone-200 shadow-2xs hover:shadow-xs transition text-center">
                Kembali / Edit Pesanan
            </a>

            <p class="mt-4 text-[11px] text-stone-400 text-center leading-relaxed">
                Pastikan data pengiriman dan pesanan sudah benar sebelum mengirim.
            </p>
        </div>
    </main>
</body>
</html>