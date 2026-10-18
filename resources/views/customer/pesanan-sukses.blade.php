<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - Toko Kue</title>
</head>
<body>
    <h1>Pesanan Berhasil Dikirim</h1>

    <p>
        Terima kasih, {{ $order->nama_pemesan }}! Pesanan kamu sudah kami terima
        dan saat ini <strong>sedang menunggu konfirmasi penjual</strong>.
    </p>

    <h2>Ringkasan Pesanan</h2>

    <table border="1" cellpadding="6" cellspacing="0">
        <tr>
            <th align="left">Kode Pesanan</th>
            <td>{{ $order->kode_pesanan }}</td>
        </tr>
        <tr>
            <th align="left">Nama Pemesan</th>
            <td>{{ $order->nama_pemesan }}</td>
        </tr>
        <tr>
            <th align="left">No HP / WA</th>
            <td>{{ $order->nomor_wa }}</td>
        </tr>
        <tr>
            <th align="left">Alamat Lengkap</th>
            <td>{{ $order->alamat_lengkap }}</td>
        </tr>
        <tr>
            <th align="left">Opsi Pengiriman</th>
            <td>{{ $order->opsi_pengiriman === \App\Enums\OpsiPengiriman::KurirToko ? 'Diantar Kurir' : 'Ambil Sendiri' }}</td>
        </tr>
        <tr>
            <th align="left">Metode Pembayaran</th>
            <td>{{ $order->metode_pembayaran === \App\Enums\MetodePembayaran::TransferBank ? 'Transfer Bank' : 'QRIS' }}</td>
        </tr>
        <tr>
            <th align="left">Status</th>
            <td>{{ $order->status->value }}</td>
        </tr>
    </table>

    <h2>Rincian Item</h2>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kue</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
                <th>Add-ons</th>
                <th>Foto Referensi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->product?->nama_kue ?? 'Kue Custom' }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    <td>
                        @forelse ($item->addons ?? [] as $addon)
                            {{ $addon['nama_addon'] }} (+Rp{{ number_format($addon['harga_tambahan'], 0, ',', '.') }})<br>
                        @empty
                            -
                        @endforelse
                    </td>
                    <td>
                        @if ($item->foto_referensi)
                            <img src="{{ Storage::disk('public')->url($item->foto_referensi) }}" alt="Foto Referensi" width="100">
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr>
                <th colspan="5" align="right">Total Harga</th>
                <td>Rp{{ number_format($order->total_harga, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <p>
        <a href="{{ route('customer.produk.index') }}">Kembali ke Katalog Produk</a>
    </p>
</body>
</html>
