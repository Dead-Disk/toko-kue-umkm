<?php

namespace App\Http\Controllers;

use App\Enums\StatusPesanan;
use App\Enums\TipePesanan;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminOrderController extends Controller
{
    /**
     * Filter tab di atas tabel: parameter ?status= => nilai status yang ditampilkan.
     *
     * @var array<string, list<string>>
     */
    private const FILTER_STATUS = [
        'pending' => ['pending'],
        'proses' => ['proses'],
        'selesai' => ['selesai'],
        'ditolak' => ['ditolak'],
        'batal' => ['batal'],
        'ditolak-batal' => ['ditolak', 'batal'],
    ];

    /**
     * Daftar pesanan masuk dari pembeli, bisa difilter lewat ?status=, ?type=, dan ?start_date=/&end_date=.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $filter = is_string($status) && array_key_exists($status, self::FILTER_STATUS) ? $status : null;

        $type = $request->query('type');
        $filterTipe = is_string($type) ? TipePesanan::tryFrom($type) : null;

        $orders = Order::with(['items.product'])
            ->withCount('items')
            ->when($request->start_date, fn ($query) => $query->whereDate('created_at', '>=', $request->start_date))
            ->when($request->end_date, fn ($query) => $query->whereDate('created_at', '<=', $request->end_date));

        if ($filter !== null) {
            $orders->whereIn('status', self::FILTER_STATUS[$filter]);
        }

        if ($filterTipe !== null) {
            $orders->where('tipe_pesanan', $filterTipe);
        }

        return view('admin.order.index', [
            'orders' => $orders->latest()->get(),
            'filter' => $filter,
            'filterTipe' => $filterTipe,
        ]);
    }

    /**
     * Ubah status pesanan: terima (potong stok), tolak, selesaikan, atau batalkan (kembalikan stok).
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::enum(StatusPesanan::class)],
        ]);

        $statusBaru = StatusPesanan::from($data['status']);
        $statusLama = $order->status;

        // Stok dipotong sekali saat pesanan diterima (pending -> proses) dan
        // dikembalikan sekali saat pesanan yang sedang diproses dibatalkan.
        $pesanError = DB::transaction(function () use ($order, $statusLama, $statusBaru): ?string {
            if ($statusBaru === StatusPesanan::Proses && $statusLama === StatusPesanan::Pending) {
                return $this->terimaPesanan($order);
            }

            $order->update(['status' => $statusBaru]);

            if ($statusBaru === StatusPesanan::Batal && $statusLama === StatusPesanan::Proses) {
                $this->kembalikanStok($order);
            }

            return null;
        });

        if ($pesanError !== null) {
            return redirect()
                ->route('admin.pesanan.index', array_filter($request->only(['status', 'start_date', 'end_date'])))
                ->withErrors(['status' => $pesanError]);
        }

        return redirect()
            ->route('admin.pesanan.index')
            ->with('success', 'Status pesanan '.$order->kode_pesanan.' berhasil diperbarui.');
    }

    /**
     * Soft delete pesanan (dihapus dari daftar, baris tetap ada di database).
     */
    public function destroy(Request $request, Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()
            ->route('admin.pesanan.index', array_filter($request->only(['status', 'start_date', 'end_date'])))
            ->with('success', 'Pesanan '.$order->kode_pesanan.' berhasil dihapus.');
    }

    /**
     * Kirim file bukti pembayaran dari private storage. Hanya admin/staf yang login.
     */
    public function viewBuktiPembayaran(Order $order): BinaryFileResponse
    {
        return $this->privateFileResponse($order->bukti_pembayaran);
    }

    /**
     * Kirim foto referensi custom cake dari private storage. Hanya admin/staf yang login.
     */
    public function viewCustomPhoto(Order $order): BinaryFileResponse
    {
        $foto = $order->items()->whereNotNull('foto_referensi')->value('foto_referensi');

        return $this->privateFileResponse($foto);
    }

    /**
     * Baca file dari disk private ('local'), 404 bila path kosong/tidak ada.
     */
    private function privateFileResponse(?string $path): BinaryFileResponse
    {
        abort_if($path === null || ! Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path));
    }

    /**
     * Terima pesanan: potong stok semua item reguler bila stoknya mencukupi.
     * Cek dilakukan untuk semua item dulu, baru dipotong, supaya tidak ada stok
     * yang terpotong sebagian bila salah satu item kurang.
     *
     * @return string|null pesan error bila stok tidak mencukupi, null bila berhasil
     */
    private function terimaPesanan(Order $order): ?string
    {
        $potongan = [];

        foreach ($order->items as $item) {
            if ($item->product_id === null) {
                continue;
            }

            // Produk soft-deleted ikut diperiksa agar angka stoknya tetap konsisten bila di-restore.
            $product = Product::withTrashed()->lockForUpdate()->find($item->product_id);

            if ($product === null) {
                continue;
            }

            if ($product->stok < $item->jumlah) {
                return 'Stok produk tidak mencukupi!';
            }

            $potongan[] = [$product, $item->jumlah];
        }

        foreach ($potongan as [$product, $jumlah]) {
            $product->decrement('stok', $jumlah);
        }

        $order->update(['status' => StatusPesanan::Proses]);

        return null;
    }

    /**
     * Kembalikan stok semua produk pada pesanan.
     */
    private function kembalikanStok(Order $order): void
    {
        $order->items->each(function (OrderItem $item): void {
            $item->product()->withTrashed()->first()?->increment('stok', $item->jumlah);
        });
    }
}
