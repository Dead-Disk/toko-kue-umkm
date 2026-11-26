<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Pembeli - {{ $panel === 'staff' ? 'Staf' : 'Admin' }} Nak Mbarep</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-nav.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
        .chat-scroll::-webkit-scrollbar { width: 5px; }
        .chat-scroll::-webkit-scrollbar-track { background: #f5f5f5; }
        .chat-scroll::-webkit-scrollbar-thumb { background: #d6b5af; border-radius: 4px; }
    </style>
</head>
<body class="bg-[#FAF2EE] text-stone-800 antialiased min-h-screen flex flex-col lg:flex-row">

    <!-- Mobile Topbar (< lg) -->
    <header class="lg:hidden bg-[#4D626C] text-white px-4 py-3 flex items-center justify-between sticky top-0 z-40 shadow-sm">
        <div class="flex items-center gap-2">
            <x-store-logo class="h-6 w-6" />
            <span class="font-bold text-base tracking-tight">Nak Mbarep</span>
            <span class="text-[10px] bg-white/20 px-1.5 py-0.5 rounded font-mono uppercase">{{ $panel }}</span>
        </div>
        <a href="{{ $panel === 'staff' ? route('staff.dashboard') : route('admin.dashboard') }}" class="text-xs text-stone-200 hover:text-white">← Kembali</a>
    </header>

    <!-- Desktop Sidebar (>= lg) -->
    <div class="hidden lg:block shrink-0 sticky top-0 h-screen">
        @if ($panel === 'staff')
            @include('staff.partials.sidebar', ['active' => 'chat'])
        @else
            @include('admin.partials.sidebar', ['active' => 'chat'])
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 min-w-0 p-3 sm:p-5 lg:p-6 flex flex-col h-[calc(100vh-3.5rem)] lg:h-screen overflow-hidden">
        
        <!-- Header Info -->
        <div class="mb-3 flex items-center justify-between shrink-0">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-[#5A121A] tracking-tight flex items-center gap-2">
                    <span>Inbox Chat Pembeli</span>
                    <span id="badgeTotalUnread" class="text-xs px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 font-bold {{ $totalUnread > 0 ? '' : 'hidden' }}">
                        {{ $totalUnread }} Belum Dibaca
                    </span>
                </h1>
                <p class="text-xs text-stone-500">Kelola obrolan langsung dan konsultasi pesanan pembeli toko kue.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Live Polling</span>
                </span>
            </div>
        </div>

        <!-- Chat Workspace Card (2-Kolom: Daftar Thread di Kiri, Percakapan di Kanan) -->
        <div class="flex-1 bg-white rounded-2xl shadow-xs border border-stone-200/80 flex overflow-hidden min-h-0">
            
            <!-- Kolom Kiri: Daftar Thread Chat -->
            <div id="threadSidebar" class="w-full sm:w-80 lg:w-96 border-r border-stone-200 flex flex-col shrink-0 {{ $activeThread ? 'hidden sm:flex' : 'flex' }}">
                <div class="p-3 border-b border-stone-100 bg-stone-50/60 flex items-center justify-between">
                    <span class="text-xs font-bold text-stone-700 uppercase tracking-wider">Semua Percakapan</span>
                    <span class="text-[11px] text-stone-400 font-mono">{{ $threads->count() }} Kontak</span>
                </div>

                <div id="threadListContainer" class="flex-1 overflow-y-auto divide-y divide-stone-100 chat-scroll">
                    @forelse ($threads as $th)
                        @php
                            $isActive = $activeThread && $activeThread->id === $th->id;
                        @endphp
                        <a href="{{ route($panel === 'staff' ? 'staff.chat.index' : 'admin.chat.index', ['thread_id' => $th->id]) }}"
                           class="thread-item block p-3.5 hover:bg-stone-50/80 transition-colors {{ $isActive ? 'bg-amber-50/50 border-l-4 border-[#6B0F1A]' : '' }}"
                           data-thread-id="{{ $th->id }}">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1.5">
                                        <p class="text-xs font-bold text-stone-900 truncate">{{ $th->customer_name }}</p>
                                        @if ($th->order_code)
                                            <span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 font-mono font-semibold shrink-0">
                                                #{{ $th->order_code }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-stone-400 font-mono mt-0.5">{{ $th->customer_phone }}</p>
                                    <p class="text-xs text-stone-500 truncate mt-1 leading-tight thread-last-message">
                                        {{ $th->latestMessage?->message ?? 'Belum ada pesan' }}
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-[10px] text-stone-400 thread-time">
                                        {{ $th->last_message_at ? $th->last_message_at->format('H:i') : '' }}
                                    </span>
                                    <div class="mt-1">
                                        <span class="thread-badge inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-[#6B0F1A] text-white {{ $th->unread_admin_count > 0 ? '' : 'hidden' }}">
                                            {{ $th->unread_admin_count }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-stone-400 text-xs">
                            <svg class="w-8 h-8 mx-auto mb-2 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span>Belum ada pesan dari pembeli.</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Kolom Kanan: Ruang Percakapan Aktif -->
            <div class="flex-1 flex flex-col min-w-0 {{ $activeThread ? 'flex' : 'hidden sm:flex' }}">
                @if ($activeThread)
                    <!-- Top Thread Active Header -->
                    <div class="p-3 sm:px-5 sm:py-3.5 border-b border-stone-200 bg-white flex items-center justify-between shrink-0 shadow-2xs">
                        <div class="flex items-center gap-3 min-w-0">
                            <!-- Mobile Back to thread list -->
                            <a href="{{ route($panel === 'staff' ? 'staff.chat.index' : 'admin.chat.index') }}" class="sm:hidden p-1.5 rounded-lg text-stone-500 hover:bg-stone-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </a>
                            <div class="w-9 h-9 rounded-full bg-[#6B0F1A] text-white flex items-center justify-center font-bold text-xs shrink-0">
                                {{ strtoupper(substr($activeThread->customer_name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h2 class="text-xs sm:text-sm font-bold text-stone-900 leading-tight truncate">
                                    {{ $activeThread->customer_name }}
                                </h2>
                                <div class="flex items-center gap-2 text-[11px] text-stone-500 font-mono">
                                    <span>{{ $activeThread->customer_phone }}</span>
                                    @if ($activeThread->order_code)
                                        <span class="text-[#6B0F1A] font-semibold">Terkait Pesanan: #{{ $activeThread->order_code }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Info Role Penjawab Saat Ini -->
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ auth()->user()->isAdmin() ? 'bg-purple-100 text-purple-800' : 'bg-sky-100 text-sky-800' }}">
                                Menjawab sebagai: {{ auth()->user()->isAdmin() ? 'Admin' : 'Staf - '.auth()->user()->name }}
                            </span>
                        </div>
                    </div>

                    <!-- Area Pesan -->
                    <div id="adminChatMessagesContainer" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3 bg-[#FAF2EE]/30 chat-scroll">
                        <div id="adminMessagesList" class="space-y-3">
                            @foreach ($activeThread->messages as $msg)
                                @php
                                    $isCustomer = $msg->sender_type === 'customer';
                                    $isStaff = $msg->sender_type === 'staff';
                                    $isAdmin = $msg->sender_type === 'admin';
                                @endphp
                                <div id="admin-msg-{{ $msg->id }}"
                                     data-message-id="{{ $msg->id }}"
                                     class="flex flex-col {{ $isCustomer ? 'items-start' : 'items-end' }}">
                                    <div class="max-w-[85%] sm:max-w-[75%] rounded-2xl px-4 py-2.5 text-xs sm:text-sm shadow-2xs break-words {{ $isCustomer ? 'bg-white text-stone-800 border border-stone-200 rounded-bl-xs' : 'bg-[#6B0F1A] text-white rounded-br-xs' }}">
                                        {{-- Header Pengirim --}}
                                        <div class="flex items-center gap-1.5 mb-1 pb-1 border-b {{ $isCustomer ? 'border-stone-100' : 'border-white/10' }}">
                                            @if ($isCustomer)
                                                <span class="font-bold text-[11px] text-stone-900">{{ $msg->sender_name }}</span>
                                                <span class="text-[9px] px-1.5 py-0.2 bg-stone-100 text-stone-600 font-medium rounded">Pembeli</span>
                                            @else
                                                <span class="font-bold text-[11px] text-amber-200">{{ $msg->sender_name }}</span>
                                                @if ($isAdmin)
                                                    <span class="text-[9px] px-1.5 py-0.2 bg-purple-900/60 text-purple-200 font-semibold rounded border border-purple-400/30">
                                                        Admin
                                                    </span>
                                                @else
                                                    <span class="text-[9px] px-1.5 py-0.2 bg-sky-900/60 text-sky-200 font-semibold rounded border border-sky-400/30">
                                                        Staf - {{ $msg->sender_name }}
                                                    </span>
                                                @endif
                                            @endif
                                        </div>

                                        @if ($msg->attachment_path)
                                            <a href="{{ route($panel.'.chat.attachment', $msg, false) }}" target="_blank" rel="noopener">
                                                <img src="{{ route($panel.'.chat.attachment', $msg, false) }}"
                                                     alt="Lampiran chat"
                                                     loading="lazy"
                                                     class="mb-2 max-h-64 max-w-full rounded-lg object-contain">
                                            </a>
                                        @endif
                                        @if (filled($msg->message))
                                            <p class="whitespace-pre-wrap leading-relaxed">{{ $msg->message }}</p>
                                        @endif

                                        <div class="text-[10px] mt-1 text-right {{ $isCustomer ? 'text-stone-400' : 'text-rose-200' }}">
                                            {{ $msg->created_at->format('H:i') }} WIB
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Input Balasan -->
                    <div class="p-3 sm:p-4 bg-white border-t border-stone-200 shrink-0">
                        <form id="adminReplyForm"
                              action="{{ route($panel === 'staff' ? 'staff.chat.reply' : 'admin.chat.reply', $activeThread, false) }}"
                              method="POST"
                              novalidate
                              class="flex items-center gap-2">
                            @csrf
                            <input type="hidden" id="activeThreadId" value="{{ $activeThread->id }}">
                            <textarea id="adminMessageInput"
                                      name="message"
                                      rows="1"
                                      placeholder="Tulis balasan untuk {{ $activeThread->customer_name }}..."
                                      required
                                      class="flex-1 text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A] resize-none max-h-28"></textarea>
                            <label for="adminImageInput"
                                   title="Pilih foto"
                                   class="cursor-pointer p-2.5 rounded-xl border border-stone-200 text-stone-600 hover:bg-stone-50 shrink-0">
                                <span class="sr-only">Pilih foto</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4-4a2 2 0 012.8 0l3.2 3.2m-2-2 1.2-1.2a2 2 0 012.8 0L20 16m-2-8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </label>
                            <input id="adminImageInput"
                                   name="image"
                                   type="file"
                                   accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                   class="hidden">
                            <button type="submit"
                                    id="adminSendBtn"
                                    class="p-2.5 sm:px-4 sm:py-2.5 rounded-xl bg-[#6B0F1A] hover:bg-[#520912] active:scale-95 text-white font-semibold text-xs transition shadow-xs flex items-center justify-center gap-1.5 shrink-0">
                                <span class="hidden sm:inline">Balas</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </button>
                        </form>
                        <div id="adminImagePreview" class="hidden mt-2 items-start gap-2">
                            <img id="adminImagePreviewImage" alt="Preview lampiran" class="max-h-20 max-w-24 rounded-lg object-contain">
                            <button id="adminRemoveImage" type="button" class="text-xs text-stone-500 hover:text-rose-700">Hapus foto</button>
                        </div>
                        <p id="adminReplyFeedback" class="hidden mt-2 text-xs" role="status" aria-live="polite"></p>
                    </div>

                @else
                    <!-- State Tidak Ada Thread Dipilih -->
                    <div class="flex-1 flex flex-col items-center justify-center p-8 text-center text-stone-400">
                        <div class="w-16 h-16 rounded-full bg-stone-100 flex items-center justify-center text-stone-300 mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-stone-700">Pilih Percakapan</h3>
                        <p class="text-xs text-stone-500 mt-1 max-w-sm">
                            Pilih salah satu kontak obrolan dari daftar di sebelah kiri untuk melihat dan membalas pesan pembeli.
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </main>

    @if ($activeThread)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('adminChatMessagesContainer');
            const messagesList = document.getElementById('adminMessagesList');
            const replyForm = document.getElementById('adminReplyForm');
            const messageInput = document.getElementById('adminMessageInput');
            const sendBtn = document.getElementById('adminSendBtn');
            const replyFeedback = document.getElementById('adminReplyFeedback');
            const imageInput = document.getElementById('adminImageInput');
            const imagePreview = document.getElementById('adminImagePreview');
            const imagePreviewImage = document.getElementById('adminImagePreviewImage');
            const removeImageButton = document.getElementById('adminRemoveImage');
            const badgeTotalUnread = document.getElementById('badgeTotalUnread');
            const replyUrl = replyForm.getAttribute('action');
            const messagesUrl = "{{ route($panel === 'staff' ? 'staff.chat.messages' : 'admin.chat.messages', $activeThread, false) }}";
            const threadsUrl = "{{ route($panel === 'staff' ? 'staff.chat.threads' : 'admin.chat.threads', [], false) }}";

            if (replyForm.dataset.handlersAttached === 'true') return;
            replyForm.dataset.handlersAttached = 'true';

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

            function setReplyFeedback(message, isError) {
                replyFeedback.textContent = message;
                replyFeedback.classList.toggle('hidden', !message);
                replyFeedback.classList.toggle('text-rose-700', isError);
                replyFeedback.classList.toggle('text-emerald-700', !isError);
            }

            function scrollToBottom() {
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }
            scrollToBottom();

            // Auto-expand textarea
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            // Enter to submit
            messageInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if (!isSending) {
                        replyForm.requestSubmit();
                    }
                }
            });

            // Kirim Balasan Admin/Staf
            replyForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                if (isSending) return;

                const text = messageInput.value.trim();
                if (!text && imageInput.files.length === 0) {
                    setReplyFeedback('Pesan balasan tidak boleh kosong.', true);
                    messageInput.focus();
                    return;
                }

                const csrfToken = replyForm.querySelector('input[name="_token"]')?.value;
                if (!csrfToken) {
                    setReplyFeedback('Token keamanan tidak tersedia. Muat ulang halaman lalu coba lagi.', true);
                    console.error('Gagal mengirim balasan: token CSRF tidak ditemukan.');
                    return;
                }

                if (!replyUrl) {
                    setReplyFeedback('Alamat pengiriman tidak tersedia. Muat ulang halaman lalu coba lagi.', true);
                    return;
                }

                const formData = new FormData(replyForm);
                formData.set('message', text);
                isSending = true;
                sendBtn.disabled = true;
                messageInput.disabled = true;
                imageInput.disabled = true;
                setReplyFeedback('', false);

                try {
                    if (activePoll) {
                        await activePoll;
                    }

                    const response = await fetch(replyUrl, {
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

                    if (!response.ok) {
                        const validationMessage = data?.errors?.message?.[0];
                        const imageValidationMessage = data?.errors?.image?.[0];
                        throw new Error(validationMessage || imageValidationMessage || data?.message || `Pengiriman gagal (HTTP ${response.status}). Periksa autentikasi/CSRF atau log server.`);
                    }

                    if (!data?.success || !data.message) {
                        throw new Error(`Respons server tidak valid (HTTP ${response.status}).`);
                    }

                    appendAdminMessage(data.message);
                    scrollToBottom();
                    messageInput.value = '';
                    messageInput.style.height = 'auto';
                    clearImagePreview();
                } catch (err) {
                    const isNetworkError = err instanceof TypeError && /network|fetch/i.test(err.message);
                    const errorMessage = isNetworkError
                        ? 'Koneksi ke server terputus. Teks balasan tetap ada; periksa jaringan lalu coba kirim lagi.'
                        : (err.message || 'Balasan gagal dikirim. Teks balasan tetap ada; silakan coba lagi.');
                    setReplyFeedback(errorMessage, true);
                    console.error('Gagal mengirim balasan:', err);
                } finally {
                    isSending = false;
                    sendBtn.disabled = false;
                    messageInput.disabled = false;
                    imageInput.disabled = false;
                    messageInput.focus();
                }
            });

            function appendAdminMessage(msg) {
                if (document.getElementById('admin-msg-' + msg.id)) return;

                const wrapper = document.createElement('div');
                wrapper.id = 'admin-msg-' + msg.id;
                wrapper.dataset.messageId = String(msg.id);
                wrapper.className = 'flex flex-col ' + (msg.is_internal ? 'items-end' : 'items-start');

                const bubble = document.createElement('div');
                bubble.className = 'max-w-[85%] sm:max-w-[75%] rounded-2xl px-4 py-2.5 text-xs sm:text-sm shadow-2xs break-words ' +
                    (msg.is_internal ? 'bg-[#6B0F1A] text-white rounded-br-xs' : 'bg-white text-stone-800 border border-stone-200 rounded-bl-xs');

                let html = '';
                if (msg.is_internal) {
                    const badgeClass = msg.sender_type === 'admin'
                        ? 'bg-purple-900/60 text-purple-200 border border-purple-400/30'
                        : 'bg-sky-900/60 text-sky-200 border border-sky-400/30';

                    html += `<div class="flex items-center gap-1.5 mb-1 pb-1 border-b border-white/10">
                        <span class="font-bold text-[11px] text-amber-200">${msg.sender_name}</span>
                        <span class="text-[9px] px-1.5 py-0.2 rounded ${badgeClass}">${msg.sender_badge}</span>
                    </div>`;
                } else {
                    html += `<div class="flex items-center gap-1.5 mb-1 pb-1 border-b border-stone-100">
                        <span class="font-bold text-[11px] text-stone-900">${msg.sender_name}</span>
                        <span class="text-[9px] px-1.5 py-0.2 bg-stone-100 text-stone-600 font-medium rounded">Pembeli</span>
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
                html += `<div class="text-[10px] mt-1 text-right ${msg.is_internal ? 'text-rose-200' : 'text-stone-400'}">${msg.created_at} WIB</div>`;

                bubble.innerHTML = html;
                wrapper.appendChild(bubble);
                const nextMessage = Array.from(messagesList.children).find(
                    messageElement => Number(messageElement.dataset.messageId) > Number(msg.id)
                );
                messagesList.insertBefore(wrapper, nextMessage || null);
            }

            // Polling Pesan Aktif & Sidebar Threads tiap 3.5 detik
            async function pollChat() {
                if (isSending || activePoll) return;

                const pollRequest = (async function() {
                    try {
                        // 1. Ambil pesan thread aktif
                        const resMsg = await fetch(messagesUrl, {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (!resMsg.ok) {
                            throw new Error(`Polling pesan gagal (HTTP ${resMsg.status}).`);
                        }

                        const data = await resMsg.json();
                        let hasNew = false;
                        if (data.messages && data.messages.length > 0) {
                            data.messages.forEach(msg => {
                                if (!document.getElementById('admin-msg-' + msg.id)) {
                                    appendAdminMessage(msg);
                                    hasNew = true;
                                }
                            });
                            if (hasNew) scrollToBottom();
                        }

                        // 2. Ambil update status sidebar threads
                        const resThreads = await fetch(threadsUrl, {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (!resThreads.ok) {
                            throw new Error(`Polling daftar percakapan gagal (HTTP ${resThreads.status}).`);
                        }

                        const threadsData = await resThreads.json();
                        if (badgeTotalUnread) {
                            if (threadsData.total_unread > 0) {
                                badgeTotalUnread.textContent = `${threadsData.total_unread} Belum Dibaca`;
                                badgeTotalUnread.classList.remove('hidden');
                            } else {
                                badgeTotalUnread.classList.add('hidden');
                            }
                        }
                        if (threadsData.threads) {
                            threadsData.threads.forEach(t => {
                                const item = document.querySelector(`.thread-item[data-thread-id="${t.id}"]`);
                                if (item) {
                                    const badge = item.querySelector('.thread-badge');
                                    const lastMsg = item.querySelector('.thread-last-message');
                                    const time = item.querySelector('.thread-time');
                                    if (badge) {
                                        if (t.unread_admin_count > 0) {
                                            badge.textContent = t.unread_admin_count;
                                            badge.classList.remove('hidden');
                                        } else {
                                            badge.classList.add('hidden');
                                        }
                                    }
                                    if (lastMsg && t.last_message) lastMsg.textContent = t.last_message;
                                    if (time && t.last_message_time) time.textContent = t.last_message_time;
                                }
                            });
                        }
                    } catch (e) {
                        console.warn('Polling chat gagal; akan dicoba kembali:', e);
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

            setInterval(pollChat, 3500);
        });
    </script>
    @endif

</body>
</html>
