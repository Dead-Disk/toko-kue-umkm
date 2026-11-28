@props(['formId'])

@php
    $savedChatPin = session('customer_chat_pin');
    $hasSavedChatPin = is_string($savedChatPin) && preg_match('/^\d{6}$/', $savedChatPin) === 1;
@endphp

<div id="chatPinModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-xs p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="chatPinModalTitle">
    <div class="w-full max-w-md rounded-3xl bg-white border border-[#F4E1D9] shadow-2xl overflow-hidden">
        {{-- Header --}}
        <div class="bg-[#6B0F1A] px-5 py-4 text-white flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-amber-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h3 id="chatPinModalTitle" class="text-sm sm:text-base font-bold leading-tight">PIN Rahasia Chat Pesanan</h3>
                    <p id="chatPinStepIndicator" class="text-[11px] text-rose-200">Langkah 1 dari 3</p>
                </div>
            </div>
            <button type="button" id="chatPinCloseBtn" aria-label="Tutup" class="p-1 rounded-lg text-rose-200 hover:text-white hover:bg-white/10 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-5 sm:p-6">
            {{-- ================= STEP 1: Buat PIN 6 Digit ================= --}}
            <div id="chatPinStep1" class="space-y-4">
                <div class="text-center space-y-1">
                    <h4 class="text-base font-bold text-stone-800">Buat PIN 6 Digit</h4>
                    <p class="text-xs text-stone-500 leading-relaxed">
                        Masukkan 6 digit angka untuk melindungi chat pesanan Anda. Penjual tidak dapat melihat PIN ini.
                    </p>
                </div>

                <div id="pinCreateBoxes" class="flex items-center justify-center gap-2 sm:gap-2.5 py-2">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="password"
                               inputmode="numeric"
                               maxlength="1"
                               autocomplete="off"
                               data-pin-box="create"
                               data-index="{{ $i }}"
                               aria-label="Digit PIN ke-{{ $i + 1 }}"
                               class="w-10 h-12 sm:w-11 sm:h-13 text-center text-lg font-mono font-bold rounded-xl border-2 border-stone-200 bg-stone-50/50 text-stone-900 focus:outline-none focus:border-[#6B0F1A] focus:bg-white focus:ring-2 focus:ring-[#6B0F1A]/20 transition">
                    @endfor
                </div>

                <p id="chatPinErrorStep1" class="hidden text-xs font-medium text-rose-700 bg-rose-50 border border-rose-200 rounded-xl px-3 py-2 text-center"></p>

                <button type="button"
                        id="chatPinNextToConfirmBtn"
                        class="w-full py-3 px-5 rounded-full bg-[#6B0F1A] hover:bg-[#540F18] active:scale-[0.99] text-white text-sm font-bold shadow-md transition flex items-center justify-center gap-1.5 cursor-pointer">
                    <span>Lanjut Konfirmasi PIN</span>
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </div>

            {{-- ================= STEP 2: Konfirmasi PIN 6 Digit ================= --}}
            <div id="chatPinStep2" class="hidden space-y-4">
                <div class="text-center space-y-1">
                    <h4 class="text-base font-bold text-stone-800">Konfirmasi PIN Anda</h4>
                    <p class="text-xs text-stone-500 leading-relaxed">
                        Ketik ulang 6 digit PIN yang baru saja Anda buat untuk memastikan tidak salah ketik.
                    </p>
                </div>

                <div id="pinConfirmBoxes" class="flex items-center justify-center gap-2 sm:gap-2.5 py-2">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="password"
                               inputmode="numeric"
                               maxlength="1"
                               autocomplete="off"
                               data-pin-box="confirm"
                               data-index="{{ $i }}"
                               aria-label="Konfirmasi digit PIN ke-{{ $i + 1 }}"
                               class="w-10 h-12 sm:w-11 sm:h-13 text-center text-lg font-mono font-bold rounded-xl border-2 border-stone-200 bg-stone-50/50 text-stone-900 focus:outline-none focus:border-[#6B0F1A] focus:bg-white focus:ring-2 focus:ring-[#6B0F1A]/20 transition">
                    @endfor
                </div>

                <p id="chatPinErrorStep2" class="hidden text-xs font-medium text-rose-700 bg-rose-50 border border-rose-200 rounded-xl px-3 py-2 text-center"></p>

                <div class="flex gap-2.5">
                    <button type="button"
                            id="chatPinBackToCreateBtn"
                            class="px-4 py-3 rounded-full border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 text-xs sm:text-sm font-semibold transition cursor-pointer">
                        Ubah PIN
                    </button>
                    <button type="button"
                            id="chatPinConfirmBtn"
                            class="flex-1 py-3 px-5 rounded-full bg-[#6B0F1A] hover:bg-[#540F18] active:scale-[0.99] text-white text-xs sm:text-sm font-bold shadow-md transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <span>Konfirmasi PIN</span>
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- ================= STEP 3: PIN Berhasil + Salin PIN ================= --}}
            <div id="chatPinStep3" class="hidden space-y-4 text-center">
                <div class="w-12 h-12 mx-auto rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <div class="space-y-1">
                    <h4 class="text-base font-bold text-stone-800">PIN Berhasil Dibuat!</h4>
                    <p class="text-xs text-stone-500 leading-relaxed">
                        Simpan atau salin PIN Anda sebelum pesanan dikirim. PIN ini wajib dipakai saat membuka ulang chat pesanan.
                    </p>
                </div>

                <div class="rounded-2xl bg-[#FAF2EE] border border-[#F4E1D9] p-3.5 flex items-center justify-between gap-3">
                    <div class="text-left">
                        <span class="block text-[10px] uppercase tracking-wider font-semibold text-stone-500">PIN Rahasia Anda</span>
                        <span id="chatPinSuccessValue" class="font-mono text-lg font-extrabold tracking-[0.25em] text-[#6B0F1A]">••••••</span>
                    </div>
                    <button type="button"
                            id="chatPinCopyBtn"
                            class="px-3.5 py-2 rounded-xl bg-white hover:bg-stone-50 border border-[#E8B8AE] text-[#6B0F1A] text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shrink-0 shadow-2xs">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span id="chatPinCopyText">Salin PIN</span>
                    </button>
                </div>

                <button type="button"
                        id="chatPinFinishBtn"
                        class="w-full py-3.5 px-5 rounded-full bg-[#6B0F1A] hover:bg-[#540F18] active:scale-[0.99] text-white text-sm font-bold shadow-md transition flex items-center justify-center gap-1.5 cursor-pointer">
                    <span>Selesai &amp; Kirim Pesanan</span>
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const targetForm = document.getElementById(@json($formId));
        const modal = document.getElementById('chatPinModal');
        const stepIndicator = document.getElementById('chatPinStepIndicator');
        const step1 = document.getElementById('chatPinStep1');
        const step2 = document.getElementById('chatPinStep2');
        const step3 = document.getElementById('chatPinStep3');
        const createBoxes = Array.from(document.querySelectorAll('[data-pin-box="create"]'));
        const confirmBoxes = Array.from(document.querySelectorAll('[data-pin-box="confirm"]'));
        const err1 = document.getElementById('chatPinErrorStep1');
        const err2 = document.getElementById('chatPinErrorStep2');
        const nextBtn = document.getElementById('chatPinNextToConfirmBtn');
        const backBtn = document.getElementById('chatPinBackToCreateBtn');
        const confirmBtn = document.getElementById('chatPinConfirmBtn');
        const copyBtn = document.getElementById('chatPinCopyBtn');
        const copyText = document.getElementById('chatPinCopyText');
        const successValueEl = document.getElementById('chatPinSuccessValue');
        const finishBtn = document.getElementById('chatPinFinishBtn');
        const closeBtn = document.getElementById('chatPinCloseBtn');

        if (!targetForm || !modal) return;

        let hiddenPinInput = targetForm.querySelector('input[name="chat_pin"]');
        if (!hiddenPinInput) {
            hiddenPinInput = document.createElement('input');
            hiddenPinInput.type = 'hidden';
            hiddenPinInput.name = 'chat_pin';
            targetForm.appendChild(hiddenPinInput);
        }

        hiddenPinInput.value = @json($hasSavedChatPin ? $savedChatPin : '');

        if (!/^\d{6}$/.test(hiddenPinInput.value)) {
            hiddenPinInput.value = '';
        }

        const savePinUrl = "{{ route('customer.chat.pin.store') }}";
        const csrfTokenInput = targetForm.querySelector('input[name="_token"]');

        async function persistChatPin(pin) {
            if (!csrfTokenInput) {
                return;
            }

            try {
                await fetch(savePinUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfTokenInput.value,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ chat_pin: pin }),
                });
            } catch (error) {
                // PIN tetap dipakai untuk pesanan ini walau sesi gagal disimpan.
            }
        }

        let createdPin = hiddenPinInput.value;
        let pinPersistPromise = null;

        function getBoxValue(boxes) {
            return boxes.map((b) => b.value).join('');
        }

        function clearBoxes(boxes) {
            boxes.forEach((b) => { b.value = ''; });
        }

        function showErr(el, msg) {
            el.textContent = msg;
            el.classList.remove('hidden');
        }

        function hideErr(el) {
            el.textContent = '';
            el.classList.add('hidden');
        }

        function goToStep(stepNumber) {
            step1.classList.toggle('hidden', stepNumber !== 1);
            step2.classList.toggle('hidden', stepNumber !== 2);
            step3.classList.toggle('hidden', stepNumber !== 3);
            stepIndicator.textContent = `Langkah ${stepNumber} dari 3`;

            if (stepNumber === 1) {
                hideErr(err1);
                createBoxes[0]?.focus();
            } else if (stepNumber === 2) {
                hideErr(err2);
                clearBoxes(confirmBoxes);
                confirmBoxes[0]?.focus();
            }
        }

        function bindPinBoxes(boxes, onComplete) {
            boxes.forEach((box, idx) => {
                box.addEventListener('input', () => {
                    const digits = box.value.replace(/\D/g, '');
                    if (digits.length > 1) {
                        digits.slice(0, boxes.length).split('').forEach((d, offset) => {
                            if (boxes[idx + offset]) {
                                boxes[idx + offset].value = d;
                            }
                        });
                        const nextFocus = Math.min(idx + digits.length, boxes.length - 1);
                        boxes[nextFocus]?.focus();
                    } else {
                        box.value = digits;
                        if (digits && idx < boxes.length - 1) {
                            boxes[idx + 1]?.focus();
                        }
                    }

                    if (getBoxValue(boxes).length === 6 && typeof onComplete === 'function') {
                        onComplete();
                    }
                });

                box.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !box.value && idx > 0) {
                        boxes[idx - 1].value = '';
                        boxes[idx - 1]?.focus();
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        if (typeof onComplete === 'function') {
                            onComplete();
                        }
                    }
                });

                box.addEventListener('paste', (e) => {
                    const pasted = (e.clipboardData?.getData('text') || '').replace(/\D/g, '').slice(0, 6);
                    if (!pasted) return;
                    e.preventDefault();
                    pasted.split('').forEach((d, i) => {
                        if (boxes[i]) boxes[i].value = d;
                    });
                    boxes[Math.min(pasted.length, 5)]?.focus();
                    if (pasted.length === 6 && typeof onComplete === 'function') {
                        onComplete();
                    }
                });
            });
        }

        function handleStep1Next() {
            const val = getBoxValue(createBoxes);
            if (!/^\d{6}$/.test(val)) {
                showErr(err1, 'PIN wajib diisi tepat 6 digit angka.');
                const firstEmpty = createBoxes.find((b) => !b.value) || createBoxes[0];
                firstEmpty?.focus();
                return;
            }
            createdPin = val;
            goToStep(2);
        }

        function handleStep2Confirm() {
            const confirmVal = getBoxValue(confirmBoxes);
            if (!/^\d{6}$/.test(confirmVal)) {
                showErr(err2, 'Konfirmasi PIN wajib diisi tepat 6 digit angka.');
                const firstEmpty = confirmBoxes.find((b) => !b.value) || confirmBoxes[0];
                firstEmpty?.focus();
                return;
            }
            if (confirmVal !== createdPin) {
                showErr(err2, 'Konfirmasi PIN tidak cocok. Silakan ketik ulang.');
                clearBoxes(confirmBoxes);
                confirmBoxes[0]?.focus();
                return;
            }

            hiddenPinInput.value = createdPin;
            successValueEl.textContent = createdPin;
            pinPersistPromise = persistChatPin(createdPin);
            goToStep(3);
        }

        bindPinBoxes(createBoxes, () => hideErr(err1));
        bindPinBoxes(confirmBoxes, () => hideErr(err2));

        nextBtn.addEventListener('click', handleStep1Next);
        backBtn.addEventListener('click', () => goToStep(1));
        confirmBtn.addEventListener('click', handleStep2Confirm);

        if (createdPin) {
            successValueEl.textContent = createdPin;
            goToStep(3);
        }

        targetForm.addEventListener('submit', (event) => {
            if (targetForm.dataset.pinConfirmed === 'true') {
                return;
            }

            if (typeof targetForm.reportValidity === 'function' && !targetForm.reportValidity()) {
                return;
            }

            if (/^\d{6}$/.test(hiddenPinInput.value)) {
                createdPin = hiddenPinInput.value;

                if (pinPersistPromise) {
                    event.preventDefault();
                    targetForm.dataset.pinConfirmed = 'true';
                    const pendingPinSave = pinPersistPromise;
                    pinPersistPromise = null;
                    pendingPinSave.then(() => targetForm.submit());
                }

                return;
            }

            event.preventDefault();
            createdPin = '';
            clearBoxes(createBoxes);
            clearBoxes(confirmBoxes);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            goToStep(1);
        });

        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        copyBtn.addEventListener('click', async () => {
            if (!createdPin) return;
            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(createdPin);
                } else {
                    const temp = document.createElement('input');
                    temp.value = createdPin;
                    document.body.appendChild(temp);
                    temp.select();
                    document.execCommand('copy');
                    document.body.removeChild(temp);
                }
                const original = copyText.textContent;
                copyText.textContent = 'PIN Tersalin!';
                setTimeout(() => {
                    copyText.textContent = original;
                }, 2000);
            } catch (e) {
                prompt('Salin PIN rahasia Anda:', createdPin);
            }
        });

        finishBtn.addEventListener('click', async () => {
            if (!/^\d{6}$/.test(createdPin)) return;
            hiddenPinInput.value = createdPin;
            targetForm.dataset.pinConfirmed = 'true';
            finishBtn.disabled = true;
            closeModal();

            if (pinPersistPromise) {
                const pendingPinSave = pinPersistPromise;
                pinPersistPromise = null;
                await pendingPinSave;
            }

            targetForm.submit();
        });
    })();
</script>
