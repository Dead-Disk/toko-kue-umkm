<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Toko Kue</title>
</head>
<body>
    <h1>Selamat Datang di Toko Kue</h1>

    <nav>
        <a href="{{ route('customer.index') }}">Beranda</a> |
        <a href="{{ route('customer.produk.index') }}">Produk</a> |
        <a href="{{ route('customer.page', 'tentang-kami') }}">Tentang Kami</a>
    </nav>

    <p>Silakan pilih menu di atas untuk melihat katalog kue dan informasi toko.</p>

    <h2>Kue Terlaris / Favorit</h2>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Foto Kue</th>
                <th>Nama Kue</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($topProducts as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if ($item->foto)
                            <img src="{{ asset('storage/'.$item->foto) }}" alt="{{ $item->nama_kue }}" width="80">
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $item->nama_kue }}</td>
                    <td>{{ $item->category?->nama_kategori ?? '-' }}</td>
                    <td>Rp{{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td><a href="{{ route('customer.produk.show', $item) }}">Lihat Detail</a></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Belum ada produk kue.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
