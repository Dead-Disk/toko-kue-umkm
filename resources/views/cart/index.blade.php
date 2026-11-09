<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - {{ \App\Models\StoreSetting::current()?->store_name ?? 'Nak Mbarep' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FAF2EE] text-gray-800 min-h-screen antialiased flex flex-col font-sans">
    {{-- Top Navigation Bar --}}
    @include('customer.partials.navbar')

    <main class="max-w-md md:max-w-3xl mx-auto px-4 py-6 w-full flex-1">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-rose-200/60">
            <h1 class="text-xl sm:text-2xl font-serif font-bold text-[#6B0F1A]">Keranjang Belanja</h1>
            <a href="{{ route('customer.produk.index') }}" class="text-xs text-[#6B0F1A] hover:underline font-semibold">
                ← Lanjut Belanja
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div data-toast-message="{{ session('error') }}" data-toast-type="error" hidden></div>
            <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs flex items-center gap-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-xs">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        @continue(session('error') === $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (empty($cart))
            <div class="bg-white rounded-2xl p-8 text-center border border-rose-100 shadow-xs">
                <svg class="w-16 h-16 mx-auto text-rose-200 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <p class="text-gray-600 font-medium mb-3">Keranjang belanja Anda masih kosong.</p>
                <a href="{{ route('customer.produk.index') }}" class="inline-flex items-center gap-1.5 bg-[#6B0F1A] hover:bg-[#520912] text-white text-xs font-semibold px-4 py-2 rounded-xl transition shadow-xs">
                    Kembali Belanja
                </a>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-rose-100 shadow-xs overflow-hidden mb-5">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-[#F8ECE7] text-gray-700 font-semibold uppercase text-[11px] tracking-wider border-b border-rose-100">
                            <tr>
                                <th class="p-3 text-center w-10">No</th>
                                <th class="p-3">Nama Kue</th>
                                <th class="p-3">Catatan per Kue</th>
                                <th class="p-3 text-right">Harga Satuan</th>
                                <th class="p-3 text-center">Jumlah</th>
                                <th class="p-3 text-right">Subtotal</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rose-100/60">
                            @foreach ($cart as $item)
                                <tr class="hover:bg-rose-50/30 transition">
                                    <td class="p-3 text-center text-gray-500">{{ $loop->iteration }}</td>
                                    <td class="p-3 font-semibold text-gray-900">
                                        @if ($item['image_url'])
                                            <a href="{{ $item['image_url'] }}" target="_blank" class="hover:text-[#6B0F1A] underline">
                                                {{ $item['name'] }}
                                            </a>
                                        @else
                                            {{ $item['name'] }}
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        <form action="{{ route('cart.update', $item['id']) }}" method="POST" class="space-y-1">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="quantity" value="{{ $item['quantity'] }}">
                                            <textarea name="catatan" rows="2" maxlength="1000" aria-label="Catatan untuk {{ $item['name'] }}" placeholder="Catatan untuk {{ $item['name'] }}" class="w-full text-[11px] p-1.5 border border-rose-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#6B0F1A] bg-[#FFFBF9]">{{ $item['catatan'] ?? '' }}</textarea>
                                            <button type="submit" class="text-[10px] text-[#6B0F1A] hover:underline font-semibold block">Simpan Catatan</button>
                                        </form>
                                    </td>
                                    <td class="p-3 text-right text-gray-700 whitespace-nowrap">
                                        Rp{{ number_format($item['price'], 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-center">
                                        <form action="{{ route('cart.update', $item['id']) }}" method="POST" class="inline-flex items-center border border-rose-200 rounded-lg bg-white overflow-hidden shadow-2xs">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}" @disabled($item['quantity'] <= 1) class="px-2 py-1 bg-gray-50 hover:bg-rose-100 text-gray-700 font-bold disabled:opacity-40 disabled:cursor-not-allowed">-</button>
                                            <span class="px-2.5 py-1 text-xs font-bold text-gray-800">{{ $item['quantity'] }}</span>
                                            <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}" class="px-2 py-1 bg-gray-50 hover:bg-rose-100 text-gray-700 font-bold">+</button>
                                        </form>
                                    </td>
                                    <td class="p-3 text-right font-bold text-[#6B0F1A] whitespace-nowrap">
                                        Rp{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-center">
                                        <form action="{{ route('cart.remove', $item['id']) }}" method="POST" data-confirm="Hapus produk ini dari keranjang?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-medium p-1 hover:underline">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-[#FDF4F0] font-bold">
                                <td colspan="5" class="p-3.5 text-right text-gray-800 text-xs uppercase tracking-wide">Grand Total:</td>
                                <td colspan="2" class="p-3.5 text-left text-base text-[#6B0F1A]">Rp{{ number_format($total, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                <form action="{{ route('cart.clear') }}" method="POST" data-confirm="Kosongkan seluruh keranjang?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-gray-500 hover:text-rose-700 underline font-medium">
                        Kosongkan Keranjang
                    </button>
                </form>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <a href="{{ route('customer.produk.index') }}" class="flex-1 sm:flex-none text-center bg-white border border-[#6B0F1A] text-[#6B0F1A] hover:bg-rose-50 text-xs font-bold py-2.5 px-4 rounded-xl transition">
                        Kembali Belanja
                    </a>
                    <a href="{{ route('customer.pesanan.review-cart') }}" class="flex-1 sm:flex-none text-center bg-[#6B0F1A] hover:bg-[#520912] text-white text-xs font-bold py-2.5 px-5 rounded-xl transition shadow-xs">
                        Lanjut ke Review Pesanan →
                    </a>
                </div>
            </div>
        @endif
    </main>
</body>
</html>
