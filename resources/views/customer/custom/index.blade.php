<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Custom Cake - Toko Kue</title>
</head>
<body>
    <h1>Pesan Custom Cake</h1>

    <p><a href="{{ route('customer.index') }}">Kembali ke Beranda</a></p>

    <form action="{{ route('customer.custom.review') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if ($errors->any())
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <p>
            <label for="nama_pemesan">Nama Pemesan</label><br>
            <input type="text" id="nama_pemesan" name="nama_pemesan" value="{{ old('nama_pemesan', $draft['nama_pemesan'] ?? '') }}" required>
        </p>

        <p>
            <label for="nomor_wa">Nomor WA</label><br>
            <input type="tel" id="nomor_wa" name="nomor_wa" value="{{ old('nomor_wa', $draft['nomor_wa'] ?? '') }}" placeholder="08xxxxxxxxxx" required>
        </p>

        <p>
            <label for="alamat_lengkap">Alamat Lengkap</label><br>
            <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" cols="40" placeholder="Isi alamat pengiriman, atau ketik &quot;Ambil di toko&quot; bila ambil sendiri" required>{{ old('alamat_lengkap', $draft['alamat_lengkap'] ?? '') }}</textarea>
        </p>

        <p>
            <label for="deskripsi_custom">Deskripsi Request (rasa, tulisan di atas kue, dll.)</label><br>
            <textarea id="deskripsi_custom" name="deskripsi_custom" rows="5" cols="40" required>{{ old('deskripsi_custom', $draft['deskripsi_custom'] ?? '') }}</textarea>
        </p>

        <p>
            <label for="base_product_id">Produk Acuan (opsional)</label><br>
            <select id="base_product_id" name="base_product_id">
                <option value="">-- Tanpa Produk Acuan --</option>
                @foreach ($products as $item)
                    <option value="{{ $item->id }}" @selected((string) old('base_product_id', $draft['base_product_id'] ?? '') === (string) $item->id) data-harga="{{ $item->harga }}">
                        {{ $item->nama_kue }} - Rp {{ number_format($item->harga, 0, ',', '.') }}
                    </option>
                @endforeach
            </select>
        </p>

        <p>
            <label for="cake_option_id">Ukuran Kue</label><br>
            <select id="cake_option_id" name="cake_option_id" required>
                <option value="">-- Pilih Ukuran --</option>
                @foreach ($cakeOptions as $option)
                    <option value="{{ $option->id }}" @selected((string) old('cake_option_id', $draft['cake_option_id'] ?? '') === (string) $option->id) data-harga="{{ $option->harga_dasar }}">
                        {{ $option->label() }}
                    </option>
                @endforeach
            </select>
        </p>

        <p>
            <label for="jumlah">Jumlah Beli (Pcs / Biji)</label><br>
            <input type="number" id="jumlah" name="jumlah" value="{{ $jumlah }}" min="1" max="{{ $maxQuantity }}" step="1" data-max-quantity="{{ $maxQuantity }}" data-max-quantity-message="Maksimal pemesanan untuk pesanan ini adalah {{ $maxQuantity }} pcs." required>
        </p>

        <p>
            <strong id="estimasi-total">Estimasi Total: Rp {{ number_format($estimasiHarga, 0, ',', '.') }}</strong>
        </p>

        <fieldset>
            <legend>Opsi Pengiriman</legend>
            <label>
                <input type="radio" name="opsi_pengiriman" value="kurir_toko" @checked(old('opsi_pengiriman', $draft['opsi_pengiriman'] ?? 'kurir_toko') === 'kurir_toko')>
                Kurir Toko
            </label>
            <label>
                <input type="radio" name="opsi_pengiriman" value="ambil_sendiri" @checked(old('opsi_pengiriman', $draft['opsi_pengiriman'] ?? '') === 'ambil_sendiri')>
                Ambil Sendiri
            </label>
        </fieldset>

        <p>
            <label for="metode_pembayaran">Metode Pembayaran</label><br>
            <select id="metode_pembayaran" name="metode_pembayaran" required>
                <option value="">-- Pilih Metode --</option>
                <option value="transfer_bank" @selected(old('metode_pembayaran', $draft['metode_pembayaran'] ?? '') === 'transfer_bank')>Transfer Bank</option>
                <option value="qris" @selected(old('metode_pembayaran', $draft['metode_pembayaran'] ?? '') === 'qris')>QRIS</option>
                <option value="cash" @selected(old('metode_pembayaran', $draft['metode_pembayaran'] ?? '') === 'cash')>Tunai / Bayar di Toko</option>
            </select>
        </p>

        <p>
            <label for="foto_referensi">Foto Referensi Desain Kue</label><br>
            <input type="file" id="foto_referensi" name="foto_referensi" accept="image/*">
            @if (! empty($draft['foto_referensi']))
                <br><small>Foto tersimpan: {{ basename($draft['foto_referensi']) }} (biarkan kosong bila tidak ingin mengganti)</small>
            @endif
        </p>

        <p>
            <button type="submit">Lanjut ke Ringkasan Pesanan</button>
        </p>
    </form>

    <script>
        (() => {
            const baseProduct = document.getElementById('base_product_id');
            const cakeOption = document.getElementById('cake_option_id');
            const quantity = document.getElementById('jumlah');
            const estimate = document.getElementById('estimasi-total');

            if (!baseProduct || !cakeOption || !quantity || !estimate) {
                return;
            }

            const parseInteger = (value) => {
                const parsed = Number.parseInt(value, 10);

                return Number.isNaN(parsed) ? 0 : parsed;
            };

            const maxQuantity = parseInteger(quantity.dataset.maxQuantity) || 5;
            const maxQuantityMessage =
                quantity.dataset.maxQuantityMessage ||
                `Maksimal pemesanan untuk pesanan ini adalah ${maxQuantity} pcs.`;

            const selectedPrice = (select) => {
                const option = select.selectedOptions[0];

                return option ? parseInteger(option.dataset.harga) : 0;
            };

            const formatRupiah = (value) => new Intl.NumberFormat('id-ID').format(value);

            const showToast = (message) => {
                let container = document.querySelector('[data-toast-container]');

                if (!container) {
                    container = document.createElement('div');
                    container.dataset.toastContainer = '';
                    container.style.cssText = 'position:fixed;top:1rem;right:1rem;z-index:9999;display:flex;flex-direction:column;align-items:flex-end;gap:.5rem;pointer-events:none;';
                    document.body.appendChild(container);
                }

                const toast = document.createElement('div');
                toast.setAttribute('role', 'alert');
                toast.setAttribute('aria-live', 'assertive');
                toast.textContent = message;
                toast.style.cssText = 'background:#b91c1c;color:#fff;padding:.75rem 1rem;border-radius:.5rem;box-shadow:0 10px 25px rgba(0,0,0,.18);font:14px/1.45 system-ui,sans-serif;max-width:24rem;pointer-events:auto;';
                container.appendChild(toast);

                window.setTimeout(() => toast.remove(), 5000);
            };

            const updateEstimate = () => {
                const basePrice = selectedPrice(baseProduct);
                const sizePrice = selectedPrice(cakeOption);
                const price = baseProduct.value !== '' ? basePrice : sizePrice;
                const quantityValue = parseInteger(quantity.value);
                const total = price * quantityValue;

                estimate.textContent = `Estimasi Total: Rp ${formatRupiah(total)}`;
            };

            const handleQuantityChange = () => {
                if (parseInteger(quantity.value) > maxQuantity) {
                    quantity.value = String(maxQuantity);
                    showToast(maxQuantityMessage);
                }

                updateEstimate();
            };

            baseProduct.addEventListener('change', updateEstimate);
            cakeOption.addEventListener('change', updateEstimate);

            ['input', 'change', 'keyup'].forEach((eventName) => {
                quantity.addEventListener(eventName, handleQuantityChange);
            });

            quantity.form?.addEventListener('submit', (event) => {
                if (parseInteger(quantity.value) > maxQuantity) {
                    quantity.value = String(maxQuantity);
                    showToast(maxQuantityMessage);
                    updateEstimate();
                    event.preventDefault();
                    quantity.focus();
                }
            });

            handleQuantityChange();
        })();
    </script>
</body>
</html>
