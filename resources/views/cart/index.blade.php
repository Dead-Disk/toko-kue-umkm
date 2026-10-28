<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Toko Kue</title>
    @vite('resources/js/app.js')
</head>
<body>
    <h1>Keranjang Belanja</h1>

    <nav>
        <a href="{{ route('customer.index') }}">Beranda</a> |
        <a href="{{ route('customer.produk.index') }}">Produk</a> |
        <a href="{{ route('customer.page', 'tentang-kami') }}">Tentang Kami</a> |
        <a href="{{ route('customer.custom.create') }}">Pesan Custom Cake</a>
    </nav>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <div data-toast-message="{{ session('error') }}" data-toast-type="error" hidden></div>
    @endif

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                @continue(session('error') === $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @if (empty($cart))
        <p>Keranjang belanja Anda masih kosong.</p>

        <p><a href="{{ route('customer.produk.index') }}">Kembali Belanja</a></p>
    @else
        <table border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kue</th>
                    <th>Catatan per Kue</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cart as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if ($item['image_url'])
                                <a href="{{ $item['image_url'] }}">{{ $item['name'] }}</a>
                            @else
                                {{ $item['name'] }}
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('cart.update', $item['id']) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="quantity" value="{{ $item['quantity'] }}">
                                <textarea name="catatan" rows="2" cols="24" maxlength="1000" aria-label="Catatan untuk {{ $item['name'] }}" placeholder="Catatan untuk {{ $item['name'] }}">{{ $item['catatan'] ?? '' }}</textarea>
                                <br>
                                <button type="submit">Simpan Catatan</button>
                            </form>
                        </td>
                        <td>Rp{{ number_format($item['price'], 0, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('cart.update', $item['id']) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}" @disabled($item['quantity'] <= 1)>-</button>
                                <strong>{{ $item['quantity'] }}</strong>
                                <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}">+</button>
                            </form>
                        </td>
                        <td>Rp{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('cart.remove', $item['id']) }}" method="POST" data-confirm="Hapus produk ini dari keranjang?">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="5" align="right">Grand Total</th>
                    <td colspan="2">Rp{{ number_format($total, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <p>
            <a href="{{ route('customer.pesanan.review-cart') }}">Lanjut ke Review Pesanan</a>
        </p>

        <p><a href="{{ route('customer.produk.index') }}">Kembali Belanja</a></p>

        <form action="{{ route('cart.clear') }}" method="POST" data-confirm="Kosongkan seluruh keranjang?">
            @csrf
            @method('DELETE')
            <button type="submit">Kosongkan Keranjang</button>
        </form>
    @endif
</body>
</html>
