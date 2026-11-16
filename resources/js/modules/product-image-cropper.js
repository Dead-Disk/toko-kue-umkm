/**
 * Module: Product Image Cropper & Position Adjuster
 * Memberikan antarmuka visual untuk memotong (crop 1:1) dan mengatur posisi
 * foto produk sebelum formulir disubmit ke server.
 * Mendukung Cropper.js jika tersedia, atau fallback otomatis ke interactive HTML5 canvas.
 */

export function initProductImageCropper() {
    // Siapkan template modal di DOM jika belum ada
    ensureCropperModal();

    // Inisialisasi pada form create, edit, dan modal index
    bindCropperInstance({
        fileInputId: 'createFotoInput',
        previewImgId: 'createPreviewImg',
        cropBtnId: 'createCropBtn',
        hiddenBase64Id: 'createCroppedBase64',
    });

    bindCropperInstance({
        fileInputId: 'editFotoInput',
        previewImgId: 'editPreviewImg',
        cropBtnId: 'editCropBtn',
        hiddenBase64Id: 'editCroppedBase64',
    });

    bindCropperInstance({
        fileInputId: 'fotoInput',
        previewImgId: 'previewImage',
        cropBtnId: 'modalCropBtn',
        hiddenBase64Id: 'modalCroppedBase64',
    });
}

/**
 * Menyambungkan input file, tombol crop, preview img, dan hidden base64 ke engine cropper
 */
function bindCropperInstance({ fileInputId, previewImgId, cropBtnId, hiddenBase64Id }) {
    const fileInput = document.getElementById(fileInputId);
    const previewImg = document.getElementById(previewImgId);
    const cropBtn = document.getElementById(cropBtnId);
    const hiddenBase64 = document.getElementById(hiddenBase64Id);

    if (!fileInput) return;

    // Simpan data gambar asli (dataURL atau file) untuk re-crop
    let activeImageSource = null;
    let originalFileName = 'product.jpg';

    fileInput.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            alert('Silakan pilih file gambar (JPG, PNG, WEBP).');
            return;
        }

        originalFileName = file.name;
        const reader = new FileReader();
        reader.onload = function (e) {
            activeImageSource = e.target.result;
            openCropModal({
                imageSrc: activeImageSource,
                fileName: originalFileName,
                onApply: function (croppedBlob, croppedDataUrl) {
                    applyCroppedResult({
                        fileInput,
                        previewImg,
                        hiddenBase64,
                        cropBtn,
                        croppedBlob,
                        croppedDataUrl,
                        fileName: originalFileName,
                    });
                },
            });
        };
        reader.readAsDataURL(file);
    });

    if (cropBtn) {
        cropBtn.addEventListener('click', function () {
            // Gunakan gambar yang sedang aktif, atau ambil src dari previewImg
            const src = activeImageSource || (previewImg ? previewImg.src : null);
            if (!src || src.includes('rec-img') || src.includes('placeholder')) {
                // Jika belum ada foto, trigger klik input file
                fileInput.click();
                return;
            }

            openCropModal({
                imageSrc: src,
                fileName: originalFileName,
                onApply: function (croppedBlob, croppedDataUrl) {
                    applyCroppedResult({
                        fileInput,
                        previewImg,
                        hiddenBase64,
                        cropBtn,
                        croppedBlob,
                        croppedDataUrl,
                        fileName: originalFileName,
                    });
                },
            });
        });
    }
}

/**
 * Mengisi DataTransfer ke fileInput dan memperbarui preview
 */
function applyCroppedResult({ fileInput, previewImg, hiddenBase64, cropBtn, croppedBlob, croppedDataUrl, fileName }) {
    try {
        const safeName = fileName.replace(/\.[^/.]+$/, '') + '.jpg';
        const file = new File([croppedBlob], safeName, { type: 'image/jpeg' });
        const container = new DataTransfer();
        container.items.add(file);
        fileInput.files = container.files;
    } catch (err) {
        console.warn('DataTransfer files assignment fallback:', err);
    }

    if (hiddenBase64) {
        hiddenBase64.value = croppedDataUrl;
    }

    if (previewImg) {
        previewImg.src = croppedDataUrl;
    }

    if (cropBtn) {
        cropBtn.classList.remove('hidden');
    }

    const toastContainer = document.getElementById('toastContainer');
    if (toastContainer && window.showToast) {
        window.showToast('Foto berhasil disesuaikan & dipotong.');
    }
}

/**
 * Singleton state untuk modal cropper
 */
let cropperModalEl = null;
let currentCropperInstance = null;
let currentCanvasState = null;

function ensureCropperModal() {
    if (document.getElementById('productCropperModal')) {
        cropperModalEl = document.getElementById('productCropperModal');
        return;
    }

    const modalHtml = `
    <div id="productCropperModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-3 sm:p-4 bg-stone-900/80 backdrop-blur-sm transition-opacity duration-200">
        <div class="bg-white rounded-2xl sm:rounded-3xl shadow-2xl max-w-2xl w-full overflow-hidden flex flex-col max-h-[92vh] border border-stone-200/80">
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-stone-100 flex items-center justify-between bg-stone-50/70 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 text-[#6B0F1A] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3v14a2 2 0 002 2h14M17 21v-4a2 2 0 00-2-2H3"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-[#5A121A]">Sesuaikan Posisi & Crop Foto</h3>
                        <p class="text-[11px] text-stone-500">Geser, perbesar, dan posisikan foto agar kue terlihat presisi di thumbnail katalog.</p>
                    </div>
                </div>
                <button type="button" id="closeCropModalBtn" class="p-1.5 text-stone-400 hover:text-stone-700 rounded-lg hover:bg-stone-200/60 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-4 sm:p-5 overflow-y-auto space-y-4 flex-1">
                <!-- Cropper Workspace Area -->
                <div class="relative w-full h-72 sm:h-80 bg-stone-900 rounded-2xl overflow-hidden flex items-center justify-center select-none shadow-inner" id="cropperWorkspace">
                    <!-- Standard image for Cropper.js if available -->
                    <img id="cropperSourceImg" src="" alt="Source" class="max-h-full max-w-full hidden">
                    
                    <!-- Fallback Interactive Canvas -->
                    <canvas id="cropperCanvas" class="w-full h-full cursor-grab active:cursor-grabbing"></canvas>

                    <!-- Guiding Frame Overlay for Canvas mode -->
                    <div id="canvasFrameOverlay" class="pointer-events-none absolute inset-0 flex items-center justify-center">
                        <div class="w-56 h-56 sm:w-64 sm:h-64 border-2 border-dashed border-white/90 rounded-2xl shadow-[0_0_0_9999px_rgba(0,0,0,0.55)] relative">
                            <!-- Rule of Thirds Grid -->
                            <div class="absolute inset-0 grid grid-cols-3 grid-rows-3 pointer-events-none">
                                <div class="border-r border-b border-white/25"></div>
                                <div class="border-r border-b border-white/25"></div>
                                <div class="border-b border-white/25"></div>
                                <div class="border-r border-b border-white/25"></div>
                                <div class="border-r border-b border-white/25"></div>
                                <div class="border-b border-white/25"></div>
                                <div class="border-r border-white/25"></div>
                                <div class="border-r border-white/25"></div>
                                <div></div>
                            </div>
                            <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-[10px] text-white/90 font-medium tracking-wide whitespace-nowrap bg-black/60 px-2 py-0.5 rounded-full">
                                Area Crop 1:1 (Persegi)
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Controls & Live Preview Bar -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-3 bg-stone-50 rounded-2xl border border-stone-200/70">
                    <!-- Zoom & Pan Controls -->
                    <div class="flex-1 w-full space-y-2">
                        <div class="flex items-center justify-between text-xs font-semibold text-stone-700">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                Zoom & Skala Foto:
                            </span>
                            <span id="zoomLevelText" class="text-stone-500 font-mono text-[11px]">100%</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" id="cropZoomOutBtn" title="Perkecil" class="p-1.5 rounded-lg border border-stone-200 bg-white hover:bg-stone-100 text-stone-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            </button>
                            <input type="range" id="cropZoomRange" min="0.5" max="3" step="0.05" value="1" class="w-full h-1.5 bg-stone-200 rounded-lg appearance-none cursor-pointer accent-[#6B0F1A]">
                            <button type="button" id="cropZoomInBtn" title="Perbesar" class="p-1.5 rounded-lg border border-stone-200 bg-white hover:bg-stone-100 text-stone-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                        <div class="flex items-center gap-2 pt-1">
                            <button type="button" id="cropRotateLeftBtn" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg border border-stone-200 bg-white hover:bg-stone-100 text-stone-700 transition">
                                <span>⟲ Putar 90°</span>
                            </button>
                            <button type="button" id="cropRotateRightBtn" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg border border-stone-200 bg-white hover:bg-stone-100 text-stone-700 transition">
                                <span>⟳ Putar 90°</span>
                            </button>
                            <button type="button" id="cropResetBtn" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg border border-stone-200 bg-white hover:bg-stone-100 text-stone-500 transition ml-auto">
                                <span>↺ Reset</span>
                            </button>
                        </div>
                    </div>

                    <!-- Mini Live Thumbnail Preview -->
                    <div class="shrink-0 flex items-center gap-3 border-t sm:border-t-0 sm:border-l border-stone-200 pt-3 sm:pt-0 sm:pl-4">
                        <div class="w-20 h-20 rounded-xl overflow-hidden border border-stone-300 bg-stone-100 aspect-square shadow-xs shrink-0">
                            <canvas id="cropPreviewCanvas" class="w-full h-full object-cover"></canvas>
                        </div>
                        <div class="text-[11px] text-stone-500 leading-tight">
                            <p class="font-bold text-stone-700">Hasil Thumbnail</p>
                            <p class="text-[10px] text-stone-400 mt-0.5">Tampilan yang akan dilihat pembeli di katalog produk.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3.5 border-t border-stone-100 flex items-center justify-end gap-2.5 bg-stone-50/70 shrink-0">
                <button type="button" id="cancelCropModalBtn" class="px-4 py-2 text-xs font-semibold rounded-xl border border-stone-200 bg-white hover:bg-stone-100 text-stone-700 transition">
                    Batal
                </button>
                <button type="button" id="applyCropModalBtn" class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-semibold rounded-xl bg-[#6B0F1A] hover:bg-[#520A13] text-white shadow-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Terapkan Crop & Gunakan Foto</span>
                </button>
            </div>
        </div>
    </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    cropperModalEl = document.getElementById('productCropperModal');
}

/**
 * Membuka modal crop dan menginisialisasi canvas / cropper engine
 */
function openCropModal({ imageSrc, fileName, onApply }) {
    ensureCropperModal();
    if (!cropperModalEl) return;

    cropperModalEl.classList.remove('hidden');
    cropperModalEl.classList.add('flex');

    const sourceImg = document.getElementById('cropperSourceImg');
    const canvas = document.getElementById('cropperCanvas');
    const previewCanvas = document.getElementById('cropPreviewCanvas');
    const zoomRange = document.getElementById('cropZoomRange');
    const zoomText = document.getElementById('zoomLevelText');
    const zoomInBtn = document.getElementById('cropZoomInBtn');
    const zoomOutBtn = document.getElementById('cropZoomOutBtn');
    const rotateLeftBtn = document.getElementById('cropRotateLeftBtn');
    const rotateRightBtn = document.getElementById('cropRotateRightBtn');
    const resetBtn = document.getElementById('cropResetBtn');
    const closeBtn = document.getElementById('closeCropModalBtn');
    const cancelBtn = document.getElementById('cancelCropModalBtn');
    const applyBtn = document.getElementById('applyCropModalBtn');
    const frameOverlay = document.getElementById('canvasFrameOverlay');

    let isUsingCropperJs = typeof window.Cropper !== 'undefined';

    const loadedImg = new Image();
    loadedImg.crossOrigin = 'anonymous';
    loadedImg.onload = function () {
        if (isUsingCropperJs) {
            // Gunakan Cropper.js jika tersedia
            sourceImg.src = imageSrc;
            sourceImg.classList.remove('hidden');
            canvas.classList.add('hidden');
            frameOverlay.classList.add('hidden');

            if (currentCropperInstance) {
                currentCropperInstance.destroy();
            }

            currentCropperInstance = new window.Cropper(sourceImg, {
                aspectRatio: 1,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.9,
                responsive: true,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                crop: function () {
                    updateCropperJsPreview(currentCropperInstance, previewCanvas);
                },
            });
        } else {
            // Mode Built-in HTML5 Canvas Interactive Positioning
            sourceImg.classList.add('hidden');
            canvas.classList.remove('hidden');
            frameOverlay.classList.remove('hidden');

            initCanvasPositioner(canvas, loadedImg, previewCanvas);
        }
    };
    loadedImg.src = imageSrc;

    // Handler Zoom & Rotate
    const handleZoomChange = (val) => {
        const num = parseFloat(val);
        zoomText.textContent = Math.round(num * 100) + '%';
        if (isUsingCropperJs && currentCropperInstance) {
            currentCropperInstance.zoomTo(num);
        } else if (currentCanvasState) {
            currentCanvasState.scale = num;
            currentCanvasState.redraw();
        }
    };

    zoomRange.oninput = (e) => handleZoomChange(e.target.value);
    zoomInBtn.onclick = () => {
        zoomRange.value = Math.min(3, parseFloat(zoomRange.value) + 0.1);
        handleZoomChange(zoomRange.value);
    };
    zoomOutBtn.onclick = () => {
        zoomRange.value = Math.max(0.5, parseFloat(zoomRange.value) - 0.1);
        handleZoomChange(zoomRange.value);
    };
    rotateLeftBtn.onclick = () => {
        if (isUsingCropperJs && currentCropperInstance) {
            currentCropperInstance.rotate(-90);
        } else if (currentCanvasState) {
            currentCanvasState.rotation = (currentCanvasState.rotation - 90) % 360;
            currentCanvasState.redraw();
        }
    };
    rotateRightBtn.onclick = () => {
        if (isUsingCropperJs && currentCropperInstance) {
            currentCropperInstance.rotate(90);
        } else if (currentCanvasState) {
            currentCanvasState.rotation = (currentCanvasState.rotation + 90) % 360;
            currentCanvasState.redraw();
        }
    };
    resetBtn.onclick = () => {
        zoomRange.value = 1;
        zoomText.textContent = '100%';
        if (isUsingCropperJs && currentCropperInstance) {
            currentCropperInstance.reset();
        } else if (currentCanvasState) {
            currentCanvasState.reset();
        }
    };

    // Close / Cancel
    const closeModal = () => {
        cropperModalEl.classList.add('hidden');
        cropperModalEl.classList.remove('flex');
        if (currentCropperInstance) {
            currentCropperInstance.destroy();
            currentCropperInstance = null;
        }
    };
    closeBtn.onclick = closeModal;
    cancelBtn.onclick = closeModal;

    // Apply Crop
    applyBtn.onclick = () => {
        if (isUsingCropperJs && currentCropperInstance) {
            const outCanvas = currentCropperInstance.getCroppedCanvas({
                width: 800,
                height: 800,
                imageSmoothingQuality: 'high',
            });
            outCanvas.toBlob(
                (blob) => {
                    const dataUrl = outCanvas.toDataURL('image/jpeg', 0.92);
                    closeModal();
                    if (onApply) onApply(blob, dataUrl);
                },
                'image/jpeg',
                0.92
            );
        } else if (currentCanvasState) {
            const outCanvas = currentCanvasState.generateCroppedCanvas(800, 800);
            outCanvas.toBlob(
                (blob) => {
                    const dataUrl = outCanvas.toDataURL('image/jpeg', 0.92);
                    closeModal();
                    if (onApply) onApply(blob, dataUrl);
                },
                'image/jpeg',
                0.92
            );
        }
    };
}

/**
 * Update preview untuk Cropper.js
 */
function updateCropperJsPreview(cropper, previewCanvas) {
    if (!cropper || !previewCanvas) return;
    try {
        const mini = cropper.getCroppedCanvas({ width: 160, height: 160 });
        if (!mini) return;
        previewCanvas.width = 160;
        previewCanvas.height = 160;
        const ctx = previewCanvas.getContext('2d');
        ctx.clearRect(0, 0, 160, 160);
        ctx.drawImage(mini, 0, 0, 160, 160);
    } catch (e) {
        // Abaikan bila cropper belum siap
    }
}

/**
 * Engine Built-in Interactive HTML5 Canvas:
 * Mengizinkan drag / pan, zoom, dan rotate foto secara langsung pada canvas tanpa library eksternal.
 */
function initCanvasPositioner(canvas, img, previewCanvas) {
    const ctx = canvas.getContext('2d');
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width || 600;
    canvas.height = rect.height || 320;

    const cropBoxSize = Math.min(canvas.width, canvas.height) * 0.75;
    const cropX = (canvas.width - cropBoxSize) / 2;
    const cropY = (canvas.height - cropBoxSize) / 2;

    const state = {
        img,
        scale: 1,
        offsetX: 0,
        offsetY: 0,
        rotation: 0,
        isDragging: false,
        startX: 0,
        startY: 0,
        cropX,
        cropY,
        cropSize: cropBoxSize,
        reset: function () {
            this.scale = 1;
            this.offsetX = 0;
            this.offsetY = 0;
            this.rotation = 0;
            this.redraw();
        },
        redraw: function () {
            drawWorkspace(ctx, canvas, this);
            drawPreview(previewCanvas, this);
        },
        generateCroppedCanvas: function (targetW = 800, targetH = 800) {
            const out = document.createElement('canvas');
            out.width = targetW;
            out.height = targetH;
            const oCtx = out.getContext('2d');
            oCtx.imageSmoothingQuality = 'high';

            // Skala dari crop frame di layar ke target output
            const scaleToOut = targetW / this.cropSize;

            oCtx.save();
            oCtx.translate(targetW / 2, targetH / 2);
            oCtx.rotate((this.rotation * Math.PI) / 180);

            // Perhitungan posisi relatif gambar
            const effectiveW = this.img.width * this.scale * scaleToOut;
            const effectiveH = this.img.height * this.scale * scaleToOut;
            const drawX = -effectiveW / 2 + this.offsetX * scaleToOut;
            const drawY = -effectiveH / 2 + this.offsetY * scaleToOut;

            oCtx.drawImage(this.img, drawX, drawY, effectiveW, effectiveH);
            oCtx.restore();

            return out;
        },
    };

    // Hitung inisial fit agar gambar menutupi kotak crop
    const initialRatio = Math.max(cropBoxSize / img.width, cropBoxSize / img.height);
    state.scale = initialRatio * 1.1;

    // Pointer events untuk pan / drag
    canvas.onpointerdown = (e) => {
        state.isDragging = true;
        state.startX = e.clientX - state.offsetX;
        state.startY = e.clientY - state.offsetY;
        canvas.setPointerCapture(e.pointerId);
    };

    canvas.onpointermove = (e) => {
        if (!state.isDragging) return;
        state.offsetX = e.clientX - state.startX;
        state.offsetY = e.clientY - state.startY;
        state.redraw();
    };

    canvas.onpointerup = (e) => {
        state.isDragging = false;
        try {
            canvas.releasePointerCapture(e.pointerId);
        } catch (_) {}
    };

    canvas.onwheel = (e) => {
        e.preventDefault();
        const delta = e.deltaY < 0 ? 0.08 : -0.08;
        state.scale = Math.min(3.5, Math.max(0.4, state.scale + delta));
        const zoomRange = document.getElementById('cropZoomRange');
        const zoomText = document.getElementById('zoomLevelText');
        if (zoomRange) zoomRange.value = state.scale;
        if (zoomText) zoomText.textContent = Math.round(state.scale * 100) + '%';
        state.redraw();
    };

    currentCanvasState = state;
    state.redraw();
}

function drawWorkspace(ctx, canvas, state) {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    ctx.save();
    // Geser ke tengah canvas
    ctx.translate(canvas.width / 2 + state.offsetX, canvas.height / 2 + state.offsetY);
    ctx.rotate((state.rotation * Math.PI) / 180);

    const drawW = state.img.width * state.scale;
    const drawH = state.img.height * state.scale;
    ctx.drawImage(state.img, -drawW / 2, -drawH / 2, drawW, drawH);
    ctx.restore();
}

function drawPreview(previewCanvas, state) {
    if (!previewCanvas) return;
    previewCanvas.width = 160;
    previewCanvas.height = 160;
    const pCtx = previewCanvas.getContext('2d');
    pCtx.clearRect(0, 0, 160, 160);

    const scaleToOut = 160 / state.cropSize;

    pCtx.save();
    pCtx.translate(80, 80);
    pCtx.rotate((state.rotation * Math.PI) / 180);

    const effectiveW = state.img.width * state.scale * scaleToOut;
    const effectiveH = state.img.height * state.scale * scaleToOut;
    const drawX = -effectiveW / 2 + state.offsetX * scaleToOut;
    const drawY = -effectiveH / 2 + state.offsetY * scaleToOut;

    pCtx.drawImage(state.img, drawX, drawY, effectiveW, effectiveH);
    pCtx.restore();
}
