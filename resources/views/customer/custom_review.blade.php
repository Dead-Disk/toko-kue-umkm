<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Pesanan Custom - Toko Kue</title>
</head>
<body>
    <h1>Review Pesanan Custom</h1>

    <p>Periksa kembali rincian pesanan kamu sebelum dikirim ke penjual.</p>

    <table border="1" cellpadding="6" cellspacing="0">
        <tr>
            <th align="left">Nama Pemesan</th>
            <td>{{ $draft['nama_pemesan'] }}</td>
        </tr>
        <tr>
            <th align="left">No WA</th>
            <td>{{ $draft['nomor_wa'] }}</td>
        </tr>
        <tr>
            <th align="left">Alamat Lengkap</th>
            <td>{{ $draft['alamat_lengkap'] }}</td>
        </tr>
        <tr>
            <th align="left">Opsi Pengiriman</th>
            <td>{{ $draft['opsi_pengiriman'] === 'kurir_toko' ? 'Kurir Toko' : 'Ambil Sendiri' }}</td>
        </tr>
        <tr>
            <th align="left">Deskripsi Request</th>
            <td>{{ $draft['deskripsi_custom'] }}</td>
        </tr>
        <tr>
            <th align="left">Ukuran Kue</th>
            <td>{{ $cakeOption->label() }}</td>
        </tr>
        <tr>
            <th align="left">Harga Dasar per Kue</th>
            <td>Rp {{ number_format($cakeOption->harga_dasar, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th align="left">Jumlah Beli (Pcs / Biji)</th>
            <td>{{ $draft['jumlah'] }}</td>
        </tr>
        <tr>
            <th align="left">Estimasi Harga</th>
            <td>Rp {{ number_format($estimasiHarga, 0, ',', '.') }} <small>(Harga Dasar &times; Jumlah)</small></td>
        </tr>
        <tr>
            <th align="left">Foto Referensi</th>
            <td>
                @if(session('temp_foto'))
                    <span>Foto referensi sudah diunggah.</span>
                @else
                    <span>Tidak ada foto referensi</span>
                @endif
            </td>
        </tr>
        <tr>
            <th align="left">Metode Pembayaran</th>
            <td>{{ \App\Enums\MetodePembayaran::from($draft['metode_pembayaran'])->label() }}</td>
        </tr>
    </table>

    <p><em>Estimasi harga dapat berbeda setelah penjual mengonfirmasi detail desain.</em></p>

    <p>
        <a href="{{ route('customer.custom.create') }}">Kembali / Edit Pesanan</a>
    </p>

    <form action="{{ route('customer.custom.store') }}" method="POST">
        @csrf
        <button type="submit">Konfirmasi &amp; Pesan Sekarang</button>
    </form>
</body>
</html>
