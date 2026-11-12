<?php

namespace App\Http\Controllers;

use App\Enums\StatusStok;
use App\Models\ActivityLog;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StaffProductStockController extends Controller
{
    /**
     * Perbarui stok dan/atau status ketersediaan produk langsung dari tabel
     * stok panel staf. Dipakai oleh permintaan AJAX tanpa reload halaman.
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'stok' => ['sometimes', 'integer', 'min:0', 'max:1000000', 'prohibits:delta'],
            'delta' => ['sometimes', 'integer', 'not_in:0', 'between:-1000000,1000000', 'prohibits:stok'],
            'status_stok' => ['sometimes', Rule::enum(StatusStok::class)],
        ]);

        if ($data === []) {
            throw ValidationException::withMessages([
                'stok' => 'Tidak ada perubahan stok yang dikirim.',
            ]);
        }

        $updatedProduct = DB::transaction(function () use ($data, $product): Product {
            $lockedProduct = Product::query()
                ->whereKey($product->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $oldStock = $lockedProduct->stok;
            $oldStatus = $lockedProduct->status_stok;

            $newStock = $oldStock;

            if (array_key_exists('stok', $data)) {
                $newStock = (int) $data['stok'];
            }

            if (array_key_exists('delta', $data)) {
                $newStock = max(0, $newStock + (int) $data['delta']);
            }

            $statusDiminta = array_key_exists('status_stok', $data);

            $newStatus = $statusDiminta
                ? StatusStok::from($data['status_stok'])
                : $oldStatus;

            // Stok kosong selalu berarti habis, termasuk saat staf mengurangi
            // stok menjadi nol dari tombol minus. Permintaan eksplisit untuk
            // menandai ready saat stok kosong tetap ditolak.
            if ($newStock <= 0) {
                if ($statusDiminta && $newStatus === StatusStok::Ready) {
                    throw ValidationException::withMessages([
                        'status_stok' => 'Stok produk masih kosong. Tambahkan stok terlebih dahulu sebelum menandai ready.',
                    ]);
                }

                $newStatus = StatusStok::Habis;
            }

            if ($newStock === $oldStock && $newStatus === $oldStatus) {
                return $lockedProduct;
            }

            $lockedProduct->update([
                'stok' => $newStock,
                'status_stok' => $newStatus,
            ]);

            $perubahan = [];

            if ($newStock !== $oldStock) {
                $perubahan[] = 'stok '.$oldStock.' menjadi '.$newStock;
            }

            if ($newStatus !== $oldStatus) {
                $perubahan[] = 'status '.$oldStatus->value.' menjadi '.$newStatus->value;
            }

            ActivityLog::record(
                'ubah',
                'Memperbarui produk '.$lockedProduct->nama_kue.': '.implode(', ', $perubahan).'.',
                $lockedProduct,
                [
                    'stok_lama' => $oldStock,
                    'stok_baru' => $newStock,
                    'status_lama' => $oldStatus->value,
                    'status_baru' => $newStatus->value,
                ]
            );

            return $lockedProduct;
        });

        return response()->json([
            'message' => 'Stok produk berhasil diperbarui.',
            'product' => [
                'id' => $updatedProduct->id,
                'nama_kue' => $updatedProduct->nama_kue,
                'stok' => $updatedProduct->stok,
                'status_stok' => $updatedProduct->status_stok->value,
                'is_ready' => $this->isReady($updatedProduct),
            ],
        ]);
    }

    /**
     * Produk dianggap ready bila statusnya ready dan stoknya masih tersedia.
     */
    private function isReady(Product $product): bool
    {
        return $product->status_stok === StatusStok::Ready && $product->stok > 0;
    }
}
