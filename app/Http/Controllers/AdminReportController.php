<?php

namespace App\Http\Controllers;

use App\Enums\StatusPesanan;
use App\Enums\StatusStok;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminReportController extends PanelController
{
    /**
     * Tampilkan halaman laporan penjualan dan stok dengan filter periode dinamis.
     */
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'penjualan');
        if (! in_array($tab, ['penjualan', 'stok'], true)) {
            $tab = 'penjualan';
        }

        $periode = (string) $request->query('periode', 'bulanan');
        $customStart = $request->query('start_date');
        $customEnd = $request->query('end_date');

        [$startDate, $endDate, $periodeLabel, $normalizedPeriode] = $this->resolveDateRange(
            $periode,
            is_string($customStart) ? $customStart : null,
            is_string($customEnd) ? $customEnd : null
        );

        $validStatuses = [StatusPesanan::Proses, StatusPesanan::Selesai];

        // 1. Ambil pesanan penjualan valid dalam rentang periode terpilih
        $salesOrders = Order::query()
            ->whereIn('status', $validStatuses)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['items.product.category'])
            ->latest()
            ->get();

        // 2. Ambil log aktivitas produk (khusus restok/perubahan & penambahan) dalam periode
        $stockLogs = ActivityLog::query()
            ->where('subject_type', 'Product')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();

        // 3. Ringkasan KPI Utama
        $totalOmzet = (int) $salesOrders->sum('total_harga');
        $totalTransaksi = $salesOrders->count();
        $rataRataTransaksi = $totalTransaksi > 0 ? (int) round($totalOmzet / $totalTransaksi) : 0;

        $totalProdukTerjual = 0;
        foreach ($salesOrders as $order) {
            $totalProdukTerjual += (int) $order->items->sum('jumlah');
        }

        $totalSisaStok = (int) Product::sum('stok');
        $totalProduk = Product::count();
        $totalProdukReady = Product::where('status_stok', StatusStok::Ready)->where('stok', '>', 0)->count();
        $totalProdukHabis = Product::where('status_stok', StatusStok::Habis)->orWhere('stok', '<=', 0)->count();
        $totalStokMasuk = $this->calculateStockInflow($stockLogs);

        // 4. Data Grafik
        $salesChart = $this->generateSalesChart($salesOrders, $normalizedPeriode, $startDate, $endDate);
        $stockChart = $this->generateStockChart($salesOrders, $stockLogs, $normalizedPeriode, $startDate, $endDate);

        // 5. Data Tabel Penjualan
        $productSales = $this->aggregateProductSales($salesOrders);

        // 6. Data Tabel Stok
        $productsInventory = Product::query()
            ->with('category')
            ->orderBy('nama_kue')
            ->get()
            ->map(function (Product $product) use ($salesOrders, $stockLogs): array {
                $soldInPeriod = 0;
                foreach ($salesOrders as $order) {
                    foreach ($order->items as $item) {
                        if ($item->product_id === $product->id) {
                            $soldInPeriod += (int) $item->jumlah;
                        }
                    }
                }

                $inflowInPeriod = 0;
                foreach ($stockLogs as $log) {
                    if ((string) $log->subject_id === (string) $product->id) {
                        $props = (array) ($log->properties ?? []);
                        if ($log->action === 'tambah') {
                            $inflowInPeriod += (int) ($props['stok_baru'] ?? $product->stok);
                        } elseif ($log->action === 'ubah') {
                            $old = (int) ($props['stok_lama'] ?? 0);
                            $new = (int) ($props['stok_baru'] ?? 0);
                            if ($new > $old) {
                                $inflowInPeriod += ($new - $old);
                            }
                        }
                    }
                }

                return [
                    'id' => $product->id,
                    'nama_kue' => $product->nama_kue,
                    'kategori' => $product->category?->nama_kategori ?? 'Tanpa Kategori',
                    'harga' => (int) $product->harga,
                    'stok' => (int) $product->stok,
                    'status_stok' => $product->status_stok,
                    'terjual_periode' => $soldInPeriod,
                    'masuk_periode' => $inflowInPeriod,
                    'nilai_stok' => (int) ($product->stok * $product->harga),
                ];
            });

        // 7. Riwayat Pergerakan Stok Gabungan
        $stockMovements = $this->buildStockMovements($salesOrders, $stockLogs);

        // 8. Pembagian Metode Pembayaran
        $metodePembayaran = $salesOrders->groupBy(fn (Order $o): string => $o->metode_pembayaran?->label() ?? 'Lainnya')
            ->map(fn (Collection $group): array => [
                'count' => $group->count(),
                'total' => (int) $group->sum('total_harga'),
            ]);

        return $this->panelView('reports.index', [
            'tab' => $tab,
            'periode' => $normalizedPeriode,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'periodeLabel' => $periodeLabel,
            'totalOmzet' => $totalOmzet,
            'totalProdukTerjual' => $totalProdukTerjual,
            'totalSisaStok' => $totalSisaStok,
            'totalTransaksi' => $totalTransaksi,
            'rataRataTransaksi' => $rataRataTransaksi,
            'totalStokMasuk' => $totalStokMasuk,
            'totalProduk' => $totalProduk,
            'totalProdukReady' => $totalProdukReady,
            'totalProdukHabis' => $totalProdukHabis,
            'salesOrders' => $salesOrders,
            'salesChart' => $salesChart,
            'stockChart' => $stockChart,
            'productSales' => $productSales,
            'productsInventory' => $productsInventory,
            'stockMovements' => $stockMovements,
            'metodePembayaran' => $metodePembayaran,
        ]);
    }

    /**
     * Tentukan batas tanggal awal dan akhir berdasarkan periode yang dipilih.
     *
     * @return array{0: Carbon, 1: Carbon, 2: string, 3: string}
     */
    private function resolveDateRange(string $periode, ?string $startInput, ?string $endInput): array
    {
        $now = Carbon::now();

        if (! in_array($periode, ['harian', 'mingguan', 'bulanan', 'kustom'], true)) {
            $periode = 'bulanan';
        }

        if ($periode === 'harian') {
            $startDate = $now->copy()->startOfDay();
            $endDate = $now->copy()->endOfDay();
            $periodeLabel = 'Hari Ini ('.$startDate->format('d M Y').')';

            return [$startDate, $endDate, $periodeLabel, 'harian'];
        }

        if ($periode === 'mingguan') {
            $startDate = $now->copy()->startOfWeek();
            $endDate = $now->copy()->endOfWeek();
            $periodeLabel = 'Minggu Ini ('.$startDate->format('d M').' - '.$endDate->format('d M Y').')';

            return [$startDate, $endDate, $periodeLabel, 'mingguan'];
        }

        if ($periode === 'kustom' && (filled($startInput) || filled($endInput))) {
            $startDate = $startInput ? Carbon::parse($startInput)->startOfDay() : $now->copy()->startOfMonth();
            $endDate = $endInput ? Carbon::parse($endInput)->endOfDay() : $now->copy()->endOfDay();
            $periodeLabel = $startDate->format('d M Y').' - '.$endDate->format('d M Y');

            return [$startDate, $endDate, $periodeLabel, 'kustom'];
        }

        $startDate = $now->copy()->startOfMonth();
        $endDate = $now->copy()->endOfMonth();
        $periodeLabel = 'Bulan Ini ('.$startDate->format('F Y').')';

        return [$startDate, $endDate, $periodeLabel, 'bulanan'];
    }

    /**
     * Susun data grafik penjualan (omzet & kuantitas) per interval waktu.
     *
     * @param  Collection<int, Order>  $salesOrders
     * @return array{points: list<array<string, mixed>>, maxOmzet: int, maxTerjual: int}
     */
    private function generateSalesChart(
        Collection $salesOrders,
        string $periode,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        $points = [];

        if ($periode === 'harian') {
            // 8 blok interval 2 jam: 06:00, 08:00, 10:00, 12:00, 14:00, 16:00, 18:00, 20:00
            $slots = [
                ['start' => 6, 'end' => 8, 'label' => '06:00-08:00'],
                ['start' => 8, 'end' => 10, 'label' => '08:00-10:00'],
                ['start' => 10, 'end' => 12, 'label' => '10:00-12:00'],
                ['start' => 12, 'end' => 14, 'label' => '12:00-14:00'],
                ['start' => 14, 'end' => 16, 'label' => '14:00-16:00'],
                ['start' => 16, 'end' => 18, 'label' => '16:00-18:00'],
                ['start' => 18, 'end' => 20, 'label' => '18:00-20:00'],
                ['start' => 20, 'end' => 23, 'label' => '20:00-23:00'],
            ];

            foreach ($slots as $slot) {
                $ordersInSlot = $salesOrders->filter(function (Order $order) use ($slot): bool {
                    $hour = (int) $order->created_at?->format('H');

                    return $hour >= $slot['start'] && $hour < $slot['end'];
                });

                $omzet = (int) $ordersInSlot->sum('total_harga');
                $terjual = 0;
                foreach ($ordersInSlot as $ord) {
                    $terjual += (int) $ord->items->sum('jumlah');
                }

                $points[] = [
                    'label' => $slot['label'],
                    'short_label' => sprintf('%02d:00', $slot['start']),
                    'omzet' => $omzet,
                    'terjual' => $terjual,
                    'transaksi' => $ordersInSlot->count(),
                ];
            }
        } elseif ($periode === 'mingguan') {
            // 7 hari (Senin s/d Minggu)
            $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            $cursor = $startDate->copy();

            while ($cursor <= $endDate) {
                $dateStr = $cursor->format('Y-m-d');
                $dayIndex = (int) $cursor->dayOfWeekIso - 1;
                $dayLabel = $dayNames[$dayIndex] ?? $cursor->format('D');

                $ordersOnDate = $salesOrders->filter(
                    fn (Order $o): bool => $o->created_at?->format('Y-m-d') === $dateStr
                );

                $omzet = (int) $ordersOnDate->sum('total_harga');
                $terjual = 0;
                foreach ($ordersOnDate as $ord) {
                    $terjual += (int) $ord->items->sum('jumlah');
                }

                $points[] = [
                    'label' => $dayLabel.' ('.$cursor->format('d/m').')',
                    'short_label' => $dayLabel,
                    'omzet' => $omzet,
                    'terjual' => $terjual,
                    'transaksi' => $ordersOnDate->count(),
                ];

                $cursor->addDay();
            }
        } else {
            // Bulanan: setiap hari dalam bulan terpilih
            $cursor = $startDate->copy();

            while ($cursor <= $endDate) {
                $dateStr = $cursor->format('Y-m-d');

                $ordersOnDate = $salesOrders->filter(
                    fn (Order $o): bool => $o->created_at?->format('Y-m-d') === $dateStr
                );

                $omzet = (int) $ordersOnDate->sum('total_harga');
                $terjual = 0;
                foreach ($ordersOnDate as $ord) {
                    $terjual += (int) $ord->items->sum('jumlah');
                }

                $points[] = [
                    'label' => 'Tgl '.$cursor->format('j M'),
                    'short_label' => $cursor->format('j'),
                    'omzet' => $omzet,
                    'terjual' => $terjual,
                    'transaksi' => $ordersOnDate->count(),
                ];

                $cursor->addDay();
            }
        }

        $maxOmzet = 0;
        $maxTerjual = 0;
        foreach ($points as $p) {
            if ($p['omzet'] > $maxOmzet) {
                $maxOmzet = $p['omzet'];
            }
            if ($p['terjual'] > $maxTerjual) {
                $maxTerjual = $p['terjual'];
            }
        }

        return [
            'points' => $points,
            'maxOmzet' => $maxOmzet,
            'maxTerjual' => $maxTerjual,
        ];
    }

    /**
     * Susun data grafik perbandingan pergerakan stok masuk vs keluar.
     *
     * @param  Collection<int, Order>  $salesOrders
     * @param  Collection<int, ActivityLog>  $stockLogs
     * @return array{points: list<array<string, mixed>>, maxMovement: int}
     */
    private function generateStockChart(
        Collection $salesOrders,
        Collection $stockLogs,
        string $periode,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        $points = [];

        if ($periode === 'harian') {
            $slots = [
                ['start' => 6, 'end' => 8, 'label' => '06:00-08:00'],
                ['start' => 8, 'end' => 10, 'label' => '08:00-10:00'],
                ['start' => 10, 'end' => 12, 'label' => '10:00-12:00'],
                ['start' => 12, 'end' => 14, 'label' => '12:00-14:00'],
                ['start' => 14, 'end' => 16, 'label' => '14:00-16:00'],
                ['start' => 16, 'end' => 18, 'label' => '16:00-18:00'],
                ['start' => 18, 'end' => 20, 'label' => '18:00-20:00'],
                ['start' => 20, 'end' => 23, 'label' => '20:00-23:00'],
            ];

            foreach ($slots as $slot) {
                $keluar = 0;
                foreach ($salesOrders as $ord) {
                    $hour = (int) $ord->created_at?->format('H');
                    if ($hour >= $slot['start'] && $hour < $slot['end']) {
                        $keluar += (int) $ord->items->sum('jumlah');
                    }
                }

                $masuk = 0;
                foreach ($stockLogs as $log) {
                    $hour = (int) $log->created_at?->format('H');
                    if ($hour >= $slot['start'] && $hour < $slot['end']) {
                        $masuk += $this->extractLogInflow($log);
                    }
                }

                $points[] = [
                    'label' => $slot['label'],
                    'short_label' => sprintf('%02d:00', $slot['start']),
                    'keluar' => $keluar,
                    'masuk' => $masuk,
                ];
            }
        } elseif ($periode === 'mingguan') {
            $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            $cursor = $startDate->copy();

            while ($cursor <= $endDate) {
                $dateStr = $cursor->format('Y-m-d');
                $dayIndex = (int) $cursor->dayOfWeekIso - 1;
                $dayLabel = $dayNames[$dayIndex] ?? $cursor->format('D');

                $keluar = 0;
                foreach ($salesOrders as $ord) {
                    if ($ord->created_at?->format('Y-m-d') === $dateStr) {
                        $keluar += (int) $ord->items->sum('jumlah');
                    }
                }

                $masuk = 0;
                foreach ($stockLogs as $log) {
                    if ($log->created_at?->format('Y-m-d') === $dateStr) {
                        $masuk += $this->extractLogInflow($log);
                    }
                }

                $points[] = [
                    'label' => $dayLabel.' ('.$cursor->format('d/m').')',
                    'short_label' => $dayLabel,
                    'keluar' => $keluar,
                    'masuk' => $masuk,
                ];

                $cursor->addDay();
            }
        } else {
            $cursor = $startDate->copy();

            while ($cursor <= $endDate) {
                $dateStr = $cursor->format('Y-m-d');

                $keluar = 0;
                foreach ($salesOrders as $ord) {
                    if ($ord->created_at?->format('Y-m-d') === $dateStr) {
                        $keluar += (int) $ord->items->sum('jumlah');
                    }
                }

                $masuk = 0;
                foreach ($stockLogs as $log) {
                    if ($log->created_at?->format('Y-m-d') === $dateStr) {
                        $masuk += $this->extractLogInflow($log);
                    }
                }

                $points[] = [
                    'label' => 'Tgl '.$cursor->format('j M'),
                    'short_label' => $cursor->format('j'),
                    'keluar' => $keluar,
                    'masuk' => $masuk,
                ];

                $cursor->addDay();
            }
        }

        $maxMovement = 0;
        foreach ($points as $p) {
            if ($p['keluar'] > $maxMovement) {
                $maxMovement = $p['keluar'];
            }
            if ($p['masuk'] > $maxMovement) {
                $maxMovement = $p['masuk'];
            }
        }

        return [
            'points' => $points,
            'maxMovement' => $maxMovement,
        ];
    }

    /**
     * Hitung total penambahan stok dari kumpulan log aktivitas produk.
     *
     * @param  Collection<int, ActivityLog>  $stockLogs
     */
    private function calculateStockInflow(Collection $stockLogs): int
    {
        $totalInflow = 0;
        foreach ($stockLogs as $log) {
            $totalInflow += $this->extractLogInflow($log);
        }

        return $totalInflow;
    }

    /**
     * Ekstrak nominal stok masuk dari satu baris log aktivitas.
     */
    private function extractLogInflow(ActivityLog $log): int
    {
        $props = (array) ($log->properties ?? []);

        if ($log->action === 'tambah') {
            if (isset($props['stok_baru'])) {
                return (int) $props['stok_baru'];
            }

            if (preg_match('/Stok:\s*(\d+)/i', $log->description, $match)) {
                return (int) $match[1];
            }

            return 0;
        }

        if ($log->action === 'ubah') {
            $old = (int) ($props['stok_lama'] ?? 0);
            $new = (int) ($props['stok_baru'] ?? 0);

            if ($new > $old) {
                return $new - $old;
            }
        }

        return 0;
    }

    /**
     * Gabungkan penjualan per produk untuk tabel ringkasan penjualan produk.
     *
     * @param  Collection<int, Order>  $salesOrders
     * @return Collection<int, array<string, mixed>>
     */
    private function aggregateProductSales(Collection $salesOrders): Collection
    {
        $map = [];

        foreach ($salesOrders as $order) {
            foreach ($order->items as $item) {
                $key = $item->product_id !== null
                    ? 'p_'.$item->product_id
                    : 'custom_'.$item->id;

                if (! isset($map[$key])) {
                    $namaKue = $item->product?->nama_kue;
                    if ($namaKue === null) {
                        $namaKue = filled($item->deskripsi_custom)
                            ? 'Custom: '.Str::limit($item->deskripsi_custom, 30)
                            : 'Kue Custom';
                    }

                    $map[$key] = [
                        'product_id' => $item->product_id,
                        'nama_kue' => $namaKue,
                        'kategori' => $item->product?->category?->nama_kategori ?? 'Kue Custom',
                        'harga' => $item->product?->harga ?? ($item->jumlah > 0 ? (int) round($item->subtotal / $item->jumlah) : 0),
                        'sisa_stok' => $item->product?->stok ?? 0,
                        'terjual' => 0,
                        'omzet' => 0,
                    ];
                }

                $map[$key]['terjual'] += (int) $item->jumlah;
                $map[$key]['omzet'] += (int) $item->subtotal;
            }
        }

        return collect($map)
            ->sortByDesc('omzet')
            ->values();
    }

    /**
     * Buat riwayat pergerakan stok gabungan (stok keluar penjualan + stok masuk restok/log).
     *
     * @param  Collection<int, Order>  $salesOrders
     * @param  Collection<int, ActivityLog>  $stockLogs
     * @return Collection<int, array<string, mixed>>
     */
    private function buildStockMovements(Collection $salesOrders, Collection $stockLogs): Collection
    {
        $movements = collect();

        // 1. Catatan stok keluar dari pesanan penjualan
        foreach ($salesOrders as $order) {
            foreach ($order->items as $item) {
                if ($item->product_id === null) {
                    continue;
                }

                $movements->push([
                    'waktu' => $order->created_at ?? Carbon::now(),
                    'produk' => $item->product?->nama_kue ?? 'Produk #'.$item->product_id,
                    'tipe' => 'keluar',
                    'jumlah' => (int) $item->jumlah,
                    'keterangan' => 'Penjualan: Pesanan #'.$order->kode_pesanan.' ('.$order->nama_pemesan.')',
                    'actor' => $order->nama_pemesan,
                ]);
            }
        }

        // 2. Catatan stok masuk atau penyesuaian dari activity logs
        foreach ($stockLogs as $log) {
            $inflow = $this->extractLogInflow($log);
            $props = (array) ($log->properties ?? []);

            if ($inflow > 0) {
                $movements->push([
                    'waktu' => $log->created_at ?? Carbon::now(),
                    'produk' => $this->parseProductNameFromLog($log),
                    'tipe' => 'masuk',
                    'jumlah' => $inflow,
                    'keterangan' => $log->description,
                    'actor' => $log->user_name,
                ]);
            } elseif ($log->action === 'ubah' && isset($props['stok_lama'], $props['stok_baru'])) {
                $old = (int) $props['stok_lama'];
                $new = (int) $props['stok_baru'];
                if ($old > $new) {
                    $movements->push([
                        'waktu' => $log->created_at ?? Carbon::now(),
                        'produk' => $this->parseProductNameFromLog($log),
                        'tipe' => 'keluar',
                        'jumlah' => ($old - $new),
                        'keterangan' => 'Penyesuaian manual: '.$log->description,
                        'actor' => $log->user_name,
                    ]);
                }
            }
        }

        return $movements->sortByDesc('waktu')->values();
    }

    /**
     * Ambil nama produk dari deskripsi log aktivitas.
     */
    private function parseProductNameFromLog(ActivityLog $log): string
    {
        if (preg_match('/produk\s+([^:.]+)/i', $log->description, $matches)) {
            return trim($matches[1]);
        }

        return 'Produk #'.($log->subject_id ?? '-');
    }
}
