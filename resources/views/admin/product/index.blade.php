<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk - Admin Toko Kue</title>
</head>
<body>
    <h1>Daftar Produk</h1>

    <p>
        <a href="{{ route('admin.dashboard') }}">Dashboard</a> |
        <a href="{{ route('admin.pesanan.index') }}">Pesanan Masuk</a> |
        <a href="{{ route('admin.pengaturan.index') }}">Pengaturan Custom Cake</a>
    </p>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <p><a href="{{ route('admin.produk.create') }}">Tambah Produk</a></p>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kue</th>
                <th>Kategori</th>
                <th>Harga (Rp)</th>
                <th>Stok</th>
                <th>Status Stok</th>
                <th>Foto</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $product->nama_kue }}</td>
                    <td>{{ $product->category?->nama_kategori }}</td>
                    <td>{{ $product->harga }}</td>
                    <td>{{ $product->stok }}</td>
                    <td>{{ $product->status_stok->value }}</td>
                    <td>
                        @if ($product->foto)
                            <a href="{{ asset('storage/'.$product->foto) }}">Lihat Foto</a>
                        @else
                            Tidak ada
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.produk.edit', $product) }}">Edit</a>
                        <form action="{{ route('admin.produk.destroy', $product) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Belum ada produk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
