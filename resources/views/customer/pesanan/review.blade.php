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
        @php($metodePembayaran = \App\Enums\MetodePembayaran::from($draft['metode_pembayaran']))
        <tr>
            <th align="left">Metode Pembayaran</th>
            <td>
                {{ $metodePembayaran->label() }}
                @if ($metodePembayaran === \App\Enums\MetodePembayaran::TransferBank && $storeSetting->hasBankDetails())
                    <div>
                        Transfer ke {{ $storeSetting->bank_name }} - {{ $storeSetting->bank_account_number }}
                        a.n. {{ $storeSetting->bank_account_holder }}
                    </div>
                @elseif ($metodePembayaran === \App\Enums\MetodePembayaran::Qris && $storeSetting->hasQrisImage())
                    <div>
                        <img src="{{ asset('storage/'.$storeSetting->qris_image_path) }}" alt="QRIS {{ $storeSetting->store_name }}" width="240">
                    </div>
                @endif
            </td>
        </tr>
        @if (! empty($draft['catatan']))
            <tr>
                <th align="left">Catatan</th>
                <td>{{ $draft['catatan'] }}</td>
            </tr>
        @endif
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

    @if ($metodePembayaran === \App\Enums\MetodePembayaran::Cash)
        <p><em>Pembayaran dilakukan secara tunai (Cash) saat pengambilan / pengantaran pesanan.</em></p>
    @else
        <p><em>Silakan lakukan pembayaran sesuai nominal di atas, lalu unggah bukti transfer di bawah ini.</em></p>
    @endif

    <p>
        <a href="{{ route('customer.produk.show', $product) }}">Kembali / Edit Pesanan</a>
    </p>

    <form action="{{ route('customer.pesanan.checkout') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @unless ($metodePembayaran === \App\Enums\MetodePembayaran::Cash)
            <p>
                <label for="bukti_pembayaran">Unggah Bukti Pembayaran (JPG/PNG)</label><br>
                <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*" required>
            </p>
        @endunless

        <button type="submit">Konfirmasi &amp; Kirim Pesanan</button>
    </form>
</body>
</html>
