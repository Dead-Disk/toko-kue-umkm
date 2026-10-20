<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Pesanan - Toko Kue</title>
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

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <h2>Data Pemesan</h2>

    <form action="{{ route('customer.pesanan.checkout') }}" method="POST">
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

        <fieldset>
            <legend>Metode Pembayaran</legend>
            <label>
                <input type="radio" name="metode_pembayaran" value="transfer_bank" @checked(old('metode_pembayaran', 'transfer_bank') === 'transfer_bank')>
                Transfer Bank
            </label>
            <label>
                <input type="radio" name="metode_pembayaran" value="qris" @checked(old('metode_pembayaran') === 'qris')>
                QRIS
            </label>
            <label>
                <input type="radio" name="metode_pembayaran" value="cash" @checked(old('metode_pembayaran') === 'cash')>
                Tunai / Bayar di Toko
            </label>
        </fieldset>

        <p>
            <button type="submit">Konfirmasi &amp; Kirim Pesanan</button>
        </p>
    </form>

    <p><a href="{{ route('cart.index') }}">Kembali ke Keranjang</a></p>
</body>
</html>
