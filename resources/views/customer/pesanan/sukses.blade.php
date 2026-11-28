<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - {{ \App\Models\StoreSetting::current()?->store_name ?? 'Nak Mbarep' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#3A0A0F] text-gray-800 min-h-screen antialiased flex flex-col font-sans">

    {{-- Top Navigation Bar --}}
    @include('customer.partials.navbar')

    {{-- Outer dark maroon background + white card container --}}
    <main class="flex-1 flex items-start justify-center px-3 py-6 sm:px-6 sm:py-8 lg:py-10">
        <div class="w-full max-w-lg md:max-w-2xl lg:max-w-5xl xl:max-w-6xl mx-auto bg-white rounded-2xl sm:rounded-3xl shadow-2xl overflow-hidden p-4 sm:p-6 lg:p-8 space-y-6 sm:space-y-8">

            {{-- ============================================================ --}}
            {{-- Success Banner (Full Width Header) --}}
            {{-- ============================================================ --}}
            <div class="text-center space-y-2.5 pb-4 sm:pb-6 border-b border-stone-100">
                {{-- Check circle icon --}}
                <div class="mx-auto w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-emerald-100 flex items-center justify-center shadow-xs">
                    <svg class="w-8 h-8 sm:w-9 sm:h-9 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <div class="space-y-1">
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-serif font-bold text-[#6B0F1A] tracking-tight">Pesanan Berhasil Dikirim</h1>
                    <p class="text-xs sm:text-sm text-gray-500 max-w-xl mx-auto leading-relaxed">
                        Terima kasih, <span class="font-semibold text-gray-800">{{ $order->nama_pemesan }}</span>!
                        Pesanan kamu sudah kami terima dan saat ini
                        <strong class="text-[#6B0F1A]">sedang menunggu konfirmasi penjual</strong>.
                    </p>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- Content Grid: 2 Kolom di Desktop, 1 Kolom di Mobile --}}
            {{-- ============================================================ --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

                {{-- ==================== KOLOM KIRI (lg:col-span-7): Ringkasan Pesanan & Pengiriman ==================== --}}
                <div class="lg:col-span-7 space-y-4 min-w-0">
                    <h2 class="flex items-center gap-2 text-sm sm:text-base font-bold text-gray-800 pb-1 border-b border-stone-100">
                        <span class="w-6 h-6 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center text-xs shrink-0 shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </span>
                        <span>Ringkasan Pesanan</span>
                    </h2>

                    <div class="bg-gray-50/80 rounded-2xl border border-gray-200/80 p-4 sm:p-5 space-y-3 text-xs sm:text-sm shadow-2xs divide-y divide-gray-200/60">
                        {{-- Kode Pesanan --}}
                        <div class="flex justify-between items-center gap-2">
                            <span class="text-gray-500 font-medium shrink-0">Kode Pesanan</span>
                            <span class="font-bold text-[#6B0F1A] text-xs sm:text-sm tracking-wide bg-rose-50 px-2.5 py-0.5 rounded-lg border border-rose-100">{{ $order->kode_pesanan }}</span>
                        </div>

                        {{-- Status Pesanan --}}
                        <div class="flex justify-between items-center gap-2 pt-3">
                            <span class="text-gray-500 font-medium shrink-0">Status</span>
                            @php
                                $statusColor = match($order->status) {
                                    \App\Enums\StatusPesanan::Pending => 'bg-amber-100 text-amber-700 border border-amber-200/60',
                                    \App\Enums\StatusPesanan::Proses  => 'bg-blue-100 text-blue-700 border border-blue-200/60',
                                    \App\Enums\StatusPesanan::Selesai => 'bg-emerald-100 text-emerald-700 border border-emerald-200/60',
                                    \App\Enums\StatusPesanan::Ditolak => 'bg-red-100 text-red-700 border border-red-200/60',
                                    \App\Enums\StatusPesanan::Batal   => 'bg-gray-200 text-gray-600 border border-gray-300/60',
                                    \App\Enums\StatusPesanan::Expired => 'bg-stone-200 text-stone-700 border border-stone-300/60',
                                };
                                $statusLabel = match($order->status) {
                                    \App\Enums\StatusPesanan::Pending => 'Menunggu Konfirmasi',
                                    \App\Enums\StatusPesanan::Proses  => 'Sedang Diproses',
                                    \App\Enums\StatusPesanan::Selesai => 'Selesai',
                                    \App\Enums\StatusPesanan::Ditolak => 'Ditolak',
                                    \App\Enums\StatusPesanan::Batal   => 'Dibatalkan',
                                    \App\Enums\StatusPesanan::Expired => 'Kedaluwarsa',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $statusColor }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        {{-- Nama Pemesan --}}
                        <div class="flex justify-between items-center gap-2 pt-3">
                            <span class="text-gray-500 font-medium shrink-0">Nama Pemesan</span>
                            <span class="font-semibold text-gray-800 text-right">{{ $order->nama_pemesan }}</span>
                        </div>

                        {{-- No HP / WA --}}
                        <div class="flex justify-between items-center gap-2 pt-3">
                            <span class="text-gray-500 font-medium shrink-0">No HP / WA</span>
                            <span class="font-semibold text-gray-800 text-right">{{ $order->nomor_wa }}</span>
                        </div>

                        {{-- Alamat Lengkap --}}
                        <div class="flex flex-col sm:flex-row justify-between items-start gap-1 sm:gap-4 pt-3">
                            <span class="text-gray-500 font-medium shrink-0">Alamat</span>
                            <span class="font-semibold text-gray-800 text-left sm:text-right break-words min-w-0 flex-1 whitespace-pre-line">{{ $order->alamat_lengkap }}</span>
                        </div>

                        {{-- Opsi Pengiriman --}}
                        <div class="flex justify-between items-center gap-2 pt-3">
                            <span class="text-gray-500 font-medium shrink-0">Opsi Pengiriman</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-stone-100 text-stone-700">
                                {{ $order->opsi_pengiriman->label() }}
                            </span>
                        </div>

                        {{-- Tanggal Ambil --}}
                        @if ($order->tanggal_ambil)
                            <div class="flex justify-between items-center gap-2 pt-3">
                                <span class="text-gray-500 font-medium shrink-0">Tanggal Ambil</span>
                                <span class="font-bold text-[#6B0F1A]">
                                    {{ $order->tanggal_ambil->translatedFormat('d F Y') }}
                                </span>
                            </div>
                        @endif

                        {{-- Metode Pembayaran --}}
                        <div class="flex justify-between items-center gap-2 pt-3">
                            <span class="text-gray-500 font-medium shrink-0">Metode Pembayaran</span>
                            <span class="font-semibold text-gray-800 text-right">{{ $order->metode_pembayaran->label() }}</span>
                        </div>
                    </div>
                </div>

                {{-- ==================== KOLOM KANAN (lg:col-span-5): Rincian Item, Total & Tombol Aksi ==================== --}}
                <div class="lg:col-span-5 space-y-5 min-w-0 lg:sticky lg:top-24">
                    {{-- Rincian Item --}}
                    <div class="space-y-3">
                        <h2 class="flex items-center gap-2 text-sm sm:text-base font-bold text-gray-800 pb-1 border-b border-stone-100">
                            <span class="w-6 h-6 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center text-xs shrink-0 shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </span>
                            <span>Rincian Item</span>
                        </h2>

                        <div class="bg-gray-50/80 rounded-2xl border border-gray-200/80 p-4 sm:p-5 space-y-3.5 shadow-2xs">
                            <div class="space-y-3 divide-y divide-gray-200/50">
                                @foreach ($order->items as $item)
                                    <div class="flex items-center gap-3 {{ $loop->first ? '' : 'pt-3' }}">
                                        {{-- Thumbnail --}}
                                        <div class="w-13 h-13 sm:w-14 sm:h-14 rounded-xl overflow-hidden bg-[#FAF2EE] shrink-0 border border-stone-200/60 flex items-center justify-center">
                                            @if ($item->product?->foto_url)
                                                <img src="{{ $item->product->foto_url }}"
                                                     alt="{{ $item->product->nama_kue }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full p-2 flex items-center justify-center">
                                                    <x-category-icon :category="$item->product?->category ?? 'tart'" class="w-full h-full" />
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Item info --}}
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs sm:text-sm font-semibold text-gray-800 truncate">{{ $item->product?->nama_kue ?? 'Kue Custom' }}</p>
                                            @if ($item->catatan)
                                                <p class="text-[11px] sm:text-xs text-amber-700 italic truncate">{{ $item->catatan }}</p>
                                            @endif
                                            <span class="text-[11px] text-gray-500">x{{ $item->jumlah }}</span>
                                        </div>

                                        {{-- Subtotal --}}
                                        <div class="text-right shrink-0">
                                            <p class="text-xs sm:text-sm font-bold text-gray-800">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Total Harga Box --}}
                            <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                                <span class="text-xs sm:text-sm font-bold text-gray-700">Total Harga</span>
                                <span class="text-lg sm:text-xl font-extrabold text-[#6B0F1A]">Rp{{ number_format($order->total_harga, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Aksi & CTA WhatsApp --}}
                    <div class="space-y-3 pt-1">
                        @if ($whatsappUrl)
                            <div class="space-y-2">
                                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"
                                   class="flex items-center justify-center gap-2 w-full py-3.5 px-5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-sm sm:text-base text-center rounded-full shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-emerald-600/30">
                                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                    <span>Kirim Pesanan ke WhatsApp Penjual</span>
                                </a>
                                <p class="text-[11px] text-gray-500 text-center leading-relaxed">
                                    Rincian pesanan sudah terisi otomatis, kamu tinggal tekan kirim di WhatsApp.
                                </p>
                            </div>
                        @endif

                        {{-- Tombol Chat Khusus Pesanan Custom Cake --}}
                        @if ($order->tipe_pesanan === \App\Enums\TipePesanan::Custom)
                            <a href="{{ route('customer.chat.order', $order->kode_pesanan) }}"
                               class="flex items-center justify-center gap-2 w-full py-3.5 px-5 bg-white hover:bg-stone-50 border-2 border-[#6B0F1A] text-[#6B0F1A] font-bold text-sm sm:text-base text-center rounded-full shadow-sm hover:shadow-md transition-all duration-200">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <span>Chat Penjual Mengenai Custom Cake Ini</span>
                            </a>
                        @endif

                        <a href="{{ route('customer.produk.index') }}"
                           class="flex items-center justify-center gap-2 w-full py-3 px-5 bg-[#6B0F1A] hover:bg-[#540F18] active:bg-[#3A0A0F] text-white font-bold text-xs sm:text-sm text-center rounded-full shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-[#6B0F1A]/30">
                            <span>Kembali ke Katalog Produk</span>
                        </a>

                        <div class="text-center pt-1">
                            <a href="{{ route('customer.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-[#6B0F1A] font-medium transition underline underline-offset-2">
                                <span>← Kembali ke Beranda</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

</body>
</html>
