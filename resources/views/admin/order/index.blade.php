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
                <a href="{{ route('admin.pesanan.index', array_filter(['status' => $nilai, 'type' => $filterTipe?->value, 'start_date' => request('start_date'), 'end_date' => request('end_date')])) }}">{{ $label }}</a>
            @endif
        @endforeach
    </p>

    <p>
        @foreach (['' => 'Semua Tipe', 'reguler' => 'Kue Reguler', 'custom' => 'Custom Cake'] as $nilai => $label)
            @unless ($loop->first) | @endunless
            @if (($filterTipe?->value) === ($nilai ?: null))
                <strong>{{ $label }}</strong>
            @else
                <a href="{{ route('admin.pesanan.index', array_filter(['status' => $filter, 'type' => $nilai, 'start_date' => request('start_date'), 'end_date' => request('end_date')])) }}">{{ $label }}</a>
            @endif
        @endforeach
    </p>

    <form action="{{ route('admin.pesanan.index') }}" method="GET">
        @if ($filter !== null)
            <input type="hidden" name="status" value="{{ $filter }}">
        @endif
        @if ($filterTipe !== null)
            <input type="hidden" name="type" value="{{ $filterTipe->value }}">
        @endif
        <label for="start_date">Dari tanggal</label>
        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
        <label for="end_date">Sampai tanggal</label>
        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
        <button type="submit">Filter</button>
        <a href="{{ route('admin.pesanan.index', array_filter(['status' => $filter, 'type' => $filterTipe?->value])) }}">Reset</a>
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
                <th>Bukti Pembayaran</th>
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
                    <td>
                        @if ($order->tipe_pesanan === \App\Enums\TipePesanan::Custom)
                            <span style="background:#f59e0b;color:#fff;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:bold">Custom Cake</span>
                        @else
                            <span style="background:#dbeafe;color:#1e40af;padding:3px 10px;border-radius:12px;font-size:12px">Kue Jadi</span>
                        @endif
                    </td>
                    <td>
                        @php($totalJumlah = $order->items->sum('jumlah'))
                        {{ number_format($totalJumlah, 0, ',', '.') }} Pcs ({{ $order->items_count }} jenis)
                        <ul class="text-xs" style="margin:4px 0 0;padding-left:16px">
                            @foreach ($order->items as $item)
                                <li>
                                    {{ $item->jumlah }}x
                                    {{ $item->product?->nama_kue ?? ($item->deskripsi_custom ? \Illuminate\Support\Str::limit($item->deskripsi_custom, 40) : 'Custom Cake') }}
                                </li>
                            @endforeach
                        </ul>
                        @if ($order->catatan)
                            <div>
                                <strong>Catatan:</strong> {{ $order->catatan }}
                            </div>
                        @endif
                    </td>
                    <td>{{ $order->total_harga }}</td>
                    <td>
                        @php($foto = $order->items->first()?->foto_referensi)
                        @if ($foto)
                            @if ($order->tipe_pesanan === \App\Enums\TipePesanan::Custom)
                                <details>
                                    <summary style="cursor:pointer"><img src="{{ route('admin.orders.custom_photo', $order) }}" alt="Foto Referensi" width="80"></summary>
                                    <img src="{{ route('admin.orders.custom_photo', $order) }}" alt="Foto Referensi" width="320" style="margin-top:4px">
                                    <br>
                                    <a href="{{ route('admin.orders.custom_photo', $order) }}" target="_blank">Buka di tab baru</a>
                                </details>
                            @else
                                <a href="{{ route('admin.orders.custom_photo', $order) }}">
                                    <img src="{{ route('admin.orders.custom_photo', $order) }}" alt="Foto Referensi" width="80">
                                </a>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($order->bukti_pembayaran)
                            <details>
                                <summary style="cursor:pointer"><img src="{{ route('admin.orders.bukti', $order) }}" alt="Bukti Pembayaran" width="80"></summary>
                                <img src="{{ route('admin.orders.bukti', $order) }}" alt="Bukti Pembayaran" width="320" style="margin-top:4px">
                                <br>
                                <a href="{{ route('admin.orders.bukti', $order) }}" target="_blank">Buka di tab baru</a>
                            </details>
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
