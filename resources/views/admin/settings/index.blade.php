<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Toko - Admin Toko Kue</title>
</head>
<body>
    <h1>Pengaturan Toko</h1>

    <p>
        <a href="{{ route('admin.dashboard') }}">Dashboard</a> |
        <a href="{{ route('admin.produk.index') }}">Daftar Produk</a> |
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

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <h2>Profil Toko</h2>

        <p>
            <label for="store_name">Nama Toko</label><br>
            <input type="text" id="store_name" name="store_name" value="{{ old('store_name', $setting->store_name) }}" required>
        </p>

        <p>
            <label for="store_description">Deskripsi Toko</label><br>
            <textarea id="store_description" name="store_description" rows="3" cols="40">{{ old('store_description', $setting->store_description) }}</textarea>
        </p>

        <p>
            <label for="store_address">Alamat Toko</label><br>
            <input type="text" id="store_address" name="store_address" value="{{ old('store_address', $setting->store_address) }}">
        </p>

        <p>
            <label for="store_phone">No HP / WA Toko</label><br>
            <input type="tel" id="store_phone" name="store_phone" value="{{ old('store_phone', $setting->store_phone) }}" placeholder="08xxxxxxxxxx">
        </p>

        <h2>Rekening Bank (Transfer Bank)</h2>

        <p>
            <label for="bank_name">Nama Bank</label><br>
            <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $setting->bank_name) }}" placeholder="BNI / BCA">
        </p>

        <p>
            <label for="bank_account_number">Nomor Rekening</label><br>
            <input type="text" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $setting->bank_account_number) }}">
        </p>

        <p>
            <label for="bank_account_holder">Nama Pemilik Rekening</label><br>
            <input type="text" id="bank_account_holder" name="bank_account_holder" value="{{ old('bank_account_holder', $setting->bank_account_holder) }}">
        </p>

        @unless ($setting->hasBankDetails())
            <p style="color:#b45309">
                Transfer Bank belum aktif di halaman pembeli sampai nama bank, nomor rekening, dan nama pemilik rekening diisi.
            </p>
        @endunless

        <h2>QRIS</h2>

        @if ($setting->hasQrisImage())
            <p>
                <img src="{{ asset('storage/'.$setting->qris_image_path) }}" alt="QRIS {{ $setting->store_name }}" width="240">
            </p>
            <p>
                <label>
                    <input type="checkbox" name="hapus_qris" value="1">
                    Hapus gambar QRIS saat menyimpan
                </label>
            </p>
        @else
            <p style="color:#b45309">
                QRIS belum aktif di halaman pembeli sampai gambar QRIS diunggah.
            </p>
        @endif

        <p>
            <label for="qris_image">Gambar QRIS</label><br>
            <input type="file" id="qris_image" name="qris_image" accept="image/jpeg,image/png,image/webp">
            <br>
            <small>Format JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
        </p>

        <p>
            <button type="submit">Simpan Pengaturan</button>
        </p>
    </form>
</body>
</html>
