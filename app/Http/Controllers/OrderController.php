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
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Langkah 1 (jalur keranjang): tampilkan ringkasan isi keranjang + form data pemesan.
     */
    public function reviewCart(): View|RedirectResponse
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->with('warning', 'Keranjang belanja Anda masih kosong.');
        }

        return view('customer.pesanan-review-cart', [
            'cart' => $cart,
            'total' => collect($cart)->sum(fn (array $item): int => $item['price'] * $item['quantity']),
        ]);
    }

    /**
     * Langkah 1: validasi form pesanan reguler, simpan draf sementara ke session,
     * lalu tampilkan halaman ringkasan. Belum ada yang ditulis ke database orders.
     */
    public function review(Request $request): View|RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')->withoutTrashed()],
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

        if ($product->stok < $data['jumlah']) {
            return back()
                ->withInput()
                ->withErrors(['jumlah' => 'Stok tidak mencukupi. Sisa stok '.$product->stok.'.']);
        }

        $draft = [
            'product_id' => (int) $data['product_id'],
            'nama_pemesan' => $data['nama_pemesan'],
            'alamat_lengkap' => $data['alamat_lengkap'],
            'nomor_wa' => $data['nomor_wa'],
            'jumlah' => (int) $data['jumlah'],
            'opsi_pengiriman' => $data['opsi_pengiriman'],
            'metode_pembayaran' => $data['metode_pembayaran'],
        ];

        session(['order_draft' => $draft]);

        return view('customer.pesanan-review', [
            'draft' => $draft,
            'product' => $product,
            'subtotal' => $product->harga * $draft['jumlah'],
        ]);
    }

    /**
     * Langkah 2: simpan pesanan reguler dari draf ringkasan, lalu arahkan ke halaman sukses.
     */
    public function checkout(Request $request): RedirectResponse
    {
        if ($request->input('checkout_mode') === 'cart') {
            return $this->checkoutCart($request);
        }

        $draft = session('order_draft');

        if (empty($draft)) {
            return redirect()
                ->route('customer.produk.index')
                ->withErrors(['order' => 'Data pesanan belum lengkap. Silakan pilih produk dan isi form pesanan terlebih dahulu.']);
        }

        try {
            $order = DB::transaction(function () use ($draft): Order {
                $product = Product::query()->lockForUpdate()->findOrFail($draft['product_id']);

                if ($product->stok < $draft['jumlah']) {
                    throw ValidationException::withMessages([
                        'jumlah' => 'Stok tidak mencukupi. Sisa stok '.$product->stok.'.',
                    ]);
                }

                $subtotal = $product->harga * $draft['jumlah'];

                $order = Order::create([
                    'kode_pesanan' => Order::generateKodePesanan(),
                    'tipe_pesanan' => TipePesanan::Reguler,
                    'nama_pemesan' => $draft['nama_pemesan'],
                    'nomor_wa' => $draft['nomor_wa'],
                    'alamat_lengkap' => $draft['alamat_lengkap'],
                    'opsi_pengiriman' => $draft['opsi_pengiriman'],
                    'metode_pembayaran' => $draft['metode_pembayaran'],
                    'total_harga' => $subtotal,
                    'status' => StatusPesanan::Pending,
                ]);

                $order->items()->create([
                    'product_id' => $product->id,
                    'jumlah' => $draft['jumlah'],
                    'subtotal' => $subtotal,
                ]);

                // Stok tidak dipotong di sini; dipotong saat admin menekan "Terima"
                // (lihat AdminOrderController::updateStatus). Cek stok di atas hanya gerbang awal.

                return $order;
            });
        } catch (ValidationException $exception) {
            // Stok berubah setelah review (race). Kembalikan ke form tanpa kehilangan inputan.
            return redirect()
                ->route('customer.produk.show', $draft['product_id'])
                ->withErrors($exception->errors());
        }

        session()->forget('order_draft');

        return redirect()->route('customer.pesanan.sukses', $order->kode_pesanan);
    }

    /**
     * Simpan pesanan multi-item dari keranjang session (jalur keranjang tamu).
     */
    private function checkoutCart(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_pemesan' => ['required', 'string', 'max:255'],
            'alamat_lengkap' => ['required', 'string', 'max:1000'],
            'nomor_wa' => ['required', 'string', 'max:20'],
            'opsi_pengiriman' => ['required', Rule::enum(OpsiPengiriman::class)],
            'metode_pembayaran' => ['required', Rule::enum(MetodePembayaran::class)],
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->with('warning', 'Keranjang belanja Anda masih kosong.');
        }

        try {
            $order = DB::transaction(function () use ($cart, $data): Order {
                $baris = [];
                $total = 0;

                foreach ($cart as $item) {
                    $product = Product::query()->lockForUpdate()->find($item['id']);

                    if ($product === null) {
                        throw ValidationException::withMessages([
                            'cart' => 'Salah satu produk di keranjang sudah tidak tersedia.',
                        ]);
                    }

                    if ($product->stok < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'cart' => 'Stok '.$product->nama_kue.' tidak mencukupi. Sisa stok '.$product->stok.'.',
                        ]);
                    }

                    $subtotal = $product->harga * $item['quantity'];
                    $total += $subtotal;
                    $baris[] = [$product, $item['quantity'], $subtotal];
                }

                $order = Order::create([
                    'kode_pesanan' => Order::generateKodePesanan(),
                    'tipe_pesanan' => TipePesanan::Reguler,
                    'nama_pemesan' => $data['nama_pemesan'],
                    'nomor_wa' => $data['nomor_wa'],
                    'alamat_lengkap' => $data['alamat_lengkap'],
                    'opsi_pengiriman' => $data['opsi_pengiriman'],
                    'metode_pembayaran' => $data['metode_pembayaran'],
                    'total_harga' => $total,
                    'status' => StatusPesanan::Pending,
                ]);

                foreach ($baris as [$product, $jumlah, $subtotal]) {
                    $order->items()->create([
                        'product_id' => $product->id,
                        'jumlah' => $jumlah,
                        'subtotal' => $subtotal,
                    ]);
                }

                // Stok dipotong saat admin menekan "Terima" (lihat AdminOrderController).

                return $order;
            });
        } catch (ValidationException $exception) {
            return redirect()
                ->route('cart.index')
                ->withErrors($exception->errors());
        }

        session()->forget('cart');

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
