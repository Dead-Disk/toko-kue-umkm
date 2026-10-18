<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Add-on - Admin Toko Kue</title>
</head>
<body>
    <h1>Tambah Add-on</h1>

    <p>
        <a href="{{ route('admin.dashboard') }}">Dashboard</a> |
        <a href="{{ route('admin.pengaturan.index') }}">Pengaturan Custom Cake</a>
    </p>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('admin.pengaturan.addon.store') }}" method="POST">
        @csrf

        <p>
            <label for="nama_addon">Nama Add-on</label><br>
            <input type="text" id="nama_addon" name="nama_addon" value="{{ old('nama_addon') }}" placeholder="contoh: Cokelat Belgium" required>
        </p>

        <p>
            <label for="harga_tambahan">Harga Tambahan (Rp)</label><br>
            <input type="number" id="harga_tambahan" name="harga_tambahan" value="{{ old('harga_tambahan') }}" min="0" step="1" required>
        </p>

        <p>
            <button type="submit">Simpan Add-on</button>
        </p>
    </form>
</body>
</html>
