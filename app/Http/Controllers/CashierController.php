<?php

namespace App\Http\Controllers;

use App\Enums\MetodePembayaran;
use App\Enums\OpsiPengiriman;
use App\Enums\StatusPesanan;
use App\Enums\StatusStok;
use App\Enums\TipePesanan;
use App\Models\Order;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CashierController extends Controller
{
    /**
     * Tampilkan halaman kasir offline: produk ready yang stoknya masih ada,
     * plus struk terakhir bila baru saja menyelesaikan pembayaran tunai.
     */
    public function index(): View
    {
        $products = Product::query()
            ->where('status_stok', StatusStok::Ready)
            ->where('stok', '>', 0)
            ->orderBy('nama_kue')
            ->get();

        $receipt = session('receipt');
        $receipt = is_array($receipt) ? $receipt : null;

        $receiptOrder = $receipt === null
            ? null
            : Order::with(['items.product'])->find($receipt['order_id'] ?? null);

        return view('admin.kasir.index', [
            'products' => $products,
            'storeSetting' => StoreSetting::current(),
            'receipt' => $receipt,
            'receiptOrder' => $receiptOrder,
        ]);
    }

    /**
     * Proses pembayaran tunai langsung di toko untuk satu atau banyak item:
     * catat pesanan berstatus selesai dan potong stok tiap produk sekaligus
     * dalam satu transaksi database.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->normalizeItems($request);

        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', Rule::exists('products', 'id')->withoutTrashed()],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'nama_pemesan' => ['nullable', 'string', 'max:255'],
            'uang_diterima' => ['nullable', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $uangDiterima = $data['uang_diterima'] ?? null;

        try {
            $order = DB::transaction(function () use ($data): Order {
                // Gabungkan baris dengan produk yang sama agar pengecekan stok
                // dan pemotongan stok dihitung dari total kuantitas sebenarnya.
                $items = collect($data['items'])
                    ->map(fn (array $item): array => [
                        'product_id' => (int) $item['product_id'],
                        'qty' => (int) $item['qty'],
                    ])
                    ->groupBy('product_id')
                    ->map(fn ($rows, $productId): array => [
                        'product_id' => (int) $productId,
                        'qty' => (int) $rows->sum('qty'),
                    ])
                    ->values();

                $products = Product::query()
                    ->whereIn('id', $items->pluck('product_id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $baris = [];
                $total = 0;

                foreach ($items as $item) {
                    $product = $products->get($item['product_id']);

                    if ($product === null) {
                        throw ValidationException::withMessages([
                            'items' => 'Salah satu produk tidak tersedia.',
                        ]);
                    }

                    if ($product->status_stok !== StatusStok::Ready) {
                        throw ValidationException::withMessages([
                            'items' => 'Produk '.$product->nama_kue.' sedang ditandai habis.',
                        ]);
                    }

                    if ($product->stok < $item['qty']) {
                        throw ValidationException::withMessages([
                            'items' => 'Stok '.$product->nama_kue.' tidak mencukupi. Sisa stok '.$product->stok.' pcs.',
                        ]);
                    }

                    $subtotal = $product->harga * $item['qty'];
                    $total += $subtotal;
                    $baris[] = [$product, $item['qty'], $subtotal];
                }

                $order = Order::create([
                    'kode_pesanan' => Order::generateKodePesanan(),
                    'tipe_pesanan' => TipePesanan::Reguler,
                    'nama_pemesan' => filled($data['nama_pemesan'] ?? null) ? $data['nama_pemesan'] : 'Pembeli Offline',
                    'nomor_wa' => '-',
                    'alamat_lengkap' => 'Penjualan langsung di toko (kasir offline)',
                    'opsi_pengiriman' => OpsiPengiriman::AmbilSendiri,
                    'metode_pembayaran' => MetodePembayaran::Cash,
                    'total_harga' => $total,
                    'status' => StatusPesanan::Selesai,
                    'catatan' => $data['catatan'] ?? null,
                ]);

                foreach ($baris as [$product, $jumlah, $subtotal]) {
                    $order->items()->create([
                        'product_id' => $product->id,
                        'jumlah' => $jumlah,
                        'subtotal' => $subtotal,
                    ]);

                    $sisaStok = $product->stok - $jumlah;

                    $product->update([
                        'stok' => $sisaStok,
                        'status_stok' => $sisaStok <= 0 ? StatusStok::Habis : StatusStok::Ready,
                    ]);
                }

                return $order;
            });
        } catch (ValidationException $exception) {
            return back()
                ->withInput()
                ->with('error', collect($exception->errors())->flatten()->first())
                ->withErrors($exception->errors());
        }

        $total = $order->total_harga;

        return redirect()
            ->route('admin.kasir.index')
            ->with('success', 'Transaksi tunai '.$order->kode_pesanan.' berhasil disimpan.')
            ->with('receipt', [
                'order_id' => $order->id,
                'uang_diterima' => $uangDiterima,
                'kembalian' => $uangDiterima === null ? null : max(0, $uangDiterima - $total),
            ]);
    }

    /**
     * Samakan format input item agar form baru (items[X][...]) dan payload
     * lama (product_id + jumlah) sama-sama bisa diproses.
     */
    private function normalizeItems(Request $request): void
    {
        $items = $request->input('items');

        if (! is_array($items) || $items === []) {
            if ($request->filled('product_id')) {
                $request->merge(['items' => [[
                    'product_id' => $request->input('product_id'),
                    'qty' => $request->input('jumlah', 1),
                ]]]);
            }

            return;
        }

        $normalized = [];

        foreach ($items as $index => $item) {
            if (! is_array($item)) {
                continue;
            }

            $normalized[$index] = [
                'product_id' => $item['product_id'] ?? null,
                'qty' => $item['qty'] ?? $item['jumlah'] ?? $item['quantity'] ?? null,
            ];
        }

        $request->merge(['items' => $normalized]);
    }
}
