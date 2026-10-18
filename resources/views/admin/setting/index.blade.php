<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Custom Cake - Admin Toko Kue</title>
</head>
<body>
    <h1>Pengaturan Custom Cake</h1>

    <p>
        <a href="{{ route('admin.dashboard') }}">Dashboard</a> |
        <a href="{{ route('admin.produk.index') }}">Daftar Produk</a> |
        <a href="{{ route('admin.pesanan.index') }}">Pesanan Masuk</a>
    </p>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <h2>Kelola Varian Ukuran</h2>

    <p><a href="{{ route('admin.pengaturan.create') }}">Tambah Opsi</a></p>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Bentuk</th>
                <th>Ukuran</th>
                <th>Harga Dasar (Rp)</th>
                <th>Label Opsi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($cakeOptions as $cakeOption)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $cakeOption->bentuk->value }}</td>
                    <td>{{ $cakeOption->ukuran }}</td>
                    <td>{{ $cakeOption->harga_dasar }}</td>
                    <td>{{ $cakeOption->label() }}</td>
                    <td>
                        <a href="{{ route('admin.pengaturan.edit', $cakeOption) }}">Edit</a>
                        <form action="{{ route('admin.pengaturan.destroy', $cakeOption) }}" method="POST" onsubmit="return confirm('Hapus opsi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Belum ada opsi custom cake.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Kelola Add-ons (Varian Tambahan)</h2>

    <p><a href="{{ route('admin.pengaturan.addon.create') }}">Tambah Add-on</a></p>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Add-on</th>
                <th>Harga Tambahan (Rp)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($cakeAddons as $cakeAddon)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $cakeAddon->nama_addon }}</td>
                    <td>{{ $cakeAddon->harga_tambahan }}</td>
                    <td>
                        <a href="{{ route('admin.pengaturan.addon.edit', $cakeAddon) }}">Edit</a>
                        <form action="{{ route('admin.pengaturan.addon.destroy', $cakeAddon) }}" method="POST" onsubmit="return confirm('Hapus add-on ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Belum ada add-on.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
