<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Opsi Custom Cake - Admin Toko Kue</title>
</head>
<body>
    <h1>Edit Opsi Custom Cake</h1>

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

    <form action="{{ route('admin.pengaturan.update', $cakeOption) }}" method="POST">
        @csrf
        @method('PUT')

        <p>
            <label for="bentuk">Bentuk Kue</label><br>
            <select id="bentuk" name="bentuk" required>
                <option value="">-- Pilih Bentuk --</option>
                @foreach ($bentukOptions as $bentuk)
                    <option value="{{ $bentuk->value }}" @selected(old('bentuk', $cakeOption->bentuk->value) === $bentuk->value)>
                        {{ $bentuk->label() }}
                    </option>
                @endforeach
            </select>
        </p>

        <p>
            <label for="ukuran">Ukuran</label><br>
            <input type="text" id="ukuran" name="ukuran" value="{{ old('ukuran', $cakeOption->ukuran) }}" required>
        </p>

        <p>
            <label for="harga_dasar">Harga Dasar (Rp)</label><br>
            <input type="number" id="harga_dasar" name="harga_dasar" value="{{ old('harga_dasar', $cakeOption->harga_dasar) }}" min="0" step="1" required>
        </p>

        <p>
            <button type="submit">Perbarui Opsi</button>
        </p>
    </form>
</body>
</html>
