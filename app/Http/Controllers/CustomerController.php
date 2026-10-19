<?php

namespace App\Http\Controllers;

use App\Enums\MetodePembayaran;
use App\Enums\OpsiPengiriman;
use App\Enums\StatusPesanan;
use App\Enums\TipePesanan;
use App\Models\CakeOption;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Halaman utama (landing page) pembeli.
     */
    public function index(): View
    {
        return view('customer.index', [
            'topProducts' => $this->topProducts(),
        ]);
    }

    /**
     * 10 produk terlaris berdasarkan akumulasi jumlah di order_items.
     * Bila belum ada transaksi sama sekali, ambil 10 produk terbaru.
     *
     * @return Collection<int, Product>
     */
    private function topProducts(): Collection
    {
        $topProducts = Product::query()
            ->with('category')
            ->withSum('orderItems as total_terjual', 'jumlah')
            ->orderByDesc('total_terjual')
            ->orderBy('nama_kue')
            ->limit(10)
            ->get()
            ->filter(fn (Product $product): bool => ($product->total_terjual ?? 0) > 0)
            ->values();

        if ($topProducts->isEmpty()) {
            $topProducts = Product::query()
                ->with('category')
                ->latest()
                ->limit(10)
                ->get();
        }

        return $topProducts;
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
     * Form pesanan custom cake, lengkap dengan pilihan ukuran dari database.
     */
    public function customForm(): View
    {
        return view('customer.custom', [
            'cakeOptions' => CakeOption::orderBy('bentuk')->orderBy('ukuran')->get(),
            'draft' => session('custom_order_draft', []),
        ]);
    }

    /**
     * Validasi form custom, simpan draf sementara ke session (termasuk foto),
     * lalu tampilkan halaman review. Belum ada yang ditulis ke database orders.
     */
    public function reviewCustom(Request $request): View
    {
        $data = $request->validate([
            'nama_pemesan' => ['required', 'string', 'max:255'],
            'nomor_wa' => ['required', 'string', 'max:20'],
            'alamat_lengkap' => ['required', 'string', 'max:1000'],
            'deskripsi_custom' => ['required', 'string', 'max:2000'],
            'cake_option_id' => ['required', 'integer', 'exists:cake_options,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'opsi_pengiriman' => ['required', Rule::enum(OpsiPengiriman::class)],
            'metode_pembayaran' => ['required', Rule::enum(MetodePembayaran::class)],
            'foto_referensi' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Simpan file sementara ke folder public (temp_custom) dan simpan path-nya di session
        if ($request->hasFile('foto_referensi')) {
            if ($oldTemp = session('temp_foto')) {
                $oldDiskPath = 'temp_custom/'.basename($oldTemp);
                if (Storage::disk('public')->exists($oldDiskPath)) {
                    Storage::disk('public')->delete($oldDiskPath);
                }
            }

            $storedPath = $request->file('foto_referensi')->store('temp_custom', 'public');
            $tempFoto = 'storage/'.$storedPath;
            session(['temp_foto' => $tempFoto]);
        } else {
            $tempFoto = session('temp_foto');
        }

        $draft = [
            'nama_pemesan' => $data['nama_pemesan'],
            'nomor_wa' => $data['nomor_wa'],
            'alamat_lengkap' => $data['alamat_lengkap'],
            'deskripsi_custom' => $data['deskripsi_custom'],
            'cake_option_id' => (int) $data['cake_option_id'],
            'jumlah' => (int) $data['jumlah'],
            'opsi_pengiriman' => $data['opsi_pengiriman'],
            'metode_pembayaran' => $data['metode_pembayaran'],
            'foto_referensi' => $tempFoto,
        ];

        session(['custom_order_draft' => $draft]);

        $cakeOption = CakeOption::findOrFail($draft['cake_option_id']);

        return view('customer.custom_review', [
            'draft' => $draft,
            'cakeOption' => $cakeOption,
            'estimasiHarga' => $cakeOption->harga_dasar * $draft['jumlah'],
        ]);
    }

    /**
     * Simpan pesanan custom secara permanen setelah dikonfirmasi di halaman review.
     */
    public function storeCustom(): RedirectResponse
    {
        $draft = session('custom_order_draft');

        if (empty($draft)) {
            return redirect()
                ->route('customer.custom.create')
                ->withErrors(['custom' => 'Data pesanan belum lengkap. Silakan isi form custom terlebih dahulu.']);
        }

        $cakeOption = CakeOption::find($draft['cake_option_id']);

        if ($cakeOption === null) {
            return redirect()
                ->route('customer.custom.create')
                ->withErrors(['cake_option_id' => 'Ukuran kue yang dipilih sudah tidak tersedia. Silakan pilih ulang.']);
        }

        $jumlah = (int) $draft['jumlah'];
        $subtotal = $cakeOption->harga_dasar * $jumlah;

        $tempFoto = session('temp_foto') ?? ($draft['foto_referensi'] ?? null);
        $fotoPermanen = null;

        if (! empty($tempFoto)) {
            $filename = basename($tempFoto);
            $tempDiskPath = 'temp_custom/'.$filename;
            $permanentDiskPath = 'custom_cakes/'.$filename;

            if (Storage::disk('public')->exists($tempDiskPath)) {
                Storage::disk('public')->move($tempDiskPath, $permanentDiskPath);
                $fotoPermanen = $permanentDiskPath;
            } elseif (Storage::disk('public')->exists($permanentDiskPath)) {
                $fotoPermanen = $permanentDiskPath;
            } elseif (Storage::disk('public')->exists($tempFoto)) {
                Storage::disk('public')->move($tempFoto, $permanentDiskPath);
                $fotoPermanen = $permanentDiskPath;
            }
        }

        $order = DB::transaction(function () use ($draft, $cakeOption, $jumlah, $subtotal, $fotoPermanen): Order {
            $order = Order::create([
                'kode_pesanan' => Order::generateKodePesanan(),
                'tipe_pesanan' => TipePesanan::Custom,
                'nama_pemesan' => $draft['nama_pemesan'],
                'nomor_wa' => $draft['nomor_wa'],
                'alamat_lengkap' => $draft['alamat_lengkap'],
                'opsi_pengiriman' => $draft['opsi_pengiriman'],
                'metode_pembayaran' => $draft['metode_pembayaran'],
                'total_harga' => $subtotal,
                'status' => StatusPesanan::Pending,
            ]);

            $order->items()->create([
                'product_id' => null,
                'jumlah' => $jumlah,
                'subtotal' => $subtotal,
                'deskripsi_custom' => $draft['deskripsi_custom'],
                'foto_referensi' => $fotoPermanen,
                'ukuran_kue' => $cakeOption->label(),
            ]);

            return $order;
        });

        session()->forget(['custom_order_draft', 'temp_foto']);

        return redirect()->route('customer.pesanan.sukses', $order->kode_pesanan);
    }
}
