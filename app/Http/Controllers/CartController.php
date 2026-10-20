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

        $cart = session('cart', []);
        $jumlah = max(1, (int) $request->input('quantity', 1));

        $jumlahBaru = ($cart[$product->id]['quantity'] ?? 0) + $jumlah;

        if ($jumlahBaru > $product->stok) {
            return back()->withErrors(['cart' => 'Stok '.$product->nama_kue.' tidak mencukupi. Sisa stok '.$product->stok.'.']);
        }

        $cart[$product->id] = [
            'id' => $product->id,
            'name' => $product->nama_kue,
            'price' => $product->harga,
            'quantity' => $jumlahBaru,
            'image_url' => $product->foto ? asset('storage/'.$product->foto) : null,
            'options' => $cart[$product->id]['options'] ?? [],
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
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = session('cart', []);

        if (! isset($cart[$product->id])) {
            return back()->withErrors(['cart' => 'Produk tidak ada di keranjang.']);
        }

        $cart[$product->id]['quantity'] = (int) $data['quantity'];

        session(['cart' => $cart]);

        return back()->with('success', 'Jumlah '.$cart[$product->id]['name'].' diperbarui.');
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
        session()->forget('cart');

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
