<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Custom Cake - Toko Kue</title>
</head>
<body>
    <h1>Pesan Custom Cake</h1>

    <p><a href="{{ route('customer.index') }}">Kembali ke Beranda</a></p>

    <form action="{{ route('customer.custom.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if ($errors->any())
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <p>
            <label for="nama_pemesan">Nama Pemesan</label><br>
            <input type="text" id="nama_pemesan" name="nama_pemesan" value="{{ old('nama_pemesan') }}" required>
        </p>

        <p>
            <label for="nomor_wa">Nomor WA</label><br>
            <input type="tel" id="nomor_wa" name="nomor_wa" value="{{ old('nomor_wa') }}" placeholder="08xxxxxxxxxx" required>
        </p>

        <p>
            <label for="alamat_lengkap">Alamat Lengkap</label><br>
            <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" cols="40" placeholder="Isi alamat pengiriman, atau ketik &quot;Ambil di toko&quot; bila ambil sendiri" required>{{ old('alamat_lengkap') }}</textarea>
        </p>

        <p>
            <label for="deskripsi_custom">Deskripsi Request (rasa, tulisan di atas kue, dll.)</label><br>
            <textarea id="deskripsi_custom" name="deskripsi_custom" rows="5" cols="40" required>{{ old('deskripsi_custom') }}</textarea>
        </p>

        <p>
            <label for="ukuran_kue">Ukuran Kue</label><br>
            <input type="text" id="ukuran_kue" name="ukuran_kue" value="{{ old('ukuran_kue') }}" placeholder="contoh: 20 cm" required>
        </p>

        <p>
            <label for="jumlah">Jumlah Beli (Pcs / Biji)</label><br>
            <input type="number" id="jumlah" name="jumlah" value="{{ old('jumlah', 1) }}" min="1" required>
        </p>

        <fieldset>
            <legend>Opsi Pengiriman</legend>
            <label>
                <input type="radio" name="opsi_pengiriman" value="kurir_toko" @checked(old('opsi_pengiriman', 'kurir_toko') === 'kurir_toko')>
                Kurir Toko
            </label>
            <label>
                <input type="radio" name="opsi_pengiriman" value="ambil_sendiri" @checked(old('opsi_pengiriman') === 'ambil_sendiri')>
                Ambil Sendiri
            </label>
        </fieldset>

        <p>
            <label for="metode_pembayaran">Metode Pembayaran</label><br>
            <select id="metode_pembayaran" name="metode_pembayaran" required>
                <option value="">-- Pilih Metode --</option>
                <option value="transfer_bank" @selected(old('metode_pembayaran') === 'transfer_bank')>Transfer Bank</option>
                <option value="qris" @selected(old('metode_pembayaran') === 'qris')>QRIS</option>
            </select>
        </p>

        <p>
            <label for="foto_referensi">Foto Referensi Desain Kue</label><br>
            <input type="file" id="foto_referensi" name="foto_referensi" accept="image/*">
        </p>

        <p>
            <button type="submit">Kirim Pesanan</button>
        </p>
    </form>
</body>
</html>
