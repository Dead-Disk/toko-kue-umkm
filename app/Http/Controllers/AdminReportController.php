<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdminReportController extends Controller
{
    /**
     * Tampilkan halaman laporan penjualan.
     *
     * Data laporan belum diolah, sehingga halaman masih menampilkan empty
     * state sampai fitur laporan tersedia.
     */
    public function index(): View
    {
        return view('admin.reports.index');
    }
}
