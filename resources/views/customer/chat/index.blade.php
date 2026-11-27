<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Penjual - {{ \App\Models\StoreSetting::current()?->store_name ?? 'Nak Mbarep' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-nav.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
        .chat-scroll {
            scrollbar-width: thin;
            scrollbar-color: #d6b5af #f8ece8;
        }
        .chat-scroll::-webkit-scrollbar { width: 6px; }
        .chat-scroll::-webkit-scrollbar-track { background: #f8ece8; }
        .chat-scroll::-webkit-scrollbar-thumb { background: #d6b5af; border-radius: 9999px; }
        .chat-scroll::-webkit-scrollbar-thumb:hover { background: #b88f87; }
    </style>
</head>
<body class="bg-[#FAF2EE] text-stone-800 min-h-screen antialiased flex flex-col font-sans">

    {{-- Top Navigation Bar --}}
    @include('customer.partials.navbar')

    <main class="flex-1 flex items-stretch justify-center p-2 sm:p-4 lg:p-6 xl:px-8 w-full max-w-7xl mx-auto">
        <div class="w-full bg-white rounded-2xl sm:rounded-3xl shadow-xl border border-[#F4E1D9] flex flex-col h-[calc(100dvh-4.25rem)] sm:h-[calc(100dvh-5.5rem)] lg:h-[calc(100dvh-6.5rem)] max-h-[860px] lg:max-h-[920px] min-h-[460px] sm:min-h-[520px] overflow-hidden transition-all duration-300">
            
            {{-- Header Chat --}}
            <div class="bg-[#6B0F1A] text-white px-3.5 py-3 sm:px-6 sm:py-4 lg:px-8 flex items-center justify-between shadow-xs shrink-0 gap-2 sm:gap-4">
                <div class="flex items-center gap-2 sm:gap-3.5 min-w-0">
                    <a href="{{ route('customer.index') }}" class="p-1.5 rounded-xl hover:bg-white/10 active:scale-95 text-stone-200 hover:text-white transition shrink-0" title="Kembali ke Beranda" aria-label="Kembali ke Beranda">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <div class="relative shrink-0">
                        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-full bg-white/15 border border-white/25 flex items-center justify-center font-bold text-white text-base sm:text-lg shadow-inner">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 15.5a3.5 3.5 0 01-3.5 3.5H6.5A3.5 3.5 0 013 15.5V14a1 1 0 011-1h16a1 1 0 011 1v1.5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 13V9a2 2 0 012-2h12a2 2 0 012 2v4"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V4m4 3V4m4 3V4"/>
                            </svg>
                        </div>
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 sm:w-3 sm:h-3 bg-emerald-400 border-2 border-[#6B0F1A] rounded-full ring-1 ring-emerald-500/30"></span>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-xs sm:text-base lg:text-lg font-bold leading-tight flex items-center gap-2 text-white truncate">
                            <span class="truncate">Layanan Chat Nak Mbarep</span>
                        </h1>
                        <p class="text-[10px] sm:text-xs lg:text-[13px] text-rose-100/80 flex items-center gap-1.5 mt-0.5 truncate">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                            <span class="truncate">Admin &amp; Staf siap melayani</span>
                        </p>
                    </div>
                </div>

                @if ($thread)
                    @php
                        $secretChatUrl = $thread->getCustomerSecretUrl();
                    @endphp
                    <div class="flex items-center gap-1.5 sm:gap-2.5 shrink-0">
                        @if ($secretChatUrl)
                            {{-- Tombol Salin Tautan Rahasia Chat --}}
                            <button type="button"
                                    id="btnCopySecretLink"
                                    data-url="{{ $secretChatUrl }}"
                                    title="Salin tautan rahasia untuk membuka kembali percakapan ini"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 text-xs font-semibold text-rose-100 hover:text-white transition cursor-pointer border border-white/10">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                                <span id="btnCopyText" class="hidden sm:inline">Simpan Tautan</span>
                            </button>
                        @endif

                        <a href="{{ route('customer.chat.index', ['mode' => 'inquiry', 'verify' => 1]) }}"
                           class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-semibold text-rose-100 hover:text-white transition">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span>Tanya Baru</span>
                        </a>

                        <div class="text-right min-w-0 max-w-[90px] min-[400px]:max-w-[130px] sm:max-w-xs">
                            <span class="text-xs sm:text-sm font-semibold block text-stone-100 truncate">{{ $thread->customer_name }}</span>
                            <span class="text-[10px] sm:text-xs text-stone-300 font-mono truncate block">{{ $thread->customer_phone }}</span>
                        </div>
                        <form action="{{ route('customer.chat.end-session') }}" method="POST">
                            @csrf
                            <button type="submit" title="Akhiri sesi chat di browser ini" aria-label="Akhiri sesi chat" class="p-1.5 sm:p-2 sm:px-3 rounded-xl bg-white/10 hover:bg-white/20 active:scale-95 text-rose-100 hover:text-white transition flex items-center gap-1.5 text-xs font-medium cursor-pointer">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                <span class="hidden sm:inline text-xs font-semibold">Keluar</span>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="hidden sm:flex items-center gap-2 text-xs text-rose-100/90 bg-white/10 px-3.5 py-1.5 rounded-full border border-white/15 shrink-0">
                        <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Respon Cepat 08:00 - 20:00 WIB</span>
                    </div>
                @endif
            </div>

            @if (! $thread)
                {{-- Form Gerbang Keamanan: Pilihan 2 Mode (Verifikasi Pesanan vs Tanya Pra-Order) --}}
                <div class="flex-1 overflow-y-auto p-3 sm:p-5 lg:p-6 flex flex-col justify-start items-center bg-[#FAF2EE]/30 custom-scrollbar">
                    <div class="w-full max-w-lg lg:max-w-5xl xl:max-w-6xl mx-auto py-1 sm:py-3">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-8 xl:gap-10 items-start">
                            
                            {{-- Panel Informasi Kiri (Desktop / Widescreen View) --}}
                            <div class="hidden lg:flex lg:col-span-5 flex-col justify-between space-y-4 p-5 xl:p-6 rounded-3xl bg-linear-to-br from-[#FAF2EE] via-white to-[#FAF2EE] border border-[#F4E1D9] shadow-xs">
                                <div>
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#6B0F1A]/10 text-[#6B0F1A] text-xs font-bold mb-3">
                                        <svg class="w-3.5 h-3.5 shrink-0 text-[#6B0F1A]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <span>Pusat Bantuan Pelanggan</span>
                                    </div>
                                    <h2 class="text-lg xl:text-xl font-serif font-bold text-[#540F18] leading-snug">
                                        Konsultasi &amp; Komunikasi Langsung dengan Penjual
                                    </h2>
                                    <p class="text-xs xl:text-[13px] text-stone-600 mt-2 leading-relaxed">
                                        Hubungi tim Toko Kue Nak Mbarep untuk diskusi pesanan kustom, konfirmasi tanggal ambil, perubahan tulisan kue, atau pertanyaan produk.
                                    </p>
                                </div>

                                <div class="space-y-3 border-t border-b border-[#F4E1D9] py-3.5">
                                    <div class="flex items-start gap-2.5">
                                        <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 mt-0.5">
                                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-xs font-bold text-stone-800">Privasi Terjaga</h3>
                                            <p class="text-[11px] text-stone-500">Riwayat obrolan pesanan Anda hanya dapat diakses dengan verifikasi data yang cocok.</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-2.5">
                                        <div class="w-7 h-7 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 mt-0.5">
                                            <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4-4a2 2 0 012.8 0l3.2 3.2m-2-2 1.2-1.2a2 2 0 012.8 0L20 16m-2-8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-xs font-bold text-stone-800">Dukungan Lampiran Foto</h3>
                                            <p class="text-[11px] text-stone-500">Kirim foto contoh dekorasi kue langsung ke tim baker kami.</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-2.5">
                                        <div class="w-7 h-7 rounded-xl bg-rose-100 text-[#6B0F1A] flex items-center justify-center shrink-0 mt-0.5">
                                            <svg class="w-4 h-4 text-[#6B0F1A]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-xs font-bold text-stone-800">Pembaruan Real-Time</h3>
                                            <p class="text-[11px] text-stone-500">Pesan baru akan otomatis masuk secara langsung di browser tanpa perlu reload.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 text-[11px] text-stone-500">
                                    <svg class="w-4 h-4 text-[#6B0F1A] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Pilih opsi tab di atas formulir untuk mulai.</span>
                                </div>
                            </div>

                            {{-- Panel Formulir Kanan (Mobile & Desktop) --}}
                            <div class="w-full lg:col-span-7 bg-white rounded-2xl sm:rounded-3xl border border-[#F4E1D9] p-4 sm:p-6 lg:p-7 shadow-sm transition-all">
                                
                                {{-- Bar Button / Tab Switcher Mode 1 vs Mode 2 --}}
                                <div class="flex rounded-xl sm:rounded-2xl bg-[#F4E1D9]/60 border border-[#E8B8AE]/70 p-1 sm:p-1.5 mb-4 sm:mb-5 text-xs sm:text-sm font-semibold gap-1.5 shadow-xs shrink-0">
                                    <button type="button"
                                            id="tabOrderBtn"
                                            onclick="switchChatMode('order')"
                                            class="flex-1 py-2 sm:py-2.5 px-2 sm:px-3 rounded-lg sm:rounded-xl text-center transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer select-none {{ $defaultMode === 'order' ? 'bg-white text-[#6B0F1A] shadow-md font-bold border border-[#E8B8AE]' : 'text-stone-600 hover:text-[#6B0F1A] hover:bg-white/60 font-medium' }}">
                                        <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                        <span class="truncate font-bold">Diskusi Pesanan</span>
                                    </button>
                                    <button type="button"
                                            id="tabInquiryBtn"
                                            onclick="switchChatMode('inquiry')"
                                            class="flex-1 py-2 sm:py-2.5 px-2 sm:px-3 rounded-lg sm:rounded-xl text-center transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer select-none {{ $defaultMode === 'inquiry' ? 'bg-white text-[#6B0F1A] shadow-md font-bold border border-[#E8B8AE]' : 'text-stone-600 hover:text-[#6B0F1A] hover:bg-white/60 font-medium' }}">
                                        <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        <span class="truncate font-bold">Tanya-Tanya Baru</span>
                                    </button>
                                </div>

                                {{-- Alert Error --}}
                                @if ($errors->any())
                                    <div class="mb-4 p-3.5 rounded-xl sm:rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm shadow-2xs">
                                        <div class="font-bold mb-1 flex items-center gap-1.5">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            <span>Verifikasi Gagal:</span>
                                        </div>
                                        <ul class="list-disc list-inside space-y-0.5 text-rose-700 text-xs sm:text-[13px]">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- ================= MODE 1: Verifikasi Nomor HP + Kode Pesanan ================= --}}
                                <div id="modeOrderSection" class="{{ $defaultMode === 'order' ? '' : 'hidden' }}">
                                    <div class="text-center mb-4 sm:mb-5">
                                        <div class="w-10 h-10 mx-auto rounded-xl bg-[#6B0F1A]/10 text-[#6B0F1A] flex items-center justify-center mb-2 shadow-2xs">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                        </div>
                                        <h2 class="text-base sm:text-lg font-bold text-stone-800">Verifikasi Riwayat Pesanan</h2>
                                        <p class="text-xs text-stone-500 mt-0.5 max-w-sm mx-auto leading-relaxed">
                                            Untuk melindungi privasi obrolan Anda, masukkan pasangan <strong>Nomor WhatsApp</strong> dan <strong>Kode Pesanan</strong> yang sah.
                                        </p>
                                    </div>

                                    <form action="{{ route('customer.chat.verify-order') }}" method="POST" class="space-y-3.5">
                                        @csrf
                                        <div>
                                            <label for="order_code" class="block text-xs sm:text-sm font-bold text-stone-700 mb-1">Kode Pesanan</label>
                                            <input type="text"
                                                   id="order_code"
                                                   name="order_code"
                                                   value="{{ old('order_code', $presetOrder) }}"
                                                   placeholder="Contoh: ORD-20261126-8784"
                                                   required
                                                   class="w-full text-xs sm:text-sm px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl sm:rounded-2xl border border-[#E8B8AE]/70 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] bg-[#FAF2EE]/20 focus:bg-white font-mono uppercase transition">
                                            <p class="text-[10px] sm:text-[11px] text-stone-400 mt-0.5">Dapat ditemukan pada nota/halaman konfirmasi pesanan.</p>
                                        </div>

                                        <div>
                                            <label for="customer_phone_order" class="block text-xs sm:text-sm font-bold text-stone-700 mb-1">Nomor WhatsApp Pemesan</label>
                                            <input type="tel"
                                                   id="customer_phone_order"
                                                   name="customer_phone"
                                                   value="{{ old('customer_phone', $presetPhone) }}"
                                                   placeholder="Contoh: 08123456789 atau 628..."
                                                   required
                                                   class="w-full text-xs sm:text-sm px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl sm:rounded-2xl border border-[#E8B8AE]/70 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] bg-[#FAF2EE]/20 focus:bg-white font-mono transition">
                                            <p class="text-[10px] sm:text-[11px] text-stone-400 mt-0.5">Wajib sama dengan nomor yang digunakan saat memesan.</p>
                                        </div>

                                        <div>
                                            <label for="chat_pin_order" class="block text-xs sm:text-sm font-bold text-stone-700 mb-1">PIN Rahasia Pesanan (6 Digit)</label>
                                            <input type="password"
                                                   id="chat_pin_order"
                                                   name="chat_pin"
                                                   inputmode="numeric"
                                                   pattern="[0-9]{6}"
                                                   minlength="6"
                                                   maxlength="6"
                                                   placeholder="6 digit PIN yang dibuat saat konfirmasi pesanan"
                                                   required
                                                   class="w-full text-xs sm:text-sm px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl sm:rounded-2xl border border-[#E8B8AE]/70 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] bg-[#FAF2EE]/20 focus:bg-white font-mono tracking-widest transition">
                                            <p class="text-[10px] sm:text-[11px] text-stone-400 mt-0.5">PIN hanya diketahui oleh Anda dan tidak dapat dilihat penjual.</p>
                                        </div>

                                        <div>
                                            <label for="order_message" class="block text-xs sm:text-sm font-bold text-stone-700 mb-1">Pesan (Opsional)</label>
                                            <textarea id="order_message"
                                                      name="message"
                                                      rows="2"
                                                      placeholder="Tanyakan status pesanan atau konfirmasi..."
                                                      class="w-full text-xs sm:text-sm px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl sm:rounded-2xl border border-[#E8B8AE]/70 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] bg-[#FAF2EE]/20 focus:bg-white resize-none transition leading-relaxed"></textarea>
                                        </div>

                                        <button type="submit"
                                                class="w-full py-2.5 sm:py-3 px-4 rounded-xl sm:rounded-2xl bg-[#6B0F1A] hover:bg-[#520912] active:scale-[0.98] text-white font-bold text-xs sm:text-sm shadow-md shadow-[#6B0F1A]/20 transition flex items-center justify-center gap-2 cursor-pointer mt-1">
                                            <span>Verifikasi &amp; Buka Riwayat Obrolan</span>
                                            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </button>
                                    </form>
                                </div>

                                {{-- ================= MODE 2: Tanya-tanya Pra-Order ================= --}}
                                <div id="modeInquirySection" class="{{ $defaultMode === 'inquiry' ? '' : 'hidden' }}">
                                    <div class="text-center mb-4 sm:mb-5">
                                        <div class="w-10 h-10 mx-auto rounded-xl bg-amber-500/10 text-amber-700 flex items-center justify-center mb-2 shadow-2xs">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                        </div>
                                        <h2 class="text-base sm:text-lg font-bold text-stone-800">Mulai Obrolan Baru (Pra-Order)</h2>
                                        <p class="text-xs text-stone-500 mt-0.5 max-w-sm mx-auto leading-relaxed">
                                            Belum memesan? Silakan tanya varian kue, ketersediaan stok, atau harga kustom kepada penjual kami.
                                        </p>
                                    </div>

                                    <form action="{{ route('customer.chat.inquiry') }}" method="POST" class="space-y-3.5">
                                        @csrf
                                        <div>
                                            <label for="customer_name_inquiry" class="block text-xs sm:text-sm font-bold text-stone-700 mb-1">Nama Anda</label>
                                            <input type="text"
                                                   id="customer_name_inquiry"
                                                   name="customer_name"
                                                   value="{{ old('customer_name', $presetName) }}"
                                                   placeholder="Contoh: Rian Pratama"
                                                   required
                                                   class="w-full text-xs sm:text-sm px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl sm:rounded-2xl border border-[#E8B8AE]/70 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] bg-[#FAF2EE]/20 focus:bg-white transition">
                                        </div>

                                        <div>
                                            <label for="customer_phone_inquiry" class="block text-xs sm:text-sm font-bold text-stone-700 mb-1">Nomor WhatsApp / HP</label>
                                            <input type="tel"
                                                   id="customer_phone_inquiry"
                                                   name="customer_phone"
                                                   value="{{ old('customer_phone', $presetPhone) }}"
                                                   placeholder="Contoh: 08123456789"
                                                   required
                                                   class="w-full text-xs sm:text-sm px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl sm:rounded-2xl border border-[#E8B8AE]/70 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] bg-[#FAF2EE]/20 focus:bg-white font-mono transition">
                                            <p class="text-[10px] sm:text-[11px] text-stone-400 mt-0.5">Sesi baru ini tidak membuka riwayat pesanan lama Anda.</p>
                                        </div>

                                        <div>
                                            <label for="inquiry_message" class="block text-xs sm:text-sm font-bold text-stone-700 mb-1">Pertanyaan Anda</label>
                                            <textarea id="inquiry_message"
                                                      name="message"
                                                      rows="2"
                                                      placeholder="Halo, apakah kue bolu marmer bisa dikirim besok siang?"
                                                      required
                                                      class="w-full text-xs sm:text-sm px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl sm:rounded-2xl border border-[#E8B8AE]/70 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] bg-[#FAF2EE]/20 focus:bg-white resize-none transition leading-relaxed"></textarea>
                                        </div>

                                        <button type="submit"
                                                class="w-full py-2.5 sm:py-3 px-4 rounded-xl sm:rounded-2xl bg-[#6B0F1A] hover:bg-[#520912] active:scale-[0.98] text-white font-bold text-xs sm:text-sm shadow-md shadow-[#6B0F1A]/20 transition flex items-center justify-center gap-2 cursor-pointer mt-1">
                                            <span>Kirim Pertanyaan</span>
                                            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function switchChatMode(mode) {
                        const orderSec = document.getElementById('modeOrderSection');
                        const inquirySec = document.getElementById('modeInquirySection');
                        const tabOrder = document.getElementById('tabOrderBtn');
                        const tabInquiry = document.getElementById('tabInquiryBtn');

                        const activeClasses = ['bg-white', 'text-[#6B0F1A]', 'shadow-md', 'font-bold', 'border', 'border-[#E8B8AE]'];
                        const inactiveClasses = ['text-stone-600', 'hover:text-[#6B0F1A]', 'hover:bg-white/60', 'font-medium'];

                        if (mode === 'order') {
                            orderSec.classList.remove('hidden');
                            inquirySec.classList.add('hidden');
                            tabOrder.classList.add(...activeClasses);
                            tabOrder.classList.remove(...inactiveClasses);
                            tabInquiry.classList.remove(...activeClasses);
                            tabInquiry.classList.add(...inactiveClasses);
                        } else {
                            orderSec.classList.add('hidden');
                            inquirySec.classList.remove('hidden');
                            tabInquiry.classList.add(...activeClasses);
                            tabInquiry.classList.remove(...inactiveClasses);
                            tabOrder.classList.remove(...activeClasses);
                            tabOrder.classList.add(...inactiveClasses);
                        }
                    }
                </script>

            @else
                {{-- Area Pesan Chat (Sesi Terverifikasi) --}}
                <div id="chatMessagesContainer" class="flex-1 overflow-y-auto p-3.5 sm:p-5 md:p-6 lg:p-8 space-y-3.5 sm:space-y-4 bg-[#FAF2EE]/30 chat-scroll">
                    {{-- Banner Status Sesi & Tautan Rahasia --}}
                    @if ($thread->order_code)
                        <div class="mx-auto max-w-sm sm:max-w-md text-center my-1 sm:my-2">
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-[10px] sm:text-xs font-semibold bg-emerald-50 text-emerald-900 border border-emerald-300 shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-emerald-700 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span>Terverifikasi Pesanan:</span>
                                <strong class="font-mono">{{ $thread->order_code }}</strong>
                            </span>
                        </div>
                    @else
                        <div class="mx-auto max-w-sm sm:max-w-md text-center my-1 sm:my-2">
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-[10px] sm:text-xs font-medium bg-amber-50 text-amber-900 border border-amber-300/70 shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-amber-700 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <span>Sesi Tanya-Tanya Pra-Order</span>
                            </span>
                        </div>
                    @endif

                    {{-- Kotak Info Tautan Rahasia --}}
                    @if ($secretChatUrl)
                        <div id="secretTokenBanner" class="mx-auto max-w-xl bg-white border border-[#F4E1D9] rounded-2xl p-3 sm:p-3.5 shadow-2xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2.5">
                            <div class="flex items-start gap-2 min-w-0">
                                <svg class="w-4 h-4 text-[#6B0F1A] shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                                <div class="text-[11px] leading-tight text-stone-600">
                                    <span class="font-bold text-stone-800 block">Tautan Rahasia Obrolan Anda</span>
                                    <span>Simpan tautan ini untuk membuka percakapan ini lagi kapan saja tanpa login.</span>
                                </div>
                            </div>
                            <button type="button"
                                    id="btnBannerCopyLink"
                                    data-url="{{ $secretChatUrl }}"
                                    class="w-full sm:w-auto px-3 py-1.5 rounded-xl bg-[#6B0F1A] hover:bg-[#520912] active:scale-95 text-white text-[11px] font-bold transition flex items-center justify-center gap-1.5 shrink-0 cursor-pointer shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <span id="btnBannerCopyText">Salin Tautan</span>
                            </button>
                        </div>
                    @endif

                    <div id="messagesList" class="space-y-3 sm:space-y-4 max-w-5xl mx-auto w-full">
                        @foreach ($thread->messages as $msg)
                            @php
                                $isCustomer = $msg->sender_type === 'customer';
                            @endphp
                            <div id="msg-{{ $msg->id }}" data-message-id="{{ $msg->id }}" class="flex flex-col {{ $isCustomer ? 'items-end' : 'items-start' }}">
                                <div class="max-w-[88%] sm:max-w-[78%] md:max-w-[68%] lg:max-w-[58%] rounded-2xl px-3.5 sm:px-4.5 py-2.5 sm:py-3 text-xs sm:text-sm shadow-2xs break-words {{ $isCustomer ? 'bg-[#6B0F1A] text-white rounded-br-xs' : 'bg-white text-stone-800 border border-[#F4E1D9] rounded-bl-xs' }}">
                                    @if (! $isCustomer)
                                        <div class="flex items-center gap-1.5 mb-1.5 pb-1 border-b border-stone-100">
                                            <span class="font-bold text-[11px] sm:text-xs text-[#6B0F1A]">Toko Kue Nak Mbarep</span>
                                            <span class="text-[9px] sm:text-[10px] px-1.5 py-0.5 bg-rose-50 text-[#6B0F1A] font-bold rounded">Penjual</span>
                                        </div>
                                    @endif
                                    @if ($msg->attachment_path)
                                        <a href="{{ route('customer.chat.attachment', $msg, false) }}" target="_blank" rel="noopener">
                                            <img src="{{ route('customer.chat.attachment', $msg, false) }}"
                                                 alt="Lampiran chat"
                                                 loading="lazy"
                                                 class="mb-2 max-h-60 sm:max-h-80 lg:max-h-96 max-w-full rounded-xl object-contain border border-stone-100">
                                        </a>
                                    @endif
                                    @if (filled($msg->message))
                                        <p class="whitespace-pre-wrap leading-relaxed">{{ $msg->message }}</p>
                                    @endif
                                    <div class="text-[10px] sm:text-[11px] mt-1 flex items-center justify-end gap-1.5 {{ $isCustomer ? 'text-rose-200' : 'text-stone-400' }}">
                                        <span>{{ $msg->created_at->format('H:i') }}</span>
                                        @if ($isCustomer)
                                            @if ($msg->is_read)
                                                <span class="read-status inline-flex items-center gap-0.5 text-sky-300 font-medium" data-is-read="1">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2 12l5 5L17 7M9 12l5 5L22 7"/>
                                                    </svg>
                                                    <span>Dibaca</span>
                                                </span>
                                            @else
                                                <span class="read-status inline-flex items-center gap-0.5 text-rose-200/80" data-is-read="0">
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>Terkirim</span>
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Input Pengiriman Pesan --}}
                <div class="p-2.5 sm:p-3.5 md:p-4 lg:p-5 lg:px-8 bg-white border-t border-[#F4E1D9] shrink-0">
                    <form id="sendMessageForm" class="flex items-end gap-1.5 sm:gap-3 max-w-5xl mx-auto w-full" novalidate>
                        @csrf
                        <textarea id="messageInput"
                                  name="message"
                                  rows="1"
                                  placeholder="Ketik pesan Anda di sini..."
                                  required
                                  class="flex-1 text-xs sm:text-sm px-3.5 sm:px-4 py-2 sm:py-2.5 lg:py-3 rounded-xl sm:rounded-2xl border border-[#E8B8AE]/70 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] bg-stone-50/40 focus:bg-white resize-none min-h-[40px] sm:min-h-[44px] lg:min-h-[48px] max-h-32 transition leading-normal"></textarea>

                        <label for="customerImageInput"
                               title="Pilih foto"
                               class="cursor-pointer p-2 sm:p-2.5 rounded-xl sm:rounded-2xl border border-[#E8B8AE]/70 text-stone-600 hover:text-[#6B0F1A] hover:bg-rose-50/40 active:scale-95 shrink-0 transition flex items-center justify-center h-[40px] w-[40px] sm:h-[44px] sm:w-[44px] lg:h-[48px] lg:w-[48px]">
                            <span class="sr-only">Pilih foto</span>
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4-4a2 2 0 012.8 0l3.2 3.2m-2-2 1.2-1.2a2 2 0 012.8 0L20 16m-2-8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </label>
                        <input id="customerImageInput"
                               name="image"
                               type="file"
                               accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                               class="hidden">

                        <button type="submit"
                                id="sendBtn"
                                class="p-2 sm:px-5 sm:py-2.5 lg:py-3 rounded-xl sm:rounded-2xl bg-[#6B0F1A] hover:bg-[#520912] active:scale-95 text-white font-semibold text-xs sm:text-sm transition shadow-sm shadow-[#6B0F1A]/20 flex items-center justify-center gap-1.5 shrink-0 h-[40px] sm:h-[44px] lg:h-[48px] min-w-[40px] sm:min-w-[88px] cursor-pointer">
                            <span class="hidden sm:inline">Kirim</span>
                            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </form>
                    <div id="customerImagePreview" class="hidden mt-2.5 items-center gap-2.5 p-2 rounded-xl bg-[#FAF2EE] border border-[#F4E1D9] w-fit max-w-5xl mx-auto">
                        <img id="customerImagePreviewImage" alt="Preview lampiran" class="max-h-16 max-w-20 rounded-lg object-contain border border-stone-200 bg-white">
                        <div class="flex flex-col">
                            <span class="text-[11px] font-medium text-stone-600">Foto terpilih</span>
                            <button id="customerRemoveImage" type="button" class="text-xs font-semibold text-rose-700 hover:text-rose-900 text-left transition underline cursor-pointer">Hapus foto</button>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </main>

    @if ($thread)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const messagesContainer = document.getElementById('chatMessagesContainer');
            const messagesList = document.getElementById('messagesList');
            const sendForm = document.getElementById('sendMessageForm');
            const messageInput = document.getElementById('messageInput');
            const sendBtn = document.getElementById('sendBtn');
            const imageInput = document.getElementById('customerImageInput');
            const imagePreview = document.getElementById('customerImagePreview');
            const imagePreviewImage = document.getElementById('customerImagePreviewImage');
            const removeImageButton = document.getElementById('customerRemoveImage');
            const sendUrl = "{{ route('customer.chat.send', [], false) }}";
            const pollUrl = "{{ route('customer.chat.poll', [], false) }}";

            if (sendForm.dataset.handlersAttached === 'true') return;
            sendForm.dataset.handlersAttached = 'true';

            // Fitur Salin Tautan Rahasia ke Clipboard
            function setupCopyButton(btnId, textId) {
                const btn = document.getElementById(btnId);
                const txt = document.getElementById(textId);
                if (!btn) return;

                btn.addEventListener('click', async function () {
                    const url = btn.dataset.url || window.location.href;
                    try {
                        if (navigator.clipboard && window.isSecureContext) {
                            await navigator.clipboard.writeText(url);
                        } else {
                            const tempInput = document.createElement('input');
                            tempInput.value = url;
                            document.body.appendChild(tempInput);
                            tempInput.select();
                            document.execCommand('copy');
                            document.body.removeChild(tempInput);
                        }
                        if (txt) {
                            const original = txt.textContent;
                            txt.textContent = 'Tersalin!';
                            setTimeout(() => { txt.textContent = original; }, 2000);
                        }
                    } catch (e) {
                        prompt('Salin tautan rahasia berikut:', url);
                    }
                });
            }

            setupCopyButton('btnCopySecretLink', 'btnCopyText');
            setupCopyButton('btnBannerCopyLink', 'btnBannerCopyText');

            let isSending = false;
            let activePoll = null;
            let imagePreviewUrl = null;

            function clearImagePreview() {
                if (imagePreviewUrl) {
                    URL.revokeObjectURL(imagePreviewUrl);
                    imagePreviewUrl = null;
                }

                imageInput.value = '';
                imagePreviewImage.removeAttribute('src');
                imagePreview.classList.add('hidden');
                imagePreview.classList.remove('flex');
            }

            imageInput.addEventListener('change', function() {
                const image = this.files?.[0];
                if (!image) {
                    clearImagePreview();
                    return;
                }

                if (imagePreviewUrl) {
                    URL.revokeObjectURL(imagePreviewUrl);
                }
                imagePreviewUrl = URL.createObjectURL(image);
                imagePreviewImage.src = imagePreviewUrl;
                imagePreview.classList.remove('hidden');
                imagePreview.classList.add('flex');
            });

            removeImageButton.addEventListener('click', clearImagePreview);

            function scrollToBottom() {
                if (messagesContainer) {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            }
            scrollToBottom();

            // Auto expand textarea
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            // Enter to send
            messageInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if (!isSending) {
                        sendForm.requestSubmit();
                    }
                }
            });

            // Kirim Pesan AJAX
            sendForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                if (isSending) return;

                const text = messageInput.value.trim();
                if (!text && imageInput.files.length === 0) return;

                const csrfToken = sendForm.querySelector('input[name="_token"]')?.value;
                if (!csrfToken) {
                    console.error('Gagal mengirim pesan: token CSRF tidak ditemukan.');
                    return;
                }

                const formData = new FormData(sendForm);
                formData.set('message', text);
                isSending = true;
                sendBtn.disabled = true;
                messageInput.disabled = true;
                imageInput.disabled = true;

                try {
                    if (activePoll) {
                        await activePoll;
                    }

                    const response = await fetch(sendUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const contentType = response.headers.get('content-type') || '';
                    const data = contentType.includes('application/json')
                        ? await response.json()
                        : null;

                    if (response.status === 403) {
                        alert('Sesi obrolan Anda telah berakhir. Halaman akan dimuat ulang.');
                        window.location.reload();
                        return;
                    }

                    if (!response.ok) {
                        const validationMessage = data?.errors?.message?.[0];
                        const imageValidationMessage = data?.errors?.image?.[0];
                        throw new Error(validationMessage || imageValidationMessage || data?.error || data?.message || `Pengiriman gagal (HTTP ${response.status}).`);
                    }

                    if (!data?.success || !data.message) {
                        throw new Error(`Respons server tidak valid (HTTP ${response.status}).`);
                    }

                    appendMessage(data.message);
                    scrollToBottom();
                    messageInput.value = '';
                    messageInput.style.height = 'auto';
                    clearImagePreview();
                } catch (err) {
                    console.error('Gagal mengirim pesan:', err);
                } finally {
                    isSending = false;
                    sendBtn.disabled = false;
                    messageInput.disabled = false;
                    imageInput.disabled = false;
                    messageInput.focus();
                }
            });

            function renderReadStatusHtml(isRead) {
                if (isRead) {
                    return `<span class="read-status inline-flex items-center gap-0.5 text-sky-300 font-medium" data-is-read="1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2 12l5 5L17 7M9 12l5 5L22 7"/>
                        </svg>
                        <span>Dibaca</span>
                    </span>`;
                }

                return `<span class="read-status inline-flex items-center gap-0.5 text-rose-200/80" data-is-read="0">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Terkirim</span>
                </span>`;
            }

            function appendMessage(msg) {
                if (document.getElementById('msg-' + msg.id)) return;

                const wrapper = document.createElement('div');
                wrapper.id = 'msg-' + msg.id;
                wrapper.dataset.messageId = String(msg.id);
                wrapper.className = 'flex flex-col ' + (msg.is_mine ? 'items-end' : 'items-start');

                const bubble = document.createElement('div');
                bubble.className = 'max-w-[88%] sm:max-w-[78%] md:max-w-[68%] lg:max-w-[58%] rounded-2xl px-3.5 sm:px-4.5 py-2.5 sm:py-3 text-xs sm:text-sm shadow-2xs break-words ' +
                    (msg.is_mine ? 'bg-[#6B0F1A] text-white rounded-br-xs' : 'bg-white text-stone-800 border border-[#F4E1D9] rounded-bl-xs');

                let html = '';
                if (!msg.is_mine) {
                    html += `<div class="flex items-center gap-1.5 mb-1.5 pb-1 border-b border-stone-100">
                        <span class="font-bold text-[11px] sm:text-xs text-[#6B0F1A]">Toko Kue Nak Mbarep</span>
                        <span class="text-[9px] sm:text-[10px] px-1.5 py-0.5 bg-rose-50 text-[#6B0F1A] font-bold rounded">Penjual</span>
                    </div>`;
                }
                if (msg.attachment_url) {
                    html += `<a href="${msg.attachment_url}" target="_blank" rel="noopener">
                                <img src="${msg.attachment_url}" alt="Lampiran chat" loading="lazy" class="mb-2 max-h-60 sm:max-h-80 lg:max-h-96 max-w-full rounded-xl object-contain border border-stone-100">
                             </a>`;
                }
                if (msg.message) {
                    const sanitizedMsg = msg.message.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                    html += `<p class="whitespace-pre-wrap leading-relaxed">${sanitizedMsg}</p>`;
                }
                const readStatusHtml = msg.is_mine ? renderReadStatusHtml(Boolean(msg.is_read)) : '';
                html += `<div class="text-[10px] sm:text-[11px] mt-1 flex items-center justify-end gap-1.5 ${msg.is_mine ? 'text-rose-200' : 'text-stone-400'}"><span>${msg.created_at}</span>${readStatusHtml}</div>`;

                bubble.innerHTML = html;
                wrapper.appendChild(bubble);
                const nextMessage = Array.from(messagesList.children).find(
                    messageElement => Number(messageElement.dataset.messageId) > Number(msg.id)
                );
                messagesList.insertBefore(wrapper, nextMessage || null);
            }

            // Polling interval 3.5 detik untuk pesan baru
            async function pollMessages() {
                if (isSending || activePoll) return;

                const pollRequest = (async function() {
                    try {
                        const response = await fetch(pollUrl, {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (!response.ok) {
                            throw new Error(`Polling pesan gagal (HTTP ${response.status}).`);
                        }

                        const data = await response.json();
                        let hasNew = false;
                        if (data.messages && data.messages.length > 0) {
                            data.messages.forEach(msg => {
                                const existingEl = document.getElementById('msg-' + msg.id);
                                if (!existingEl) {
                                    appendMessage(msg);
                                    hasNew = true;
                                } else if (msg.is_mine && msg.is_read) {
                                    const statusEl = existingEl.querySelector('.read-status');
                                    if (statusEl && statusEl.dataset.isRead !== '1') {
                                        statusEl.outerHTML = renderReadStatusHtml(true);
                                    }
                                }
                            });
                            if (hasNew) {
                                scrollToBottom();
                            }
                        }
                    } catch (e) {
                        console.warn('Polling customer chat gagal; akan dicoba kembali:', e);
                    }
                })();

                activePoll = pollRequest;
                try {
                    await pollRequest;
                } finally {
                    if (activePoll === pollRequest) {
                        activePoll = null;
                    }
                }
            }

            setInterval(pollMessages, 3500);
        });
    </script>
    @endif

</body>
</html>
