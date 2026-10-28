<?php

use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\AdminStoreSettingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Pembeli (tanpa login)
|--------------------------------------------------------------------------
| Navigasi terpusat: halaman utama di '/', katalog produk di '/produk',
| dan halaman statis lain lewat '/p/{slug}' (mis. /p/tentang-kami).
*/
Route::get('/', [CustomerController::class, 'index'])->name('customer.index');
Route::get('/p/{slug}', [CustomerController::class, 'page'])
    ->where('slug', '[A-Za-z0-9\-]+')
    ->name('customer.page');

/*
| Katalog produk & alur pemesanan reguler (tanpa login).
| Ready di atas, habis di bawah, lalu form pesan dan simpan pesanan.
*/
Route::prefix('produk')->name('customer.produk.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
});

/*
| Keranjang belanja tamu (session, tanpa login).
*/
Route::prefix('keranjang')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/tambah/{product}', [CartController::class, 'add'])->name('add');
    Route::patch('/update/{product}', [CartController::class, 'update'])->name('update');
    Route::delete('/hapus/{product}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/kosongkan', [CartController::class, 'clear'])->name('clear');
});

Route::prefix('pesanan')->name('customer.pesanan.')->group(function () {
    Route::get('/review', [OrderController::class, 'reviewCart'])->name('review-cart');
    Route::post('/review', [OrderController::class, 'review'])->name('review');
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::get('/sukses/{kode_pesanan}', [OrderController::class, 'success'])
        ->where('kode_pesanan', '[A-Za-z0-9\-]+')
        ->name('sukses');
});

Route::prefix('customer')->name('customer.')->group(function () {
    // Pesanan custom cake: form -> review -> simpan final
    Route::get('/custom', [CustomerController::class, 'customForm'])->name('custom.create');
    Route::post('/custom', [CustomerController::class, 'reviewCustom'])->name('custom.review');
    Route::post('/custom/store', [CustomerController::class, 'storeCustom'])->name('custom.store');
});

/*
|--------------------------------------------------------------------------
| Autentikasi Penjual / Admin
|--------------------------------------------------------------------------
| Login memakai username + password. Route bernama 'login' wajib ada karena
| middleware 'auth' mengalihkan tamu ke sana.
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Web Penjual / Admin (wajib login)
|--------------------------------------------------------------------------
| Akses bersama (admin & staff): dashboard, pesanan, dan lihat stok produk.
| Khusus admin: CRUD produk, pesanan (hapus), pengaturan, dan kategori.
*/
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');

    Route::middleware('role:admin,staff')->group(function () {
        // Pesanan masuk & ubah status
        Route::get('pesanan', [AdminOrderController::class, 'index'])->name('pesanan.index');
        Route::patch('pesanan/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('pesanan.update-status');

        // Lihat status stok/inventory (read-only untuk staff)
        Route::get('produk', [AdminProductController::class, 'index'])->name('produk.index');

        // Kasir / POS offline: penjualan tunai langsung di toko.
        Route::get('kasir', [CashierController::class, 'index'])->name('kasir.index');
        Route::post('kasir/store', [CashierController::class, 'store'])->name('kasir.store');

        // File sensitif dari private storage: bukti pembayaran & foto custom cake.
        Route::get('pesanan/{order}/bukti-pembayaran', [AdminOrderController::class, 'viewBuktiPembayaran'])
            ->name('orders.bukti');
        Route::get('pesanan/{order}/custom-photo', [AdminOrderController::class, 'viewCustomPhoto'])
            ->name('orders.custom_photo');
    });

    Route::middleware('role:admin')->group(function () {
        // Produk (CRUD). Parameter URL tetap {product} agar cocok dengan route model binding.
        Route::resource('produk', AdminProductController::class)
            ->parameters(['produk' => 'product'])
            ->except(['show', 'index']);

        Route::delete('pesanan/{order}', [AdminOrderController::class, 'destroy'])->name('pesanan.destroy');

        // Pengaturan toko: profil, rekening bank, dan QRIS (single-row).
        Route::get('settings', [AdminStoreSettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [AdminStoreSettingController::class, 'update'])->name('settings.update');

        // Pengaturan Varian Custom Cake (CRUD)
        Route::resource('pengaturan', AdminSettingController::class)
            ->parameters(['pengaturan' => 'cakeOption'])
            ->except(['show']);

        // Kategori Kue (CRUD), ditampilkan di halaman Pengaturan.
        Route::prefix('pengaturan')->group(function () {
            Route::resource('kategori', AdminCategoryController::class)
                ->parameters(['kategori' => 'category'])
                ->except(['show']);
        });

        // Kelola akun admin/staf (khusus admin).
        Route::resource('users', UserController::class)->except(['show']);
    });
});
