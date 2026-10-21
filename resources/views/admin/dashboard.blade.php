<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penjual - Toko Kue</title>
</head>
<body>
    <h1>Dashboard Penjual</h1>

    <p>Selamat datang, {{ auth()->user()->name }} ({{ auth()->user()->username }}).</p>

    <ul>
        <li><a href="{{ route('admin.produk.index') }}">Kelola Produk</a></li>
        <li><a href="{{ route('admin.pesanan.index') }}">Pesanan Masuk</a></li>
        @if (auth()->user()->isAdmin())
            <li><a href="{{ route('admin.settings.index') }}">Pengaturan Toko</a></li>
            <li><a href="{{ route('admin.pengaturan.index') }}">Pengaturan Custom Cake</a></li>
            <li><a href="{{ route('admin.users.index') }}">Kelola User / Staf</a></li>
        @endif
        <li><a href="{{ route('customer.index') }}">Lihat Web Pembeli</a></li>
    </ul>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>
