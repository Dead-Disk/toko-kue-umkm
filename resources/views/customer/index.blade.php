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
</body>
</html>
