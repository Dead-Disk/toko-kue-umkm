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
        .chat-scroll::-webkit-scrollbar { width: 5px; }
        .chat-scroll::-webkit-scrollbar-track { background: #f1f1f1; }
        .chat-scroll::-webkit-scrollbar-thumb { background: #d6b5af; border-radius: 4px; }
    </style>
</head>
<body class="bg-[#FAF2EE] text-stone-800 min-h-screen antialiased flex flex-col font-sans">

    {{-- Top Navigation Bar --}}
    @include('customer.partials.navbar')

    <main class="flex-1 flex items-stretch justify-center px-2 sm:px-4 py-4 sm:py-6">
        <div class="w-full max-w-3xl bg-white rounded-2xl sm:rounded-3xl shadow-xl border border-stone-200/80 flex flex-col h-[calc(100vh-6.5rem)] min-h-[520px] overflow-hidden">
            
            {{-- Header Chat --}}
            <div class="bg-[#6B0F1A] text-white px-4 py-3.5 sm:px-6 sm:py-4 flex items-center justify-between shadow-xs shrink-0">
                <div class="flex items-center gap-3">
                    <a href="{{ route('customer.index') }}" class="p-1 rounded-lg hover:bg-white/10 text-stone-200 hover:text-white transition" title="Kembali ke Beranda">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <div class="relative">
                        <div class="w-10 h-10 rounded-full bg-white/10 border border-white/20 flex items-center justify-center font-bold text-white text-sm">
                            🍰
                        </div>
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-400 border-2 border-[#6B0F1A] rounded-full"></span>
                    </div>
                    <div>
                        <h1 class="text-sm sm:text-base font-bold leading-tight flex items-center gap-2">
                            <span>Layanan Chat Nak Mbarep</span>
                        </h1>
                        <p class="text-[11px] text-stone-200/80 flex items-center gap-1.5">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            <span>Admin &amp; Staf siap melayani</span>
                        </p>
                    </div>
                </div>

                @if ($thread)
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <span class="text-xs font-semibold block text-stone-100">{{ $thread->customer_name }}</span>
                            <span class="text-[10px] text-stone-300 font-mono">{{ $thread->customer_phone }}</span>
                        </div>
                        <form action="{{ route('customer.chat.end-session') }}" method="POST">
                            @csrf
                            <button type="submit" title="Akhiri sesi chat di browser ini" class="p-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-xs text-rose-200 hover:text-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            @if (! $thread)
                {{-- Form Gerbang Keamanan: Pilihan 2 Mode (Verifikasi Pesanan vs Tanya Pra-Order) --}}
                <div class="flex-1 overflow-y-auto p-4 sm:p-8 flex items-center justify-center bg-stone-50/50">
                    <div class="w-full max-w-lg bg-white rounded-2xl border border-stone-200 p-5 sm:p-7 shadow-sm">
                        
                        {{-- Tab Switcher Mode 1 vs Mode 2 --}}
                        <div class="flex rounded-xl bg-stone-100 p-1 mb-6 text-xs font-semibold">
                            <button type="button"
                                    id="tabOrderBtn"
                                    onclick="switchChatMode('order')"
                                    class="flex-1 py-2 rounded-lg text-center transition-all duration-200 {{ $defaultMode === 'order' ? 'bg-white text-[#6B0F1A] shadow-xs font-bold' : 'text-stone-600 hover:text-stone-900' }}">
                                📦 Diskusi Pesanan
                            </button>
                            <button type="button"
                                    id="tabInquiryBtn"
                                    onclick="switchChatMode('inquiry')"
                                    class="flex-1 py-2 rounded-lg text-center transition-all duration-200 {{ $defaultMode === 'inquiry' ? 'bg-white text-[#6B0F1A] shadow-xs font-bold' : 'text-stone-600 hover:text-stone-900' }}">
                                💬 Tanya-Tanya Baru
                            </button>
                        </div>

                        {{-- Alert Error --}}
                        @if ($errors->any())
                            <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-2xs">
                                <div class="font-bold mb-1 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span>Verifikasi Gagal:</span>
                                </div>
                                <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- ================= MODE 1: Verifikasi Nomor HP + Kode Pesanan ================= --}}
                        <div id="modeOrderSection" class="{{ $defaultMode === 'order' ? '' : 'hidden' }}">
                            <div class="text-center mb-5">
                                <div class="w-10 h-10 mx-auto rounded-xl bg-rose-50 text-[#6B0F1A] flex items-center justify-center mb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <h2 class="text-base font-bold text-stone-800">Verifikasi Riwayat Pesanan</h2>
                                <p class="text-[11px] text-stone-500 mt-0.5">
                                    Untuk melindungi privasi obrolan Anda, masukkan pasangan <strong>Nomor WhatsApp</strong> dan <strong>Kode Pesanan</strong> yang sah.
                                </p>
                            </div>

                            <form action="{{ route('customer.chat.verify-order') }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="order_code" class="block text-xs font-bold text-stone-700 mb-1">Kode Pesanan</label>
                                    <input type="text"
                                           id="order_code"
                                           name="order_code"
                                           value="{{ old('order_code', $presetOrder) }}"
                                           placeholder="Contoh: ORD-20261126-8784"
                                           required
                                           class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] font-mono uppercase">
                                    <p class="text-[10px] text-stone-400 mt-1">Dapat ditemukan pada nota/halaman konfirmasi pesanan.</p>
                                </div>

                                <div>
                                    <label for="customer_phone_order" class="block text-xs font-bold text-stone-700 mb-1">Nomor WhatsApp Pemesan</label>
                                    <input type="tel"
                                           id="customer_phone_order"
                                           name="customer_phone"
                                           value="{{ old('customer_phone', $presetPhone) }}"
                                           placeholder="Contoh: 08123456789 atau 628..."
                                           required
                                           class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] font-mono">
                                    <p class="text-[10px] text-stone-400 mt-1">Wajib sama dengan nomor yang digunakan saat memesan.</p>
                                </div>

                                <div>
                                    <label for="order_message" class="block text-xs font-bold text-stone-700 mb-1">Pesan (Opsional)</label>
                                    <textarea id="order_message"
                                              name="message"
                                              rows="2"
                                              placeholder="Tanyakan status pesanan atau konfirmasi..."
                                              class="w-full text-xs px-3.5 py-2 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] resize-none"></textarea>
                                </div>

                                <button type="submit"
                                        class="w-full py-2.5 px-4 rounded-xl bg-[#6B0F1A] hover:bg-[#520912] active:scale-98 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2">
                                    <span>Verifikasi &amp; Buka Riwayat Obrolan</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </form>
                        </div>

                        {{-- ================= MODE 2: Tanya-tanya Pra-Order ================= --}}
                        <div id="modeInquirySection" class="{{ $defaultMode === 'inquiry' ? '' : 'hidden' }}">
                            <div class="text-center mb-5">
                                <div class="w-10 h-10 mx-auto rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center mb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                </div>
                                <h2 class="text-base font-bold text-stone-800">Mulai Obrolan Baru (Pra-Order)</h2>
                                <p class="text-[11px] text-stone-500 mt-0.5">
                                    Belum memesan? Silakan tanya varian kue, ketersediaan stok, atau harga kustom kepada penjual kami.
                                </p>
                            </div>

                            <form action="{{ route('customer.chat.inquiry') }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="customer_name_inquiry" class="block text-xs font-bold text-stone-700 mb-1">Nama Anda</label>
                                    <input type="text"
                                           id="customer_name_inquiry"
                                           name="customer_name"
                                           value="{{ old('customer_name', $presetName) }}"
                                           placeholder="Contoh: Rian Pratama"
                                           required
                                           class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A]">
                                </div>

                                <div>
                                    <label for="customer_phone_inquiry" class="block text-xs font-bold text-stone-700 mb-1">Nomor WhatsApp / HP</label>
                                    <input type="tel"
                                           id="customer_phone_inquiry"
                                           name="customer_phone"
                                           value="{{ old('customer_phone', $presetPhone) }}"
                                           placeholder="Contoh: 08123456789"
                                           required
                                           class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] font-mono">
                                    <p class="text-[10px] text-stone-400 mt-1">Sesi baru ini tidak membuka riwayat pesanan lama Anda.</p>
                                </div>

                                <div>
                                    <label for="inquiry_message" class="block text-xs font-bold text-stone-700 mb-1">Pertanyaan Anda</label>
                                    <textarea id="inquiry_message"
                                              name="message"
                                              rows="2"
                                              placeholder="Halo, apakah kue bolu marmer bisa dikirim besok siang?"
                                              required
                                              class="w-full text-xs px-3.5 py-2 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] resize-none"></textarea>
                                </div>

                                <button type="submit"
                                        class="w-full py-2.5 px-4 rounded-xl bg-[#6B0F1A] hover:bg-[#520912] active:scale-98 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2">
                                    <span>Kirim Pertanyaan</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

                <script>
                    function switchChatMode(mode) {
                        const orderSec = document.getElementById('modeOrderSection');
                        const inquirySec = document.getElementById('modeInquirySection');
                        const tabOrder = document.getElementById('tabOrderBtn');
                        const tabInquiry = document.getElementById('tabInquiryBtn');

                        if (mode === 'order') {
                            orderSec.classList.remove('hidden');
                            inquirySec.classList.add('hidden');
                            tabOrder.className = "flex-1 py-2 rounded-lg text-center transition-all duration-200 bg-white text-[#6B0F1A] shadow-xs font-bold";
                            tabInquiry.className = "flex-1 py-2 rounded-lg text-center transition-all duration-200 text-stone-600 hover:text-stone-900";
                        } else {
                            orderSec.classList.add('hidden');
                            inquirySec.classList.remove('hidden');
                            tabInquiry.className = "flex-1 py-2 rounded-lg text-center transition-all duration-200 bg-white text-[#6B0F1A] shadow-xs font-bold";
                            tabOrder.className = "flex-1 py-2 rounded-lg text-center transition-all duration-200 text-stone-600 hover:text-stone-900";
                        }
                    }
                </script>

            @else
                {{-- Area Pesan Chat (Sesi Terverifikasi) --}}
                <div id="chatMessagesContainer" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-3 bg-[#FAF2EE]/50 chat-scroll">
                    {{-- Banner Status Sesi --}}
                    @if ($thread->order_code)
                        <div class="mx-auto max-w-sm text-center my-2">
                            <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100/90 text-emerald-900 border border-emerald-300/80 shadow-2xs">
                                <span>🔒 Terverifikasi Pesanan:</span>
                                <strong class="font-mono">{{ $thread->order_code }}</strong>
                            </span>
                        </div>
                    @else
                        <div class="mx-auto max-w-sm text-center my-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-medium bg-amber-100/80 text-amber-900 border border-amber-200/60 shadow-2xs">
                                <span>💬 Sesi Tanya-Tanya Pra-Order</span>
                            </span>
                        </div>
                    @endif

                    <div id="messagesList" class="space-y-3">
                        @foreach ($thread->messages as $msg)
                            @php
                                $isCustomer = $msg->sender_type === 'customer';
                            @endphp
                            <div id="msg-{{ $msg->id }}"
                                 data-message-id="{{ $msg->id }}"
                                 class="flex flex-col {{ $isCustomer ? 'items-end' : 'items-start' }}">
                                <div class="max-w-[85%] sm:max-w-[75%] rounded-2xl px-4 py-2.5 text-xs sm:text-sm shadow-2xs break-words {{ $isCustomer ? 'bg-[#6B0F1A] text-white rounded-br-xs' : 'bg-white text-stone-800 border border-stone-200 rounded-bl-xs' }}">
                                    @if (! $isCustomer)
                                        <div class="flex items-center gap-1.5 mb-1 pb-1 border-b border-stone-100">
                                            <span class="font-bold text-[11px] text-[#6B0F1A]">Toko Kue Nak Mbarep</span>
                                            <span class="text-[9px] px-1.5 py-0.2 bg-rose-50 text-[#6B0F1A] font-semibold rounded">Penjual</span>
                                        </div>
                                    @endif
                                    @if ($msg->attachment_path)
                                        <a href="{{ route('customer.chat.attachment', $msg, false) }}" target="_blank" rel="noopener">
                                            <img src="{{ route('customer.chat.attachment', $msg, false) }}"
                                                 alt="Lampiran chat"
                                                 loading="lazy"
                                                 class="mb-2 max-h-64 max-w-full rounded-lg object-contain">
                                        </a>
                                    @endif
                                    @if (filled($msg->message))
                                        <p class="whitespace-pre-wrap leading-relaxed">{{ $msg->message }}</p>
                                    @endif
                                    <div class="text-[10px] mt-1 text-right {{ $isCustomer ? 'text-rose-200' : 'text-stone-400' }}">
                                        {{ $msg->created_at->format('H:i') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Input Pengiriman Pesan --}}
                <div class="p-3 sm:p-4 bg-white border-t border-stone-200/80 shrink-0">
                    <form id="sendMessageForm" class="flex items-center gap-2" novalidate>
                        @csrf
                        <textarea id="messageInput"
                                  name="message"
                                  rows="1"
                                  placeholder="Ketik pesan Anda di sini..."
                                  required
                                  class="flex-1 text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] resize-none max-h-28"></textarea>

                        <label for="customerImageInput"
                               title="Pilih foto"
                               class="cursor-pointer p-2.5 rounded-xl border border-stone-200 text-stone-600 hover:bg-stone-50 shrink-0">
                            <span class="sr-only">Pilih foto</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4-4a2 2 0 012.8 0l3.2 3.2m-2-2 1.2-1.2a2 2 0 012.8 0L20 16m-2-8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </label>
                        <input id="customerImageInput"
                               name="image"
                               type="file"
                               accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                               class="hidden">

                        <button type="submit"
                                id="sendBtn"
                                class="p-2.5 sm:px-4 sm:py-2.5 rounded-xl bg-[#6B0F1A] hover:bg-[#520912] active:scale-95 text-white font-semibold text-xs transition shadow-xs flex items-center justify-center gap-1.5 shrink-0">
                            <span class="hidden sm:inline">Kirim</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        </button>
                    </form>
                    <div id="customerImagePreview" class="hidden mt-2 items-start gap-2">
                        <img id="customerImagePreviewImage" alt="Preview lampiran" class="max-h-20 max-w-24 rounded-lg object-contain">
                        <button id="customerRemoveImage" type="button" class="text-xs text-stone-500 hover:text-rose-700">Hapus foto</button>
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

            function appendMessage(msg) {
                if (document.getElementById('msg-' + msg.id)) return;

                const wrapper = document.createElement('div');
                wrapper.id = 'msg-' + msg.id;
                wrapper.dataset.messageId = String(msg.id);
                wrapper.className = 'flex flex-col ' + (msg.is_mine ? 'items-end' : 'items-start');

                const bubble = document.createElement('div');
                bubble.className = 'max-w-[85%] sm:max-w-[75%] rounded-2xl px-4 py-2.5 text-xs sm:text-sm shadow-2xs break-words ' +
                    (msg.is_mine ? 'bg-[#6B0F1A] text-white rounded-br-xs' : 'bg-white text-stone-800 border border-stone-200 rounded-bl-xs');

                let html = '';
                if (!msg.is_mine) {
                    html += `<div class="flex items-center gap-1.5 mb-1 pb-1 border-b border-stone-100">
                        <span class="font-bold text-[11px] text-[#6B0F1A]">Toko Kue Nak Mbarep</span>
                        <span class="text-[9px] px-1.5 py-0.2 bg-rose-50 text-[#6B0F1A] font-semibold rounded">Penjual</span>
                    </div>`;
                }
                if (msg.attachment_url) {
                    html += `<a href="${msg.attachment_url}" target="_blank" rel="noopener">
                                <img src="${msg.attachment_url}" alt="Lampiran chat" loading="lazy" class="mb-2 max-h-64 max-w-full rounded-lg object-contain">
                             </a>`;
                }
                if (msg.message) {
                    const sanitizedMsg = msg.message.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                    html += `<p class="whitespace-pre-wrap leading-relaxed">${sanitizedMsg}</p>`;
                }
                html += `<div class="text-[10px] mt-1 text-right ${msg.is_mine ? 'text-rose-200' : 'text-stone-400'}">${msg.created_at}</div>`;

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
                                if (!document.getElementById('msg-' + msg.id)) {
                                    appendMessage(msg);
                                    hasNew = true;
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
