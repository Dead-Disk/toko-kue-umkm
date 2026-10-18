<?php

namespace App\Http\Controllers;

use App\Enums\StatusPesanan;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    /**
     * Daftar pesanan masuk dari pembeli.
     */
    public function index(): View
    {
        $orders = Order::with(['items'])->withCount('items')->latest()->get();

        return view('admin.order.index', ['orders' => $orders]);
    }

    /**
     * Ubah status pesanan.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::enum(StatusPesanan::class)],
        ]);

        $order->update($data);

        return redirect()
            ->route('admin.pesanan.index')
            ->with('success', 'Status pesanan '.$order->kode_pesanan.' berhasil diperbarui.');
    }
}
