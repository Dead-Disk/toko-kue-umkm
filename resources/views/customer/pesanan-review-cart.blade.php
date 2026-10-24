<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Pesanan - Toko Kue</title>
    @vite('resources/js/app.js')
</head>
<body>
    <h1>Review Pesanan</h1>

    <p>Periksa kembali isi keranjang dan lengkapi data pengiriman sebelum mengirim pesanan.</p>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Nama Kue</th>
                <th>Harga Satuan</th>
                <th>Kuantitas</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cart as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>Rp{{ number_format($item['price'], 0, ',', '.') }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>Rp{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <th colspan="3" align="right">Total Harga</th>
                <td>Rp{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if (session('error'))
        <div data-toast-message="{{ session('error') }}" data-toast-type="error" hidden></div>
    @endif

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                @continue(session('error') === $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <h2>Data Pemesan</h2>

    <form action="{{ route('customer.pesanan.checkout') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="checkout_mode" value="cart">

        <p>
            <label for="nama_pemesan">Nama Pemesan</label><br>
            <input type="text" id="nama_pemesan" name="nama_pemesan" value="{{ old('nama_pemesan') }}" required>
        </p>

        <p>
            <label for="alamat_lengkap">Alamat Lengkap</label><br>
            <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" cols="40" required>{{ old('alamat_lengkap') }}</textarea>
        </p>

        <p>
            <label for="nomor_wa">No HP / WA</label><br>
            <input type="tel" id="nomor_wa" name="nomor_wa" value="{{ old('nomor_wa') }}" placeholder="08xxxxxxxxxx" required>
        </p>

        <fieldset>
            <legend>Opsi Pengiriman</legend>
            <label>
                <input type="radio" name="opsi_pengiriman" value="kurir_toko" @checked(old('opsi_pengiriman', 'kurir_toko') === 'kurir_toko')>
                Diantar Kurir
            </label>
            <label>
                <input type="radio" name="opsi_pengiriman" value="ambil_sendiri" @checked(old('opsi_pengiriman') === 'ambil_sendiri')>
                Ambil Sendiri
            </label>
        </fieldset>

        @php
            $bankSiap = $storeSetting->hasBankDetails();
            $qrisSiap = $storeSetting->hasQrisImage();
            $cashSelected = old('metode_pembayaran') === 'cash';
        @endphp

        <div data-payment-group>
            <fieldset>
                <legend>Metode Pembayaran</legend>
                <label>
                    <input type="radio" name="metode_pembayaran" value="transfer_bank" @checked($bankSiap && old('metode_pembayaran', 'transfer_bank') === 'transfer_bank') @disabled(! $bankSiap)>
                    Transfer Bank
                </label>
                @unless ($bankSiap)
                    <small style="color:#b45309">Transfer Bank belum dikonfigurasi oleh penjual.</small>
                @endunless
                <br>
                <label>
                    <input type="radio" name="metode_pembayaran" value="qris" @checked(old('metode_pembayaran') === 'qris') @disabled(! $qrisSiap)>
                    QRIS
                </label>
                @unless ($qrisSiap)
                    <small style="color:#b45309">QRIS belum dikonfigurasi oleh penjual.</small>
                @endunless
                <br>
                <label>
                    <input type="radio" name="metode_pembayaran" value="cash" @checked(old('metode_pembayaran') === 'cash')>
                    Tunai / Bayar di Toko
                </label>
            </fieldset>

            @if ($bankSiap)
                <div data-payment-detail="transfer_bank" hidden>
                    <p>
                        <strong>Transfer Bank:</strong>
                        {{ $storeSetting->bank_name }} - {{ $storeSetting->bank_account_number }}
                        a.n. {{ $storeSetting->bank_account_holder }}
                    </p>
                </div>
            @endif

            @if ($qrisSiap)
                <div data-payment-detail="qris" hidden>
                    <p><strong>Scan QRIS berikut untuk membayar:</strong></p>
                    <img src="{{ asset('storage/'.$storeSetting->qris_image_path) }}" alt="QRIS {{ $storeSetting->store_name }}" width="240">
                </div>
            @endif

            <p data-payment-proof @if ($cashSelected) hidden @endif>
                <label for="bukti_pembayaran">Unggah Bukti Pembayaran (JPG/PNG)</label><br>
                <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*" data-payment-proof-input>
                <br>
                <small>Wajib untuk Transfer Bank dan QRIS.</small>
            </p>

            <p data-payment-cash-info @unless ($cashSelected) hidden @endunless>
                <em>Pembayaran tunai dilakukan secara langsung di toko / saat penerimaan kue.</em>
            </p>
        </div>

        <p>
            <label for="catatan">Catatan Khusus (opsional)</label><br>
            <textarea id="catatan" name="catatan" rows="3" cols="40" placeholder="Catatan khusus (misal: tulisan ucapan, ucapan di dus, lilin, dll)">{{ old('catatan', session('cart_note')) }}</textarea>
        </p>

        <p>
            <button type="submit">Konfirmasi &amp; Kirim Pesanan</button>
        </p>
    </form>

    <p><a href="{{ route('cart.index') }}">Kembali ke Keranjang</a></p>
</body>
</html>
