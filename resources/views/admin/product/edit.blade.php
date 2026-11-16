<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Admin Nak Mbarep</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-nav.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-[#FAF2EE] text-stone-800 antialiased min-h-screen flex flex-col lg:flex-row">

    <!-- Mobile Topbar (< lg) -->
    <header class="lg:hidden bg-[#4D626C] text-white px-4 py-3 flex items-center justify-between sticky top-0 z-40 shadow-sm">
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/logo-nav.png') }}" alt="Nak Mbarep Logo" class="h-6 w-auto brightness-0 invert">
            <span class="font-bold text-base tracking-tight">Nak Mbarep</span>
        </div>
        <a href="{{ route('admin.produk.index') }}" class="text-xs text-stone-200 hover:text-white">← Kembali</a>
    </header>

    <!-- Desktop Sidebar (>= lg) -->
    <div class="hidden lg:block shrink-0 sticky top-0 h-screen">
        @include('admin.partials.sidebar', ['active' => 'produk'])
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 min-w-0 p-4 sm:p-6 lg:p-8 xl:p-10 overflow-y-auto">
        <div class="max-w-2xl mx-auto">
            <!-- Breadcrumbs / Top Back -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <a href="{{ route('admin.produk.index') }}" class="inline-flex items-center gap-1.5 text-xs text-[#6B0F1A] font-semibold hover:underline mb-2">
                        ← Kembali ke Kelola Produk
                    </a>
                    <h1 class="text-2xl font-extrabold text-[#5A121A] tracking-tight">Edit Produk: {{ $product->nama_kue }}</h1>
                    <p class="text-xs text-stone-500 mt-0.5">Perbarui informasi kue, harga, stok, atau fotonya.</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-xs">
                    <p class="font-bold mb-1.5">Terdapat kesalahan:</p>
                    <ul class="list-disc list-inside space-y-1 text-rose-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-xs border border-stone-200/60 p-6 sm:p-8">
                <form action="{{ route('admin.produk.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')

                    @php
                        $currentPhoto = $product->foto_url;
                    @endphp

                    <!-- Foto Produk & Live Preview -->
                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-2">Foto Produk</label>
                        <div class="flex items-center gap-5">
                            <div class="w-28 h-28 rounded-2xl overflow-hidden bg-[#FAF2EE] border border-stone-200 shrink-0 flex items-center justify-center relative">
                                <div id="editFallbackIcon" class="w-full h-full p-3 flex items-center justify-center {{ $currentPhoto ? 'hidden' : '' }}">
                                    @foreach ($categories as $cat)
                                        <div data-category-id="{{ $cat->id }}" class="category-svg-item w-full h-full {{ $product->category_id == $cat->id ? '' : 'hidden' }}">
                                            <x-category-icon :category="$cat" class="w-full h-full" />
                                        </div>
                                    @endforeach
                                    <div data-category-id="default" class="category-svg-item w-full h-full hidden">
                                        <x-category-icon category="default" class="w-full h-full" />
                                    </div>
                                </div>
                                <img id="editPreviewImg"
                                     src="{{ $currentPhoto ?? '' }}"
                                     alt="{{ $product->nama_kue }}"
                                     class="w-full h-full object-cover {{ $currentPhoto ? '' : 'hidden' }}">
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <label for="editFotoInput" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold rounded-xl border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 cursor-pointer shadow-xs transition-colors">
                                        <svg class="w-4 h-4 text-[#6B0F1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        <span>Ubah Foto</span>
                                    </label>
                                    <button type="button" id="editCropBtn" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl border border-[#6B0F1A]/20 bg-rose-50 hover:bg-rose-100 text-[#6B0F1A] cursor-pointer shadow-xs transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3v14a2 2 0 002 2h14M17 21v-4a2 2 0 00-2-2H3"/>
                                        </svg>
                                        <span>Atur Posisi & Crop</span>
                                    </button>
                                </div>
                                <input type="file" id="editFotoInput" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden">
                                <input type="hidden" name="cropped_foto_base64" id="editCroppedBase64">
                                <p class="text-[11px] text-stone-400 leading-relaxed">Format: JPG, JPEG, PNG, WEBP (Maksimal 2MB). Biarkan kosong jika tidak ingin mengubah foto.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Nama Produk -->
                    <div>
                        <label for="nama_kue" class="block text-xs font-bold text-stone-700 mb-1">Nama Kue</label>
                        <input type="text"
                               id="nama_kue"
                               name="nama_kue"
                               value="{{ old('nama_kue', $product->nama_kue) }}"
                               required
                               class="w-full text-xs px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A]">
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label for="category_id" class="block text-xs font-bold text-stone-700 mb-1">Kategori</label>
                        <select id="category_id"
                                name="category_id"
                                required
                                class="w-full text-xs px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A]">
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>
                                    {{ $cat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Harga -->
                    <div>
                        <label for="harga" class="block text-xs font-bold text-stone-700 mb-1">Harga (Rp)</label>
                        <input type="number"
                               id="harga"
                               name="harga"
                               value="{{ old('harga', $product->harga) }}"
                               min="0"
                               step="1"
                               required
                               class="w-full text-xs px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A]">
                    </div>

                    <!-- Stok & Status Stok -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="stok" class="block text-xs font-bold text-stone-700 mb-1">Stok</label>
                            <input type="number"
                                   id="stok"
                                   name="stok"
                                   value="{{ old('stok', $product->stok) }}"
                                   min="0"
                                   step="1"
                                   required
                                   class="w-full text-xs px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A]">
                        </div>
                        <div>
                            <label for="status_stok" class="block text-xs font-bold text-stone-700 mb-1">Status Stok</label>
                            <select id="status_stok"
                                    name="status_stok"
                                    required
                                    class="w-full text-xs px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A]">
                                <option value="ready" @selected(old('status_stok', $product->status_stok->value) === 'ready')>Ready</option>
                                <option value="habis" @selected(old('status_stok', $product->status_stok->value) === 'habis')>Habis</option>
                            </select>
                        </div>
                    </div>

                    <!-- Maksimal Beli per Pesanan -->
                    <div>
                        <label for="max_per_order" class="block text-xs font-bold text-stone-700 mb-1">Maksimal Beli per Pesanan</label>
                        <input type="number"
                               id="max_per_order"
                               name="max_per_order"
                               value="{{ old('max_per_order', $product->max_per_order) }}"
                               min="1"
                               class="w-full text-xs px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A]">
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label for="deskripsi" class="block text-xs font-bold text-stone-700 mb-1">Deskripsi</label>
                        <textarea id="deskripsi"
                                  name="deskripsi"
                                  rows="4"
                                  class="w-full text-xs px-4 py-2.5 rounded-xl border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#6B0F1A]/20 focus:border-[#6B0F1A]">{{ old('deskripsi', $product->deskripsi) }}</textarea>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.produk.index') }}"
                           class="px-4 py-2.5 text-xs font-semibold rounded-xl border border-stone-200 hover:bg-stone-50 text-stone-700 transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-6 py-2.5 text-xs font-semibold rounded-xl bg-[#6B0F1A] hover:bg-[#520A13] text-white shadow-xs transition-colors">
                            Perbarui Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categorySelect = document.getElementById('category_id');
            const fallbackContainer = document.getElementById('editFallbackIcon');
            const previewImg = document.getElementById('editPreviewImg');

            function updateCategoryIcon(catId) {
                if (!fallbackContainer) return;
                const items = fallbackContainer.querySelectorAll('.category-svg-item');
                let matched = false;
                items.forEach(item => {
                    if (catId && item.dataset.categoryId === String(catId)) {
                        item.classList.remove('hidden');
                        matched = true;
                    } else {
                        item.classList.add('hidden');
                    }
                });
                if (!matched) {
                    const def = fallbackContainer.querySelector('.category-svg-item[data-category-id="default"]');
                    if (def) def.classList.remove('hidden');
                }
            }

            if (categorySelect) {
                categorySelect.addEventListener('change', function () {
                    if (previewImg && previewImg.classList.contains('hidden')) {
                        updateCategoryIcon(this.value);
                    }
                });
            }
        });
    </script>
</body>
</html>
