<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Pesanan - Toko Kue</title>
</head>
<body>
    <h1>Ringkasan Pesanan</h1>

    <p>Periksa kembali rincian pesanan kamu sebelum dikirim ke penjual.</p>

    <table border="1" cellpadding="6" cellspacing="0">
        <tr>
            <th align="left">Nama Pemesan</th>
            <td>{{ $draft['nama_pemesan'] }}</td>
        </tr>
        <tr>
            <th align="left">No HP / WA</th>
            <td>{{ $draft['nomor_wa'] }}</td>
        </tr>
        <tr>
            <th align="left">Alamat Lengkap</th>
            <td>{{ $draft['alamat_lengkap'] }}</td>
        </tr>
        <tr>
            <th align="left">Opsi Pengiriman</th>
            <td>{{ $draft['opsi_pengiriman'] === 'kurir_toko' ? 'Diantar Kurir' : 'Ambil Sendiri' }}</td>
        </tr>
        <tr>
            <th align="left">Metode Pembayaran</th>
            <td>{{ \App\Enums\MetodePembayaran::from($draft['metode_pembayaran'])->label() }}</td>
        </tr>
    </table>

    <h2>Detail Produk</h2>

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
            <tr>
                <td>{{ $product->nama_kue }}</td>
                <td>Rp{{ number_format($product->harga, 0, ',', '.') }}</td>
                <td>{{ $draft['jumlah'] }}</td>
                <td>Rp{{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th colspan="3" align="right">Total Harga</th>
                <td>Rp{{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <p><em>Tidak ada pembayaran di halaman ini. Penjual akan mengonfirmasi pesanan dan cara pembayaran.</em></p>

    <p>
        <a href="{{ route('customer.produk.show', $product) }}">Kembali / Edit Pesanan</a>
    </p>

    <form action="{{ route('customer.pesanan.checkout') }}" method="POST">
        @csrf
        <button type="submit">Konfirmasi &amp; Kirim Pesanan</button>
    </form>
</body>
</html>
