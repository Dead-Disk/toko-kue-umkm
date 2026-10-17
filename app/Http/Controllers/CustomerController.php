<?php

namespace App\Http\Controllers;

use App\Enums\MetodePembayaran;
use App\Enums\OpsiPengiriman;
use App\Enums\StatusPesanan;
use App\Enums\TipePesanan;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Halaman utama (landing page) pembeli.
     */
    public function index(): View
    {
        return view('customer.index');
    }

    /**
     * Halaman dinamis berbasis slug.
     *
     * View dicari di resources/views/customer/pages/{slug}.blade.php.
     * Bila belum ada, tampilkan halaman placeholder.
     */
    public function page(string $slug): View
    {
        $view = "customer.pages.{$slug}";

        if (! view()->exists($view)) {
            return view('customer.placeholder', ['slug' => $slug]);
        }

        return view($view, ['slug' => $slug]);
    }

    /**
     * Simpan pesanan custom cake beserta foto referensinya.
     */
    public function storeCustom(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_pemesan' => ['required', 'string', 'max:255'],
            'nomor_wa' => ['required', 'string', 'max:20'],
            'alamat_lengkap' => ['required', 'string', 'max:1000'],
            'deskripsi_custom' => ['required', 'string', 'max:2000'],
            'ukuran_kue' => ['required', 'string', 'max:50'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'opsi_pengiriman' => ['required', Rule::enum(OpsiPengiriman::class)],
            'metode_pembayaran' => ['required', Rule::enum(MetodePembayaran::class)],
            'foto_referensi' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $fotoPath = $request->hasFile('foto_referensi')
            ? $request->file('foto_referensi')->store('custom_cakes', 'public')
            : null;

        $order = DB::transaction(function () use ($data, $fotoPath): Order {
            $order = Order::create([
                'kode_pesanan' => Order::generateKodePesanan(),
                'tipe_pesanan' => TipePesanan::Custom,
                'nama_pemesan' => $data['nama_pemesan'],
                'nomor_wa' => $data['nomor_wa'],
                'alamat_lengkap' => $data['alamat_lengkap'],
                'opsi_pengiriman' => $data['opsi_pengiriman'],
                'metode_pembayaran' => $data['metode_pembayaran'],
                'total_harga' => 0,
                'status' => StatusPesanan::Pending,
            ]);

            $order->items()->create([
                'product_id' => null,
                'jumlah' => $data['jumlah'],
                'subtotal' => 0,
                'deskripsi_custom' => $data['deskripsi_custom'],
                'foto_referensi' => $fotoPath,
                'ukuran_kue' => $data['ukuran_kue'],
            ]);

            return $order;
        });

        return redirect()->route('customer.pesanan.sukses', $order->kode_pesanan);
    }
}
