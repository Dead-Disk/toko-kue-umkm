<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori Kue - Admin Toko Kue</title>
</head>
<body>
    <h1>Tambah Kategori Kue</h1>

    <p>
        <a href="{{ route('admin.dashboard') }}">Dashboard</a> |
        <a href="{{ route('admin.pengaturan.index') }}">Pengaturan Custom Cake &amp; Kategori</a>
    </p>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('admin.kategori.store') }}" method="POST">
        @csrf

        <p>
            <label for="nama_kategori">Nama Kategori</label><br>
            <input type="text" id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori', old('name')) }}" placeholder="contoh: Kue Kering, Tart Ulang Tahun" required>
        </p>

        <p>
            <button type="submit">Simpan Kategori</button>
        </p>
    </form>
</body>
</html>
