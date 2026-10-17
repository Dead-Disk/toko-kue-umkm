<?php

namespace App\Http\Controllers;

use App\Enums\MetodePembayaran;
use App\Enums\OpsiPengiriman;
use App\Enums\StatusPesanan;
use App\Enums\StatusStok;
use App\Enums\TipePesanan;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Simpan pesanan reguler dari pembeli, lalu arahkan ke halaman sukses.
     */
    public function checkout(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'nama_pemesan' => ['required', 'string', 'max:255'],
            'alamat_lengkap' => ['required', 'string', 'max:1000'],
            'nomor_wa' => ['required', 'string', 'max:20'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'opsi_pengiriman' => ['required', Rule::enum(OpsiPengiriman::class)],
            'metode_pembayaran' => ['required', Rule::enum(MetodePembayaran::class)],
        ]);

        $product = Product::findOrFail($data['product_id']);

        if ($product->status_stok !== StatusStok::Ready) {
            return back()
                ->withInput()
                ->withErrors(['product_id' => 'Maaf, produk ini sedang habis.']);
        }

        $subtotal = $product->harga * $data['jumlah'];

        $order = DB::transaction(function () use ($data, $product, $subtotal): Order {
            $order = Order::create([
                'kode_pesanan' => Order::generateKodePesanan(),
                'tipe_pesanan' => TipePesanan::Reguler,
                'nama_pemesan' => $data['nama_pemesan'],
                'nomor_wa' => $data['nomor_wa'],
                'alamat_lengkap' => $data['alamat_lengkap'],
                'opsi_pengiriman' => $data['opsi_pengiriman'],
                'metode_pembayaran' => $data['metode_pembayaran'],
                'total_harga' => $subtotal,
                'status' => StatusPesanan::Pending,
            ]);

            $order->items()->create([
                'product_id' => $product->id,
                'jumlah' => $data['jumlah'],
                'subtotal' => $subtotal,
            ]);

            return $order;
        });

        return redirect()->route('customer.pesanan.sukses', $order->kode_pesanan);
    }

    /**
     * Halaman konfirmasi bahwa pesanan berhasil dikirim.
     */
    public function success(string $kode_pesanan): View
    {
        $order = Order::with(['items.product'])
            ->where('kode_pesanan', $kode_pesanan)
            ->firstOrFail();

        return view('customer.pesanan-sukses', ['order' => $order]);
    }
}
