<?php

use App\Http\Controllers\AdminAddonController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
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

Route::prefix('pesanan')->name('customer.pesanan.')->group(function () {
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
*/
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');

    // Produk (CRUD). Parameter URL tetap {product} agar cocok dengan route model binding.
    Route::resource('produk', AdminProductController::class)
        ->parameters(['produk' => 'product'])
        ->except(['show']);

    // Pesanan masuk & ubah status
    Route::get('pesanan', [AdminOrderController::class, 'index'])->name('pesanan.index');
    Route::patch('pesanan/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('pesanan.update-status');

    // Pengaturan Varian Custom Cake (CRUD)
    Route::resource('pengaturan', AdminSettingController::class)
        ->parameters(['pengaturan' => 'cakeOption'])
        ->except(['show']);

    // Add-ons / varian tambahan custom cake (CRUD)
    Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
        Route::resource('addon', AdminAddonController::class)
            ->parameters(['addon' => 'cakeAddon'])
            ->except(['show', 'index']);
    });
});
