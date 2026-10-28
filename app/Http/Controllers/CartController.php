<?php

namespace App\Http\Controllers;

use App\Enums\StatusStok;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Tampilkan isi keranjang belanja dari session.
     */
    public function index(): View
    {
        $cart = session('cart', []);

        return view('cart.index', [
            'cart' => $cart,
            'total' => $this->total($cart),
        ]);
    }

    /**
     * Tambah produk ke keranjang. Produk yang sudah ada cukup ditambah jumlahnya.
     */
    public function add(Request $request, Product $product): RedirectResponse
    {
        if ($product->status_stok !== StatusStok::Ready) {
            return back()->withErrors(['cart' => 'Maaf, produk ini sedang habis.']);
        }

        $data = $request->validate([
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $cart = session('cart', []);
        $jumlah = max(1, (int) $request->input('quantity', 1));

        $jumlahBaru = ($cart[$product->id]['quantity'] ?? 0) + $jumlah;
        $maxPerOrder = $product->max_per_order ?? 5;

        if ($jumlahBaru > $product->stok) {
            return back()->withErrors(['cart' => 'Stok '.$product->nama_kue.' tidak mencukupi. Sisa stok '.$product->stok.'.']);
        }

        if ($jumlahBaru > $maxPerOrder) {
            $message = 'Produk ini hanya dapat dipesan maksimal '.$maxPerOrder.' pcs per transaksi.';

            return back()
                ->with('error', $message)
                ->withErrors(['cart' => $message]);
        }

        $catatan = trim((string) ($data['catatan'] ?? ''));

        $cart[$product->id] = [
            'id' => $product->id,
            'name' => $product->nama_kue,
            'price' => $product->harga,
            'quantity' => $jumlahBaru,
            'image_url' => $product->foto ? asset('storage/'.$product->foto) : null,
            'options' => $cart[$product->id]['options'] ?? [],
            'catatan' => $catatan !== '' ? $catatan : ($cart[$product->id]['catatan'] ?? null),
        ];

        session(['cart' => $cart]);

        return back()->with('success', $product->nama_kue.' ditambahkan ke keranjang.');
    }

    /**
     * Ubah jumlah produk tertentu di keranjang.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['sometimes', 'required', 'integer', 'min:1'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $cart = session('cart', []);

        if (! isset($cart[$product->id])) {
            return back()->withErrors(['cart' => 'Produk tidak ada di keranjang.']);
        }

        $quantity = (int) ($data['quantity'] ?? $cart[$product->id]['quantity']);
        $maxPerOrder = $product->max_per_order ?? 5;

        if ($quantity > $maxPerOrder) {
            $message = 'Produk ini hanya dapat dipesan maksimal '.$maxPerOrder.' pcs per transaksi.';

            return back()
                ->with('error', $message)
                ->withErrors(['cart' => $message]);
        }

        $cart[$product->id]['quantity'] = $quantity;

        if ($request->has('catatan')) {
            $catatan = trim((string) $request->input('catatan', ''));

            $cart[$product->id]['catatan'] = $catatan !== '' ? $catatan : null;
        }

        session(['cart' => $cart]);

        $message = $request->has('catatan')
            ? 'Catatan '.$cart[$product->id]['name'].' diperbarui.'
            : 'Jumlah '.$cart[$product->id]['name'].' diperbarui.';

        return back()->with('success', $message);
    }

    /**
     * Hapus satu produk dari keranjang.
     */
    public function remove(Product $product): RedirectResponse
    {
        $cart = session('cart', []);

        unset($cart[$product->id]);

        session(['cart' => $cart]);

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    /**
     * Kosongkan seluruh keranjang.
     */
    public function clear(): RedirectResponse
    {
        session()->forget(['cart', 'cart_note']);

        return back()->with('success', 'Keranjang belanja dikosongkan.');
    }

    /**
     * Total harga seluruh item di keranjang.
     *
     * @param  array<int, array<string, mixed>>  $cart
     */
    private function total(array $cart): int
    {
        return collect($cart)->sum(fn (array $item): int => $item['price'] * $item['quantity']);
    }
}
