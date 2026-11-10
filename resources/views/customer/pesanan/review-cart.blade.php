<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - {{ $storeSetting->store_name ?? 'Nak Mbarep' }}</title>
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

                {{-- Header --}}
                <div>
                    <h1 class="text-2xl font-serif font-bold text-[#6B0F1A]">Checkout</h1>
                    <p class="text-xs text-gray-500 mt-0.5 italic">Selesaikan pesananmu dengan mudah!</p>
                </div>

                {{-- Flash / validation errors --}}
                @if (session('error'))
                    <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            @continue(session('error') === $error)
                            <p>• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- Checkout Form --}}
                <form action="{{ route('customer.pesanan.checkout') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="checkout_mode" value="cart">

                    {{-- ============================================================ --}}
                    {{-- Section 1: Data Pengiriman --}}
                    {{-- ============================================================ --}}
                    <section class="space-y-3">
                        <h2 class="flex items-center gap-2 text-sm font-bold text-gray-800">
                            <span class="w-6 h-6 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center text-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </span>
                            Data Pengiriman
                        </h2>

                        {{-- Nama Lengkap --}}
                        <div>
                            <label for="nama_pemesan" class="block text-xs font-medium text-gray-600 mb-1">Nama Lengkap</label>
                            <input type="text" id="nama_pemesan" name="nama_pemesan" value="{{ old('nama_pemesan') }}"
                                   placeholder="Contoh: Calino Wicaksono"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#6B0F1A]/30 focus:border-[#6B0F1A] outline-none transition placeholder:text-gray-400"
                                   required>
                        </div>

                        {{-- Nomor Telepon --}}
                        <div>
                            <label for="nomor_wa" class="block text-xs font-medium text-gray-600 mb-1">Nomor Telepon</label>
                            <input type="tel" id="nomor_wa" name="nomor_wa" value="{{ old('nomor_wa') }}"
                                   placeholder="Contoh: 08123-456-789"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#6B0F1A]/30 focus:border-[#6B0F1A] outline-none transition placeholder:text-gray-400"
                                   required>
                        </div>

                        {{-- Alamat Lengkap --}}
                        <div>
                            <label for="alamat_lengkap" class="block text-xs font-medium text-gray-600 mb-1">Alamat Lengkap</label>
                            <textarea id="alamat_lengkap" name="alamat_lengkap" rows="2"
                                      placeholder="Contoh: Jl. Chamomile, No. 3, RT 03/RW 04, Campurdarat"
                                      class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#6B0F1A]/30 focus:border-[#6B0F1A] outline-none transition placeholder:text-gray-400 resize-none"
                                      required>{{ old('alamat_lengkap') }}</textarea>
                        </div>


                    </section>

                    {{-- ============================================================ --}}
                    {{-- Section 2: Pilih Metode Pembayaran --}}
                    {{-- ============================================================ --}}
                    @php
                        $bankSiap = $storeSetting->hasBankDetails();
                        $qrisSiap = $storeSetting->hasQrisImage();
                    @endphp

                    <section class="space-y-3" data-payment-group>
                        <h2 class="flex items-center gap-2 text-sm font-bold text-gray-800">
                            <span class="w-6 h-6 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center text-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </span>
                            Pilih Metode Pembayaran
                        </h2>

                        <div class="space-y-2">
                            {{-- Transfer Bank --}}
                            <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer transition hover:border-[#6B0F1A]/40 hover:bg-[#FAF2EE]/50 has-[:checked]:border-[#6B0F1A] has-[:checked]:bg-[#FAF2EE] {{ !$bankSiap ? 'opacity-50 cursor-not-allowed' : '' }}">
                                <input type="radio" name="metode_pembayaran" value="transfer_bank"
                                       class="mt-0.5 accent-[#6B0F1A]"
                                       @checked($bankSiap && old('metode_pembayaran', 'transfer_bank') === 'transfer_bank')
                                       @disabled(!$bankSiap)>
                                <div>
                                    <span class="text-sm font-semibold text-gray-800">Transfer Bank</span>
                                    <p class="text-xs text-gray-500 mt-0.5">Bayar melalui BCA, BNI, BRI, Mandiri, dan lainnya</p>
                                    @unless ($bankSiap)
                                        <p class="text-xs text-amber-600 mt-0.5 font-medium">Transfer Bank belum dikonfigurasi oleh penjual.</p>
                                    @endunless
                                </div>
                            </label>

                            {{-- E-Wallet / QRIS --}}
                            <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer transition hover:border-[#6B0F1A]/40 hover:bg-[#FAF2EE]/50 has-[:checked]:border-[#6B0F1A] has-[:checked]:bg-[#FAF2EE] {{ !$qrisSiap ? 'opacity-50 cursor-not-allowed' : '' }}">
                                <input type="radio" name="metode_pembayaran" value="qris"
                                       class="mt-0.5 accent-[#6B0F1A]"
                                       @checked(old('metode_pembayaran') === 'qris')
                                       @disabled(!$qrisSiap)>
                                <div>
                                    <span class="text-sm font-semibold text-gray-800">E-Wallet</span>
                                    <p class="text-xs text-gray-500 mt-0.5">GoPay, OVO, DANA, ShopeePay, dan lainnya</p>
                                    @unless ($qrisSiap)
                                        <p class="text-xs text-amber-600 mt-0.5 font-medium">QRIS belum dikonfigurasi oleh penjual.</p>
                                    @endunless
                                </div>
                            </label>

                            {{-- COD / Tunai --}}
                            <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer transition hover:border-[#6B0F1A]/40 hover:bg-[#FAF2EE]/50 has-[:checked]:border-[#6B0F1A] has-[:checked]:bg-[#FAF2EE]">
                                <input type="radio" name="metode_pembayaran" value="cash"
                                       class="mt-0.5 accent-[#6B0F1A]"
                                       @checked(old('metode_pembayaran') === 'cash')>
                                <div>
                                    <span class="text-sm font-semibold text-gray-800">COD — Tunai / Bayar di Toko</span>
                                    <p class="text-xs text-gray-500 mt-0.5">Bayar saat pesanan tiba</p>
                                </div>
                            </label>
                        </div>

                        {{-- Bank detail (revealed by JS) --}}
                        @if ($bankSiap)
                            <div data-payment-detail="transfer_bank" class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-800" hidden>
                                <p class="font-semibold mb-1">Rekening Tujuan:</p>
                                <p>{{ $storeSetting->bank_name }} — {{ $storeSetting->bank_account_number }}</p>
                                <p>a.n. {{ $storeSetting->bank_account_holder }}</p>
                            </div>
                        @endif

                        @if ($qrisSiap)
                            <div data-payment-detail="qris" class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-center" hidden>
                                <p class="text-xs font-semibold text-blue-800 mb-2">Scan QRIS berikut untuk membayar:</p>
                                <img src="{{ asset('storage/'.$storeSetting->qris_image_path) }}" alt="QRIS {{ $storeSetting->store_name }}" class="w-48 mx-auto rounded-lg">
                            </div>
                        @endif

                        {{-- Payment proof upload --}}
                        <div data-payment-proof @if(old('metode_pembayaran') === 'cash') hidden @endif class="space-y-1.5">
                            <label for="bukti_pembayaran" class="block text-xs font-medium text-gray-600">Unggah Bukti Pembayaran (JPG/PNG)</label>
                            <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*" data-payment-proof-input
                                   class="w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#FAF2EE] file:text-[#6B0F1A] hover:file:bg-[#F4E1D9] transition">
                            <p class="text-[11px] text-gray-400">Wajib untuk Transfer Bank dan E-Wallet/QRIS.</p>
                        </div>

                        <div data-payment-cash-info @unless(old('metode_pembayaran') === 'cash') hidden @endunless class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                            <p class="text-xs text-amber-700 italic">Pembayaran tunai dilakukan secara langsung di toko / saat penerimaan kue.</p>
                        </div>
                    </section>

                    {{-- ============================================================ --}}
                    {{-- Section 3: Ringkasan Pesanan --}}
                    {{-- ============================================================ --}}
                    <section class="space-y-3">
                        <h2 class="flex items-center gap-2 text-sm font-bold text-gray-800">
                            <span class="w-6 h-6 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center text-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </span>
                            Ringkasan Pesanan
                        </h2>

                        {{-- Cart items --}}
                        <div class="space-y-3">
                            @foreach ($cart as $item)
                                <div class="flex items-center gap-3">
                                    {{-- Thumbnail --}}
                                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 shrink-0">
                                        @if (!empty($item['image_url']))
                                            <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z"/></svg>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Item info --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $item['name'] }}</p>
                                        <p class="text-xs text-gray-500">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                        @if (!empty($item['catatan']))
                                            <p class="text-xs text-amber-700 mt-0.5 italic">{{ $item['catatan'] }}</p>
                                        @endif
                                    </div>

                                    {{-- Qty --}}
                                    <div class="text-center shrink-0">
                                        <span class="text-sm font-medium text-gray-600">{{ $item['quantity'] }}</span>
                                    </div>

                                    {{-- Subtotal --}}
                                    <div class="text-right shrink-0">
                                        <p class="text-sm font-semibold text-gray-800">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-gray-100 pt-3 space-y-1.5">
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Subtotal</span>
                                <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Ongkos Kirim</span>
                                <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </section>

                    {{-- ============================================================ --}}
                    {{-- Section 4: Opsi Pengiriman --}}
                    {{-- ============================================================ --}}
                    <section class="space-y-3">
                        <h2 class="flex items-center gap-2 text-sm font-bold text-gray-800">
                            <span class="w-6 h-6 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center text-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            </span>
                            Opsi Pengiriman
                        </h2>

                        <div class="space-y-2">
                            <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer transition hover:border-[#6B0F1A]/40 hover:bg-[#FAF2EE]/50 has-[:checked]:border-[#6B0F1A] has-[:checked]:bg-[#FAF2EE]">
                                <input type="radio" name="opsi_pengiriman" value="kurir_toko"
                                       class="accent-[#6B0F1A]"
                                       @checked(old('opsi_pengiriman', 'kurir_toko') === 'kurir_toko')>
                                <span class="text-sm font-medium text-gray-700">Diantar</span>
                            </label>

                            <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer transition hover:border-[#6B0F1A]/40 hover:bg-[#FAF2EE]/50 has-[:checked]:border-[#6B0F1A] has-[:checked]:bg-[#FAF2EE]">
                                <input type="radio" name="opsi_pengiriman" value="ambil_sendiri"
                                       class="accent-[#6B0F1A]"
                                       @checked(old('opsi_pengiriman') === 'ambil_sendiri')>
                                <span class="text-sm font-medium text-gray-700">Ambil di tempat</span>
                            </label>
                        </div>
                    </section>

                    {{-- ============================================================ --}}
                    {{-- Total Akhir --}}
                    {{-- ============================================================ --}}
                    <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                        <span class="text-sm font-bold text-gray-800">Total</span>
                        <span class="text-lg font-extrabold text-[#6B0F1A]">Rp{{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    {{-- Catatan tambahan --}}
                    <div>
                        <label for="catatan" class="block text-xs font-medium text-gray-600 mb-1">Catatan Tambahan (opsional)</label>
                        <textarea id="catatan" name="catatan" rows="2" maxlength="1000"
                                  placeholder="Misal: instruksi pengiriman atau permintaan khusus"
                                  class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#6B0F1A]/30 focus:border-[#6B0F1A] outline-none transition placeholder:text-gray-400 resize-none">{{ old('catatan') }}</textarea>
                    </div>

                    {{-- ============================================================ --}}
                    {{-- CTA Button --}}
                    {{-- ============================================================ --}}
                    <button type="submit"
                            class="w-full py-3.5 bg-[#6B0F1A] hover:bg-[#540F18] active:bg-[#3A0A0F] text-white font-bold text-base rounded-full shadow-lg transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-[#6B0F1A]/30">
                        Bayar Sekarang
                    </button>

                    {{-- Back to cart link --}}
                    <div class="text-center pb-2">
                        <a href="{{ route('cart.index') }}" class="text-xs text-gray-500 hover:text-[#6B0F1A] underline underline-offset-2 transition">
                            ← Kembali ke Keranjang
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </main>

</body>
</html>
