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
    <main class="flex-1 flex items-start justify-center px-3 py-6 sm:px-4 sm:py-8">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden">

            {{-- Scrollable inner content --}}
            <div class="max-h-[85vh] overflow-y-auto px-5 py-6 sm:px-6 sm:py-8 space-y-6">

                {{-- ============================================================ --}}
                {{-- Success Banner --}}
                {{-- ============================================================ --}}
                <div class="text-center space-y-2 pt-2">
                    {{-- Check circle icon --}}
                    <div class="mx-auto w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center">
                        <svg class="w-9 h-9 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>

                    <h1 class="text-xl font-serif font-bold text-[#6B0F1A]">Pesanan Berhasil Dikirim</h1>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Terima kasih, <span class="font-semibold text-gray-700">{{ $order->nama_pemesan }}</span>!
                        Pesanan kamu sudah kami terima dan saat ini
                        <strong class="text-[#6B0F1A]">sedang menunggu konfirmasi penjual</strong>.
                    </p>
                </div>

                {{-- ============================================================ --}}
                {{-- Ringkasan Pesanan --}}
                {{-- ============================================================ --}}
                <section class="space-y-3">
                    <h2 class="flex items-center gap-2 text-sm font-bold text-gray-800">
                        <span class="w-6 h-6 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center text-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </span>
                        Ringkasan Pesanan
                    </h2>

                    <div class="bg-gray-50 rounded-xl p-4 space-y-2.5 text-sm">
                        {{-- Kode Pesanan --}}
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-xs">Kode Pesanan</span>
                            <span class="font-bold text-[#6B0F1A] text-xs tracking-wide">{{ $order->kode_pesanan }}</span>
                        </div>

                        <div class="border-t border-gray-200"></div>

                        {{-- Nama Pemesan --}}
                        <div class="flex justify-between">
                            <span class="text-gray-500 text-xs">Nama Pemesan</span>
                            <span class="text-xs font-medium text-gray-800">{{ $order->nama_pemesan }}</span>
                        </div>

                        {{-- No HP / WA --}}
                        <div class="flex justify-between">
                            <span class="text-gray-500 text-xs">No HP / WA</span>
                            <span class="text-xs font-medium text-gray-800">{{ $order->nomor_wa }}</span>
                        </div>

                        {{-- Alamat Lengkap --}}
                        <div class="flex justify-between gap-4">
                            <span class="text-gray-500 text-xs shrink-0">Alamat</span>
                            <span class="text-xs font-medium text-gray-800 text-right">{{ $order->alamat_lengkap }}</span>
                        </div>

                        <div class="border-t border-gray-200"></div>

                        {{-- Opsi Pengiriman --}}
                        <div class="flex justify-between">
                            <span class="text-gray-500 text-xs">Opsi Pengiriman</span>
                            <span class="text-xs font-medium text-gray-800">
                                {{ $order->opsi_pengiriman->label() }}
                            </span>
                        </div>

                        {{-- Metode Pembayaran --}}
                        <div class="flex justify-between">
                            <span class="text-gray-500 text-xs">Metode Pembayaran</span>
                            <span class="text-xs font-medium text-gray-800">{{ $order->metode_pembayaran->label() }}</span>
                        </div>

                        <div class="border-t border-gray-200"></div>

                        {{-- Status Pesanan --}}
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-xs">Status</span>
                            @php
                                $statusColor = match($order->status) {
                                    \App\Enums\StatusPesanan::Pending => 'bg-amber-100 text-amber-700',
                                    \App\Enums\StatusPesanan::Proses  => 'bg-blue-100 text-blue-700',
                                    \App\Enums\StatusPesanan::Selesai => 'bg-emerald-100 text-emerald-700',
                                    \App\Enums\StatusPesanan::Ditolak => 'bg-red-100 text-red-700',
                                    \App\Enums\StatusPesanan::Batal   => 'bg-gray-200 text-gray-600',
                                };
                                $statusLabel = match($order->status) {
                                    \App\Enums\StatusPesanan::Pending => 'Menunggu Konfirmasi',
                                    \App\Enums\StatusPesanan::Proses  => 'Sedang Diproses',
                                    \App\Enums\StatusPesanan::Selesai => 'Selesai',
                                    \App\Enums\StatusPesanan::Ditolak => 'Ditolak',
                                    \App\Enums\StatusPesanan::Batal   => 'Dibatalkan',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $statusColor }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- Rincian Item --}}
                {{-- ============================================================ --}}
                <section class="space-y-3">
                    <h2 class="flex items-center gap-2 text-sm font-bold text-gray-800">
                        <span class="w-6 h-6 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center text-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </span>
                        Rincian Item
                    </h2>

                    <div class="space-y-3">
                        @foreach ($order->items as $item)
                            <div class="flex items-center gap-3">
                                {{-- Thumbnail --}}
                                <div class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 shrink-0">
                                    @if ($item->product?->foto)
                                        <img src="{{ asset('storage/' . $item->product->foto) }}"
                                             alt="{{ $item->product->nama_kue }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Item info --}}
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $item->product?->nama_kue ?? 'Kue Custom' }}</p>
                                    @if ($item->catatan)
                                        <p class="text-xs text-amber-700 italic truncate">{{ $item->catatan }}</p>
                                    @endif
                                </div>

                                {{-- Qty --}}
                                <div class="text-center shrink-0">
                                    <span class="text-xs text-gray-500">x{{ $item->jumlah }}</span>
                                </div>

                                {{-- Subtotal --}}
                                <div class="text-right shrink-0">
                                    <p class="text-sm font-semibold text-gray-800">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Total --}}
                    <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                        <span class="text-sm font-bold text-gray-800">Total Harga</span>
                        <span class="text-lg font-extrabold text-[#6B0F1A]">Rp{{ number_format($order->total_harga, 0, ',', '.') }}</span>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- CTA Button --}}
                {{-- ============================================================ --}}
                @if ($whatsappUrl)
                    <div class="space-y-2">
                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"
                           class="flex items-center justify-center gap-2 w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-base text-center rounded-full shadow-lg transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-emerald-600/30">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            Kirim Pesanan ke WhatsApp Penjual
                        </a>
                        <p class="text-[11px] text-gray-500 text-center leading-relaxed">
                            Rincian pesanan sudah terisi otomatis, kamu tinggal tekan kirim di WhatsApp.
                        </p>
                    </div>
                @endif

                <a href="{{ route('customer.produk.index') }}"
                   class="block w-full py-3.5 bg-[#6B0F1A] hover:bg-[#540F18] active:bg-[#3A0A0F] text-white font-bold text-base text-center rounded-full shadow-lg transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-[#6B0F1A]/30">
                    Kembali ke Katalog Produk
                </a>

                <div class="text-center pb-2">
                    <a href="{{ route('customer.index') }}" class="text-xs text-gray-500 hover:text-[#6B0F1A] underline underline-offset-2 transition">
                        ← Kembali ke Beranda
                    </a>
                </div>

            </div>
        </div>
    </main>

</body>
</html>
