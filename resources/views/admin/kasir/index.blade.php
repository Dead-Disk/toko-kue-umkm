<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir / POS Offline - Admin Toko Kue</title>
    @vite('resources/js/app.js')
    <style>
        body {
            font-family: system-ui, sans-serif;
            margin: 2rem auto;
            max-width: 1040px;
            padding: 0 1rem;
            color: #1f2937;
        }
        .layout {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: minmax(0, 1.7fr) minmax(0, 1fr);
            align-items: start;
        }
        @media (max-width: 780px) {
            .layout {
                grid-template-columns: 1fr;
            }
        }
        .card {
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            padding: 1.25rem;
            background: #fff;
        }
        .field {
            display: flex;
            flex-direction: column;
            gap: .35rem;
            margin-bottom: 1rem;
        }
        .field label {
            font-weight: 600;
            font-size: .9rem;
        }
        .field input,
        .field select,
        .field textarea,
        .item-row input,
        .item-row select {
            padding: .55rem .65rem;
            border: 1px solid #d1d5db;
            border-radius: .4rem;
            font: inherit;
            width: 100%;
            box-sizing: border-box;
        }
        .hint {
            font-size: .8rem;
            color: #6b7280;
        }
        .total-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: .5rem;
            padding: 1rem;
            margin: 1rem 0;
        }
        .total-box .amount {
            font-size: 1.75rem;
            font-weight: 700;
            color: #166534;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            font-size: .9rem;
            margin: .35rem 0;
        }
        button {
            font: inherit;
            cursor: pointer;
        }
        .btn-primary {
            background: #16a34a;
            color: #fff;
            border: 0;
            border-radius: .4rem;
            padding: .8rem 1rem;
            font-weight: 700;
            width: 100%;
        }
        .btn-secondary {
            background: #1d4ed8;
            color: #fff;
            border: 0;
            border-radius: .4rem;
            padding: .6rem .9rem;
            font-weight: 600;
        }
        .btn-remove {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            border-radius: .35rem;
            padding: .45rem .6rem;
            font-weight: 700;
        }
        .error {
            color: #b91c1c;
            font-size: .85rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: .9rem;
        }
        th,
        td {
            text-align: left;
            padding: .45rem .4rem;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .item-row td:first-child {
            min-width: 220px;
        }
        .item-row td:nth-child(2) {
            width: 90px;
        }
        .item-row td:nth-child(3) {
            width: 130px;
            white-space: nowrap;
            padding-top: .8rem;
        }
        .item-row td:last-child {
            width: 60px;
        }
        #struk {
            display: none;
            margin-top: 1.5rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: .9rem;
        }
        #struk.is-visible {
            display: block;
        }
        #struk table {
            font-size: .9rem;
        }
        @media print {
            body {
                margin: 0;
                max-width: none;
            }
            .no-print {
                display: none !important;
            }
            #struk {
                display: block !important;
                border: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <h1>Kasir / POS Offline</h1>

        <p>
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> |
            <a href="{{ route('admin.pesanan.index') }}">Pesanan Masuk</a> |
            <a href="{{ route('admin.produk.index') }}">Daftar Produk</a>
        </p>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>

        @if (session('success'))
            <div data-toast-message="{{ session('success') }}" data-toast-type="success" hidden></div>
        @endif

        @if (session('error'))
            <div data-toast-message="{{ session('error') }}" data-toast-type="error" hidden></div>
        @endif
    </div>

    <div class="layout no-print">
        <section class="card">
            <h2>Transaksi Penjualan Tunai</h2>

            @if ($products->isEmpty())
                <p>Belum ada produk dengan stok tersedia. Tambahkan stok produk terlebih dahulu.</p>
            @else
                @php
                    $oldItems = old('items');

                    if (! is_array($oldItems) || $oldItems === []) {
                        $oldItems = [[
                            'product_id' => old('product_id'),
                            'qty' => old('jumlah', 1),
                        ]];
                    }
                @endphp

                <form action="{{ route('admin.kasir.store') }}" method="POST" data-cashier-pos>
                    @csrf

                    @error('items')
                        <p class="error">{{ $message }}</p>
                    @enderror

                    @error('items.*')
                        <p class="error">{{ $message }}</p>
                    @enderror

                    <table>
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody data-cashier-rows>
                            @foreach ($oldItems as $item)
                                <tr class="item-row" data-cashier-row>
                                    <td>
                                        <select name="items[{{ $loop->index }}][product_id]" data-cashier-product required>
                                            <option value="">-- Pilih produk --</option>
                                            @foreach ($products as $product)
                                                <option
                                                    value="{{ $product->id }}"
                                                    data-harga="{{ $product->harga }}"
                                                    data-stok="{{ $product->stok }}"
                                                    data-nama="{{ $product->nama_kue }}"
                                                    @selected((string) ($item['product_id'] ?? '') === (string) $product->id)
                                                >
                                                    {{ $product->nama_kue }} — Rp {{ number_format($product->harga, 0, ',', '.') }} (stok {{ $product->stok }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="hint">
                                            Harga: <span data-cashier-price>-</span> &middot;
                                            Stok: <span data-cashier-stock>-</span>
                                        </div>
                                    </td>
                                    <td>
                                        <input
                                            type="number"
                                            name="items[{{ $loop->index }}][qty]"
                                            min="1"
                                            step="1"
                                            value="{{ $item['qty'] ?? 1 }}"
                                            data-cashier-qty
                                            required
                                        >
                                    </td>
                                    <td><strong data-cashier-subtotal>Rp 0</strong></td>
                                    <td>
                                        <button type="button" class="btn-remove" data-cashier-remove aria-label="Hapus baris">[X]</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <template data-cashier-row-template>
                        <tr class="item-row" data-cashier-row>
                            <td>
                                <select name="items[__INDEX__][product_id]" data-cashier-product required>
                                    <option value="">-- Pilih produk --</option>
                                    @foreach ($products as $product)
                                        <option
                                            value="{{ $product->id }}"
                                            data-harga="{{ $product->harga }}"
                                            data-stok="{{ $product->stok }}"
                                            data-nama="{{ $product->nama_kue }}"
                                        >
                                            {{ $product->nama_kue }} — Rp {{ number_format($product->harga, 0, ',', '.') }} (stok {{ $product->stok }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="hint">
                                    Harga: <span data-cashier-price>-</span> &middot;
                                    Stok: <span data-cashier-stock>-</span>
                                </div>
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="items[__INDEX__][qty]"
                                    min="1"
                                    step="1"
                                    value="1"
                                    data-cashier-qty
                                    required
                                >
                            </td>
                            <td><strong data-cashier-subtotal>Rp 0</strong></td>
                            <td>
                                <button type="button" class="btn-remove" data-cashier-remove aria-label="Hapus baris">[X]</button>
                            </td>
                        </tr>
                    </template>

                    <p>
                        <button type="button" class="btn-secondary" data-cashier-add>+ Tambah Produk</button>
                    </p>

                    <div class="field">
                        <label for="nama_pemesan">Nama Pembeli (opsional)</label>
                        <input type="text" name="nama_pemesan" id="nama_pemesan" maxlength="255" value="{{ old('nama_pemesan') }}">
                    </div>

                    <div class="field">
                        <label for="uang_diterima">Uang Diterima (opsional)</label>
                        <input
                            type="number"
                            name="uang_diterima"
                            id="uang_diterima"
                            min="0"
                            step="1"
                            value="{{ old('uang_diterima') }}"
                            data-cashier-paid
                        >
                        <span class="hint">Isi untuk menghitung kembalian secara otomatis.</span>
                    </div>

                    <div class="field">
                        <label for="catatan">Catatan (opsional)</label>
                        <textarea name="catatan" id="catatan" rows="2" maxlength="1000">{{ old('catatan') }}</textarea>
                    </div>

                    <div class="total-box">
                        <div class="summary-row">
                            <span>Kembalian</span>
                            <strong data-cashier-change>-</strong>
                        </div>
                        <p style="margin:.75rem 0 .25rem">Total Belanja</p>
                        <div class="amount" data-cashier-total>Rp 0</div>
                    </div>

                    <button type="submit" class="btn-primary">Proses Pembayaran Tunai &amp; Cetak Struk / Selesai</button>
                </form>
            @endif
        </section>

        <section class="card">
            <h2>Daftar Produk Tersedia</h2>

            @if ($products->isEmpty())
                <p>Tidak ada produk ready dengan stok lebih dari nol.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->nama_kue }}</td>
                                <td>Rp {{ number_format($product->harga, 0, ',', '.') }}</td>
                                <td>{{ $product->stok }} pcs</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    </div>

    @if ($receiptOrder)
        <section id="struk" class="card is-visible">
            <h2 style="margin:0 0 .25rem">{{ $storeSetting->store_name ?: 'Toko Kue' }}</h2>

            @if ($storeSetting->store_address)
                <p style="margin:.15rem 0">{{ $storeSetting->store_address }}</p>
            @endif

            @if ($storeSetting->store_phone)
                <p style="margin:.15rem 0">Telp: {{ $storeSetting->store_phone }}</p>
            @endif

            <hr style="border:0;border-top:1px dashed #9ca3af;margin:.75rem 0">

            <p style="margin:.15rem 0">No. Transaksi: {{ $receiptOrder->kode_pesanan }}</p>
            <p style="margin:.15rem 0">Tanggal: {{ $receiptOrder->created_at->format('d-m-Y H:i') }}</p>
            <p style="margin:.15rem 0">Kasir: {{ auth()->user()->name }}</p>
            <p style="margin:.15rem 0">Pembeli: {{ $receiptOrder->nama_pemesan }}</p>

            <hr style="border:0;border-top:1px dashed #9ca3af;margin:.75rem 0">

            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($receiptOrder->items as $item)
                        <tr>
                            <td>{{ $item->product?->nama_kue ?? 'Produk' }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <hr style="border:0;border-top:1px dashed #9ca3af;margin:.75rem 0">

            <div class="summary-row">
                <span>Total</span>
                <strong>Rp {{ number_format($receiptOrder->total_harga, 0, ',', '.') }}</strong>
            </div>

            @if ($receipt['uang_diterima'] !== null)
                <div class="summary-row">
                    <span>Tunai</span>
                    <span>Rp {{ number_format($receipt['uang_diterima'], 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span>Kembalian</span>
                    <span>Rp {{ number_format($receipt['kembalian'], 0, ',', '.') }}</span>
                </div>
            @endif

            <p style="text-align:center;margin-top:1rem">Terima kasih atas pembelian Anda.</p>

            <button type="button" class="btn-secondary no-print" onclick="window.print()">Cetak Struk</button>
            <a href="{{ route('admin.kasir.index') }}" class="no-print"> Transaksi Baru</a>
        </section>
    @endif
</body>
</html>
