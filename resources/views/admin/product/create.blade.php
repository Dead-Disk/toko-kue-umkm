<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Admin Toko Kue</title>
</head>
<body>
    <h1>Tambah Produk</h1>

    <p>
        <a href="{{ route('admin.dashboard') }}">Dashboard</a> |
        <a href="{{ route('admin.produk.index') }}">Daftar Produk</a>
    </p>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <p>
            <label for="nama_kue">Nama Kue</label><br>
            <input type="text" id="nama_kue" name="nama_kue" value="{{ old('nama_kue') }}" required>
        </p>

        <p>
            <label for="category_id">Kategori</label><br>
            <select id="category_id" name="category_id" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>
                        {{ $cat->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </p>

        <p>
            <label for="deskripsi">Deskripsi</label><br>
            <textarea id="deskripsi" name="deskripsi" rows="5" cols="40">{{ old('deskripsi') }}</textarea>
        </p>

        <p>
            <label for="harga">Harga (Rp)</label><br>
            <input type="number" id="harga" name="harga" value="{{ old('harga') }}" min="0" step="1" required>
        </p>

        <p>
            <label for="stok">Stok</label><br>
            <input type="number" id="stok" name="stok" value="{{ old('stok', 0) }}" min="0" step="1" required>
        </p>

        <p>
            <label for="status_stok">Status Stok</label><br>
            <select id="status_stok" name="status_stok" required>
                <option value="ready" @selected(old('status_stok', 'ready') === 'ready')>Ready</option>
                <option value="habis" @selected(old('status_stok') === 'habis')>Habis</option>
            </select>
        </p>

        <p>
            <label for="foto">Foto Kue</label><br>
            <input type="file" id="foto" name="foto" accept="image/*">
        </p>

        <p>
            <button type="submit">Simpan Produk</button>
        </p>
    </form>
</body>
</html>
