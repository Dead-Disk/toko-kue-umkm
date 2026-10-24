<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk Kue - Toko Kue</title>
    @vite('resources/js/app.js')
</head>
<body>
    <h1>Katalog Produk Kue</h1>

    <nav>
        <a href="{{ route('customer.index') }}">Beranda</a> |
        <a href="{{ route('customer.produk.index') }}">Produk</a> |
        <a href="{{ route('cart.index') }}">Keranjang</a> |
        <a href="{{ route('customer.page', 'tentang-kami') }}">Tentang Kami</a> |
        <a href="{{ route('customer.custom.create') }}">Pesan Custom Cake</a>
    </nav>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                @continue(session('error') === $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @if (session('error'))
        <div data-toast-message="{{ session('error') }}" data-toast-type="error" hidden></div>
    @endif

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kue</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nama_kue }}</td>
                    <td>{{ $item->category?->nama_kategori ?? '-' }}</td>
                    <td>Rp{{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td>
                        @if ($item->status_stok === \App\Enums\StatusStok::Ready)
                            Ready ({{ $item->stok }})
                        @else
                            Habis
                        @endif
                    </td>
                    <td>
                        @if ($item->status_stok === \App\Enums\StatusStok::Ready)
                            <a href="{{ route('customer.produk.show', $item) }}">Pesan</a>
                        @else
                            <button type="button" disabled>Pesan (Habis)</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Belum ada produk yang tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @isset($product)
        @php
            $maxPerOrder = $product->max_per_order ?? 5;
        @endphp

        <h2>Detail Produk</h2>

        <table border="1" cellpadding="6" cellspacing="0">
            <tr>
                <th align="left">Nama Kue</th>
                <td>{{ $product->nama_kue }}</td>
            </tr>
            <tr>
                <th align="left">Kategori</th>
                <td>{{ $product->category?->nama_kategori ?? '-' }}</td>
            </tr>
            <tr>
                <th align="left">Deskripsi</th>
                <td>{{ $product->deskripsi ?? '-' }}</td>
            </tr>
            <tr>
                <th align="left">Harga</th>
                <td>Rp{{ number_format($product->harga, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th align="left">Stok</th>
                <td>
                    @if ($product->status_stok === \App\Enums\StatusStok::Ready)
                        Ready ({{ $product->stok }})
                    @else
                        Habis
                    @endif
                </td>
            </tr>
            @if ($product->foto)
                <tr>
                    <th align="left">Foto</th>
                    <td><a href="{{ asset('storage/'.$product->foto) }}">Lihat Foto</a></td>
                </tr>
            @endif
        </table>

        @if ($product->status_stok === \App\Enums\StatusStok::Ready)
            <h2>Tambah ke Keranjang</h2>

            <form action="{{ route('cart.add', $product) }}" method="POST">
                @csrf

                <p>
                    <label for="cart_quantity">Jumlah</label><br>
                    <input type="number" id="cart_quantity" name="quantity" value="1" min="1" max="{{ $maxPerOrder }}" data-max-per-order="{{ $maxPerOrder }}" data-max-order-message="Produk ini hanya dapat dipesan maksimal {{ $maxPerOrder }} pcs per transaksi." required>
                </p>

                <p>
                    <label for="cart_catatan">Catatan Khusus (opsional)</label><br>
                    <textarea id="cart_catatan" name="catatan" rows="3" cols="40" placeholder="Catatan khusus (misal: tulisan ucapan, ucapan di dus, lilin, dll)">{{ old('catatan') }}</textarea>
                </p>

                <p>
                    <button type="submit">Tambah ke Keranjang</button>
                </p>
            </form>

            <h2>Form Pesanan</h2>

            <form action="{{ route('customer.pesanan.review') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <p>
                    <label for="nama_pemesan">Nama Pemesan</label><br>
                    <input type="text" id="nama_pemesan" name="nama_pemesan" value="{{ old('nama_pemesan', $draft['nama_pemesan'] ?? '') }}" required>
                </p>

                <p>
                    <label for="alamat_lengkap">Alamat Lengkap</label><br>
                    <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" cols="40" required>{{ old('alamat_lengkap', $draft['alamat_lengkap'] ?? '') }}</textarea>
                </p>

                <p>
                    <label for="nomor_wa">No HP / WA</label><br>
                    <input type="tel" id="nomor_wa" name="nomor_wa" value="{{ old('nomor_wa', $draft['nomor_wa'] ?? '') }}" placeholder="08xxxxxxxxxx" required>
                </p>

                <p>
                    <label for="jumlah">Jumlah Beli</label><br>
                    <input type="number" id="jumlah" name="jumlah" value="{{ old('jumlah', $draft['jumlah'] ?? 1) }}" min="1" max="{{ $maxPerOrder }}" data-max-per-order="{{ $maxPerOrder }}" data-max-order-message="Produk ini hanya dapat dipesan maksimal {{ $maxPerOrder }} pcs per transaksi." required>
                </p>

                <fieldset>
                    <legend>Opsi Pengiriman</legend>
                    <label>
                        <input type="radio" name="opsi_pengiriman" value="kurir_toko" @checked(old('opsi_pengiriman', $draft['opsi_pengiriman'] ?? 'kurir_toko') === 'kurir_toko')>
                        Diantar Kurir
                    </label>
                    <label>
                        <input type="radio" name="opsi_pengiriman" value="ambil_sendiri" @checked(old('opsi_pengiriman', $draft['opsi_pengiriman'] ?? '') === 'ambil_sendiri')>
                        Ambil Sendiri
                    </label>
                </fieldset>

                @php
                    $bankSiap = $storeSetting->hasBankDetails();
                    $qrisSiap = $storeSetting->hasQrisImage();
                @endphp

                <div data-payment-group>
                    <fieldset>
                        <legend>Metode Pembayaran</legend>
                        <label>
                            <input type="radio" name="metode_pembayaran" value="transfer_bank" @checked($bankSiap && old('metode_pembayaran', $draft['metode_pembayaran'] ?? 'transfer_bank') === 'transfer_bank') @disabled(! $bankSiap)>
                            Transfer Bank
                        </label>
                        @unless ($bankSiap)
                            <small style="color:#b45309">Transfer Bank belum dikonfigurasi oleh penjual.</small>
                        @endunless
                        <br>
                        <label>
                            <input type="radio" name="metode_pembayaran" value="qris" @checked(old('metode_pembayaran', $draft['metode_pembayaran'] ?? '') === 'qris') @disabled(! $qrisSiap)>
                            QRIS
                        </label>
                        @unless ($qrisSiap)
                            <small style="color:#b45309">QRIS belum dikonfigurasi oleh penjual.</small>
                        @endunless
                        <br>
                        <label>
                            <input type="radio" name="metode_pembayaran" value="cash" @checked(old('metode_pembayaran', $draft['metode_pembayaran'] ?? '') === 'cash')>
                            Tunai / Bayar di Toko
                        </label>
                    </fieldset>

                    @if ($bankSiap)
                        <div data-payment-detail="transfer_bank" hidden>
                            <p>
                                <strong>Transfer Bank:</strong>
                                {{ $storeSetting->bank_name }} - {{ $storeSetting->bank_account_number }}
                                a.n. {{ $storeSetting->bank_account_holder }}
                            </p>
                        </div>
                    @endif

                    @if ($qrisSiap)
                        <div data-payment-detail="qris" hidden>
                            <p><strong>Scan QRIS berikut untuk membayar:</strong></p>
                            <img src="{{ asset('storage/'.$storeSetting->qris_image_path) }}" alt="QRIS {{ $storeSetting->store_name }}" width="240">
                        </div>
                    @endif
                </div>

                <p>
                    <label for="catatan">Catatan Khusus (opsional)</label><br>
                    <textarea id="catatan" name="catatan" rows="3" cols="40" placeholder="Catatan khusus (misal: tulisan ucapan, ucapan di dus, lilin, dll)">{{ old('catatan', $draft['catatan'] ?? '') }}</textarea>
                </p>

                <p>
                    <button type="submit">Lanjut ke Ringkasan Pesanan</button>
                </p>
            </form>
        @else
            <p>Maaf, produk ini sedang habis dan belum bisa dipesan.</p>
        @endif
    @endisset
</body>
</html>
