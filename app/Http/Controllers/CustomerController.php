<?php

namespace App\Http\Controllers;

use App\Enums\MetodePembayaran;
use App\Enums\OpsiPengiriman;
use App\Enums\StatusPesanan;
use App\Enums\TipePesanan;
use App\Models\Banner;
use App\Models\CakeOption;
use App\Models\Category;
use App\Models\ChatMessage;
use App\Models\ChatThread;
use App\Models\Order;
use App\Models\Product;
use App\Models\StoreSetting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Batas maksimal jumlah kue pada satu pesanan custom.
     */
    private const MAX_CUSTOM_QUANTITY = 5;

    /**
     * Halaman utama (landing page) pembeli.
     */
    public function index(): View
    {
        $categories = Category::whereHas('products')->with('products')->get();
        $produks = $this->topProducts();
        $storeSetting = StoreSetting::current();

        $activeBanners = Banner::query()
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();

        if ($activeBanners->isNotEmpty()) {
            $banners = $activeBanners;
        } elseif ($storeSetting->hasBanner()) {
            $banners = collect([
                (object) [
                    'id' => 'store-banner',
                    'judul' => null,
                    'subjudul' => null,
                    'gambar' => $storeSetting->banner_path,
                    'gambar_url' => $storeSetting->bannerUrl(),
                    'link' => null,
                    'badge' => null,
                    'scale' => $storeSetting->banner_scale ?? 'cover',
                    'urutan' => 0,
                    'is_active' => true,
                ],
            ]);
        } else {
            $banners = collect();
        }

        return view('customer.beranda.index', [
            'categories' => $categories,
            'produks' => $produks,
            'topProducts' => $produks,
            'storeSetting' => $storeSetting,
            'banners' => $banners,
        ]);
    }

    /**
     * Produk rekomendasi / terlaris dari order_items.
     * Bila belum ada transaksi sama sekali, ambil produk terbaru.
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
            ->limit(4)
            ->get()
            ->filter(fn (Product $product): bool => ($product->total_terjual ?? 0) > 0)
            ->values();

        if ($topProducts->isEmpty()) {
            $topProducts = Product::query()
                ->with('category')
                ->latest()
                ->limit(4)
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
            return view('customer.pages.placeholder', ['slug' => $slug]);
        }

        return view($view, [
            'slug' => $slug,
            'storeSetting' => StoreSetting::current(),
        ]);
    }

    /**
     * Form pesanan custom cake, lengkap dengan pilihan ukuran dari database.
     */
    public function customForm(Request $request): View
    {
        $draft = session('custom_order_draft', []);
        $products = Product::query()->with('category')->orderBy('nama_kue')->get();
        $cakeOptions = CakeOption::with('category')->orderBy('bentuk')->orderBy('ukuran')->get();
        $categoriesWithOptions = Category::query()
            ->whereHas('cakeOptions')
            ->orderBy('nama_kategori')
            ->get();

        $baseProductId = $request->old('base_product_id', $draft['base_product_id'] ?? null);
        $cakeOptionId = $request->old('cake_option_id', $draft['cake_option_id'] ?? null);
        $jumlah = (int) $request->old('jumlah', $draft['jumlah'] ?? 1);
        $jumlah = min(self::MAX_CUSTOM_QUANTITY, max(1, $jumlah));

        $baseProduct = $baseProductId ? $products->firstWhere('id', (int) $baseProductId) : null;
        $cakeOption = $cakeOptionId ? $cakeOptions->firstWhere('id', (int) $cakeOptionId) : null;
        $selectedCategoryId = $request->old(
            'cake_category_id',
            $draft['cake_category_id'] ?? $cakeOption?->category_id ?? $baseProduct?->category_id ?? ''
        );
        $hargaAcuan = $baseProduct?->harga ?? $cakeOption?->harga_dasar ?? 0;

        return view('customer.custom.index', [
            'cakeOptions' => $cakeOptions,
            'categoriesWithOptions' => $categoriesWithOptions,
            'selectedCategoryId' => $selectedCategoryId,
            'products' => $products,
            'draft' => $draft,
            'jumlah' => $jumlah,
            'maxQuantity' => self::MAX_CUSTOM_QUANTITY,
            'estimasiHarga' => $hargaAcuan * $jumlah,
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
            'cake_category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->whereNull('deleted_at')],
            'cake_option_id' => ['required', 'integer', 'exists:cake_options,id'],
            'base_product_id' => ['nullable', 'integer', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'jumlah' => ['required', 'integer', 'min:1', 'max:'.self::MAX_CUSTOM_QUANTITY],
            'opsi_pengiriman' => ['required', Rule::enum(OpsiPengiriman::class)],
            'metode_pembayaran' => ['required', Rule::enum(MetodePembayaran::class)],
            'tanggal_ambil' => ['required', 'date', 'after_or_equal:today'],
            'foto_referensi' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'jumlah.max' => 'Maksimal pemesanan untuk pesanan ini adalah '.self::MAX_CUSTOM_QUANTITY.' pcs.',
        ]);

        // Simpan file sementara ke private storage (temp_custom) dan simpan path-nya di session
        if ($request->hasFile('foto_referensi')) {
            if ($oldTemp = session('temp_foto')) {
                $oldDiskPath = 'temp_custom/'.basename($oldTemp);
                if (Storage::exists($oldDiskPath)) {
                    Storage::delete($oldDiskPath);
                }
            }

            $tempFoto = $request->file('foto_referensi')->store('temp_custom');
            session(['temp_foto' => $tempFoto]);
        } else {
            $tempFoto = session('temp_foto');
        }

        $draft = [
            'nama_pemesan' => $data['nama_pemesan'],
            'nomor_wa' => $data['nomor_wa'],
            'alamat_lengkap' => $data['alamat_lengkap'],
            'deskripsi_custom' => $data['deskripsi_custom'],
            'cake_category_id' => ! empty($data['cake_category_id']) ? (int) $data['cake_category_id'] : null,
            'cake_option_id' => (int) $data['cake_option_id'],
            'base_product_id' => ! empty($data['base_product_id']) ? (int) $data['base_product_id'] : null,
            'jumlah' => (int) $data['jumlah'],
            'opsi_pengiriman' => $data['opsi_pengiriman'],
            'metode_pembayaran' => $data['metode_pembayaran'],
            'tanggal_ambil' => $data['tanggal_ambil'],
            'foto_referensi' => $tempFoto,
        ];

        session(['custom_order_draft' => $draft]);

        $cakeOption = CakeOption::findOrFail($draft['cake_option_id']);
        $baseProduct = ! empty($draft['base_product_id'])
            ? Product::find($draft['base_product_id'])
            : null;
        $hargaAcuan = $baseProduct?->harga ?? $cakeOption->harga_dasar;

        return view('customer.custom.review', [
            'draft' => $draft,
            'cakeOption' => $cakeOption,
            'baseProduct' => $baseProduct,
            'hargaAcuan' => $hargaAcuan,
            'estimasiHarga' => $hargaAcuan * $draft['jumlah'],
        ]);
    }

    /**
     * Simpan pesanan custom secara permanen setelah dikonfirmasi di halaman review.
     */
    public function storeCustom(Request $request): RedirectResponse
    {
        $validatedPin = $request->validate([
            'chat_pin' => ['nullable', 'digits:6'],
        ], [
            'chat_pin.digits' => 'PIN rahasia chat harus berupa tepat 6 digit angka.',
        ]);

        $chatPin = $validatedPin['chat_pin'] ?? session('customer_chat_pin');

        $chatPinHash = filled($chatPin)
            ? Hash::make((string) $chatPin)
            : null;

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

        if ($jumlah < 1 || $jumlah > self::MAX_CUSTOM_QUANTITY) {
            return redirect()
                ->route('customer.custom.create')
                ->withErrors(['jumlah' => 'Maksimal pemesanan untuk pesanan ini adalah '.self::MAX_CUSTOM_QUANTITY.' pcs.']);
        }

        $baseProduct = ! empty($draft['base_product_id'])
            ? Product::find($draft['base_product_id'])
            : null;
        $baseProductId = $baseProduct?->id;
        $hargaAcuan = $baseProduct?->harga ?? $cakeOption->harga_dasar;
        $subtotal = $hargaAcuan * $jumlah;

        $tempFoto = session('temp_foto') ?? ($draft['foto_referensi'] ?? null);
        $fotoPermanen = null;

        if (! empty($tempFoto)) {
            $filename = basename($tempFoto);
            $tempDiskPath = 'temp_custom/'.$filename;
            $permanentDiskPath = 'custom_cakes/'.$filename;

            if (Storage::exists($tempDiskPath)) {
                Storage::move($tempDiskPath, $permanentDiskPath);
                $fotoPermanen = $permanentDiskPath;
            } elseif (Storage::exists($permanentDiskPath)) {
                $fotoPermanen = $permanentDiskPath;
            }
        }

        $order = DB::transaction(function () use ($draft, $cakeOption, $baseProductId, $baseProduct, $jumlah, $subtotal, $fotoPermanen, $chatPinHash): Order {
            $order = Order::create([
                'kode_pesanan' => Order::generateKodePesanan(),
                'tipe_pesanan' => TipePesanan::Custom,
                'nama_pemesan' => $draft['nama_pemesan'],
                'nomor_wa' => $draft['nomor_wa'],
                'chat_pin' => $chatPinHash,
                'alamat_lengkap' => $draft['alamat_lengkap'],
                'opsi_pengiriman' => $draft['opsi_pengiriman'],
                'metode_pembayaran' => $draft['metode_pembayaran'],
                'total_harga' => $subtotal,
                'status' => StatusPesanan::Pending,
                'tanggal_ambil' => $draft['tanggal_ambil'] ?? null,
            ]);

            $order->items()->create([
                'product_id' => $baseProductId,
                'jumlah' => $jumlah,
                'subtotal' => $subtotal,
                'deskripsi_custom' => $draft['deskripsi_custom'],
                'foto_referensi' => $fotoPermanen,
                'ukuran_kue' => $cakeOption->label(),
            ]);

            // Auto-kirim pesan template pesanan custom ke chat penjual (tepat sekali / idempoten)
            $normalizedPhone = ChatThread::normalizePhone($order->nomor_wa);
            $thread = ChatThread::firstOrCreate(
                ['order_code' => $order->kode_pesanan],
                [
                    'customer_name' => $order->nama_pemesan,
                    'customer_phone' => $normalizedPhone,
                    'thread_type' => 'order',
                    'unread_admin_count' => 0,
                    'unread_customer_count' => 0,
                    'last_message_at' => now(),
                ]
            );

            // Cek apakah pesan pembuka custom sudah pernah dibuat untuk thread ini
            $alreadySent = $thread->messages()
                ->where('sender_type', 'customer')
                ->where('message', 'like', "%[Pesanan Custom Cake Baru - {$order->kode_pesanan}]%")
                ->exists();

            if (! $alreadySent) {
                $categoryName = $cakeOption->category?->nama_kategori ?? 'Kue Custom';
                $productName = $baseProduct ? $baseProduct->nama_kue : '-';
                $opsiPengiriman = $order->opsi_pengiriman instanceof OpsiPengiriman
                    ? $order->opsi_pengiriman->label()
                    : ucfirst((string) $order->opsi_pengiriman);
                $metodeBayar = $order->metode_pembayaran instanceof MetodePembayaran
                    ? $order->metode_pembayaran->label()
                    : strtoupper((string) $order->metode_pembayaran);
                $tglAmbil = $order->tanggal_ambil
                    ? (Carbon::parse($order->tanggal_ambil)->translatedFormat('d F Y'))
                    : '-';
                $formattedTotal = 'Rp '.number_format($order->total_harga, 0, ',', '.');

                $templateMessage = "🍰 *[Pesanan Custom Cake Baru - {$order->kode_pesanan}]*\n\n"
                    ."Halo Penjual Nak Mbarep, saya baru saja mengonfirmasi pesanan custom cake dengan rincian berikut:\n\n"
                    ."📋 *Kode Pesanan:* {$order->kode_pesanan}\n"
                    ."👤 *Nama Pemesan:* {$order->nama_pemesan}\n"
                    ."📱 *Nomor WhatsApp:* {$order->nomor_wa}\n"
                    ."🚚 *Opsi Pengiriman:* {$opsiPengiriman}\n"
                    ."📅 *Tanggal Ambil/Kirim:* {$tglAmbil}\n"
                    ."📍 *Alamat Lengkap:* {$order->alamat_lengkap}\n\n"
                    ."🎂 *Spesifikasi Kue:*\n"
                    ."• *Kategori:* {$categoryName}\n"
                    ."• *Produk Acuan:* {$productName}\n"
                    ."• *Varian/Ukuran:* {$cakeOption->label()}\n"
                    ."• *Jumlah:* {$jumlah} pcs\n"
                    ."• *Deskripsi Request:* {$draft['deskripsi_custom']}\n\n"
                    ."💳 *Metode Pembayaran:* {$metodeBayar}\n"
                    ."💰 *Estimasi Total Harga:* {$formattedTotal}\n\n"
                    .'Mohon dikonfirmasi ketersediaan dan detail pesanannya ya. Terima kasih!';

                ChatMessage::create([
                    'thread_id' => $thread->id,
                    'sender_type' => 'customer',
                    'sender_id' => null,
                    'sender_name' => $order->nama_pemesan,
                    'message' => $templateMessage,
                    'is_read' => false,
                ]);

                $thread->increment('unread_admin_count');
                $thread->update(['last_message_at' => now()]);
            }

            // Simpan status verifikasi di session browser
            session([
                'customer_active_thread_id' => $thread->id,
                'customer_plain_access_token' => $thread->plainAccessToken,
                'customer_verified_phone' => $normalizedPhone,
                'customer_verified_order' => $order->kode_pesanan,
                'customer_chat_phone' => $normalizedPhone,
                'customer_chat_name' => $order->nama_pemesan,
                'customer_chat_order' => $order->kode_pesanan,
            ]);

            return $order;
        });

        session()->forget(['custom_order_draft', 'temp_foto', 'customer_chat_pin']);

        return redirect()->route('customer.pesanan.sukses', $order->kode_pesanan);
    }
}
