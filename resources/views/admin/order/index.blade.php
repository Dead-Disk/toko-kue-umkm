<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Masuk - Admin Toko Kue</title>
    @vite('resources/js/app.js')
</head>
<body>
    <h1>Pesanan Masuk</h1>

    <p>
        <a href="{{ route('admin.dashboard') }}">Dashboard</a> |
        <a href="{{ route('admin.produk.index') }}">Daftar Produk</a> |
        @if (auth()->user()->isAdmin())
            <a href="{{ route('admin.pengaturan.index') }}">Pengaturan Custom Cake</a>
        @endif
    </p>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @if (session('errors'))
        <p style="color:#dc2626">{{ session('errors')->first() }}</p>
    @endif

    @php
        $tabStatus = [
            '' => 'Semua',
            'pending' => 'Pesanan Masuk (Pending)',
            'proses' => 'Dalam Proses',
            'selesai' => 'Selesai',
            'ditolak-batal' => 'Ditolak / Batal',
        ];
    @endphp

    <p>
        @foreach ($tabStatus as $nilai => $label)
            @unless ($loop->first) | @endunless
            @if ($filter === ($nilai ?: null))
                <strong>{{ $label }}</strong>
            @else
                <a href="{{ route('admin.pesanan.index', array_filter(['status' => $nilai, 'start_date' => request('start_date'), 'end_date' => request('end_date')])) }}">{{ $label }}</a>
            @endif
        @endforeach
    </p>

    <form action="{{ route('admin.pesanan.index') }}" method="GET">
        @if ($filter !== null)
            <input type="hidden" name="status" value="{{ $filter }}">
        @endif
        <label for="start_date">Dari tanggal</label>
        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
        <label for="end_date">Sampai tanggal</label>
        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
        <button type="submit">Filter</button>
        <a href="{{ route('admin.pesanan.index', $filter !== null ? ['status' => $filter] : []) }}">Reset</a>
    </form>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Pesanan</th>
                <th>Waktu Pesanan</th>
                <th>Nama Pemesan</th>
                <th>Nomor WA</th>
                <th>Tipe</th>
                <th>Jumlah Item</th>
                <th>Total Harga (Rp)</th>
                <th>Foto Referensi</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $order->kode_pesanan }}</td>
                    <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
                    <td>{{ $order->nama_pemesan }}</td>
                    <td>{{ $order->nomor_wa }}</td>
                    <td>{{ $order->tipe_pesanan->value }}</td>
                    <td>
                        @php($totalJumlah = $order->items->sum('jumlah'))
                        {{ number_format($totalJumlah, 0, ',', '.') }} Pcs
                        @if ($order->items_count > 1)
                            <small>({{ $order->items_count }} jenis)</small>
                        @endif
                    </td>
                    <td>{{ $order->total_harga }}</td>
                    <td>
                        @php($foto = $order->items->first()?->foto_referensi)
                        @if ($foto)
                            <a href="{{ asset('storage/'.$foto) }}">
                                <img src="{{ asset('storage/'.$foto) }}" alt="Foto Referensi" width="80">
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $order->status->value }}</td>
                    <td>
                        @switch($order->status->value)
                            @case('pending')
                                <form action="{{ route('admin.pesanan.update-status', $order) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" name="status" value="proses" style="background:#16a34a;color:#fff">Terima</button>
                                    <button type="submit" name="status" value="ditolak" style="background:#dc2626;color:#fff">Tolak</button>
                                </form>
                                @break
                            @case('proses')
                                <form action="{{ route('admin.pesanan.update-status', $order) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" name="status" value="selesai" style="background:#2563eb;color:#fff">Selesaikan Pesanan</button>
                                    <button type="submit" name="status" value="batal" style="background:#6b7280;color:#fff">Batal</button>
                                </form>
                                @break
                            @default
                                <span>{{ $order->status->value }}</span>
                                @if (auth()->user()->isAdmin())
                                    <form action="{{ route('admin.pesanan.destroy', $order) }}" method="POST" style="display:inline" data-confirm="Yakin ingin menghapus pesanan ini?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background:#dc2626;color:#fff">Hapus</button>
                                    </form>
                                @endif
                        @endswitch
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11">Belum ada pesanan masuk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
