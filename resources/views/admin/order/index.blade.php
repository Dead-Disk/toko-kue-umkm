<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Masuk - Admin Toko Kue</title>
</head>
<body>
    <h1>Pesanan Masuk</h1>

    <p>
        <a href="{{ route('admin.dashboard') }}">Dashboard</a> |
        <a href="{{ route('admin.produk.index') }}">Daftar Produk</a> |
        <a href="{{ route('admin.pengaturan.index') }}">Pengaturan Custom Cake</a>
    </p>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Pesanan</th>
                <th>Nama Pemesan</th>
                <th>Nomor WA</th>
                <th>Tipe</th>
                <th>Jumlah Item</th>
                <th>Total Harga (Rp)</th>
                <th>Foto Referensi</th>
                <th>Status</th>
                <th>Ubah Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $order->kode_pesanan }}</td>
                    <td>{{ $order->nama_pemesan }}</td>
                    <td>{{ $order->nomor_wa }}</td>
                    <td>{{ $order->tipe_pesanan->value }}</td>
                    <td>{{ $order->items_count }}</td>
                    <td>{{ $order->total_harga }}</td>
                    <td>
                        @php($foto = $order->items->first()?->foto_referensi)
                        @if ($foto)
                            <a href="{{ Storage::disk('public')->url($foto) }}">
                                <img src="{{ Storage::disk('public')->url($foto) }}" alt="Foto Referensi" width="80">
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $order->status->value }}</td>
                    <td>
                        <form action="{{ route('admin.pesanan.update-status', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <select name="status" required>
                                <option value="pending" @selected($order->status->value === 'pending')>Pending</option>
                                <option value="diproses" @selected($order->status->value === 'diproses')>Diproses</option>
                                <option value="selesai" @selected($order->status->value === 'selesai')>Selesai</option>
                                <option value="batal" @selected($order->status->value === 'batal')>Batal</option>
                            </select>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">Belum ada pesanan masuk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
