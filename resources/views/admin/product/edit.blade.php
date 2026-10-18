<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Admin Toko Kue</title>
</head>
<body>
    <h1>Edit Produk</h1>

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

    <form action="{{ route('admin.produk.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <p>
            <label for="nama_kue">Nama Kue</label><br>
            <input type="text" id="nama_kue" name="nama_kue" value="{{ old('nama_kue', $product->nama_kue) }}" required>
        </p>

        <p>
            <label for="category_id">Kategori</label><br>
            <select id="category_id" name="category_id" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>
                        {{ $cat->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </p>

        <p>
            <label for="deskripsi">Deskripsi</label><br>
            <textarea id="deskripsi" name="deskripsi" rows="5" cols="40">{{ old('deskripsi', $product->deskripsi) }}</textarea>
        </p>

        <p>
            <label for="harga">Harga (Rp)</label><br>
            <input type="number" id="harga" name="harga" value="{{ old('harga', $product->harga) }}" min="0" step="1" required>
        </p>

        <p>
            <label for="stok">Stok</label><br>
            <input type="number" id="stok" name="stok" value="{{ old('stok', $product->stok) }}" min="0" step="1" required>
        </p>

        <p>
            <label for="status_stok">Status Stok</label><br>
            <select id="status_stok" name="status_stok" required>
                <option value="ready" @selected(old('status_stok', $product->status_stok->value) === 'ready')>Ready</option>
                <option value="habis" @selected(old('status_stok', $product->status_stok->value) === 'habis')>Habis</option>
            </select>
        </p>

        <p>
            <label for="foto">Foto Kue</label><br>
            @if ($product->foto)
                <a href="{{ asset('storage/'.$product->foto) }}">Lihat Foto Saat Ini</a><br>
            @endif
            <input type="file" id="foto" name="foto" accept="image/*">
            <br><small>Biarkan kosong bila tidak ingin mengganti foto.</small>
        </p>

        <p>
            <button type="submit">Perbarui Produk</button>
        </p>
    </form>
</body>
</html>
