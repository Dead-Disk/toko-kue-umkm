<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Batas Waktu Kedaluwarsa Pesanan Custom (Jam)
    |--------------------------------------------------------------------------
    |
    | Pesanan custom cake yang masih berstatus pending (belum ACC 100%,
    | selesai, atau dibatalkan) akan kedaluwarsa otomatis setelah durasi ini.
    |
    */
    'expiry_hours' => (int) env('CUSTOM_ORDER_EXPIRY_HOURS', 24),

    /*
    |--------------------------------------------------------------------------
    | Daftar Tambahan Nomor HP Penjual / Admin yang Diblokir
    |--------------------------------------------------------------------------
    |
    | Selain nomor dari StoreSetting dan akun admin/staff pada tabel users,
    | nomor tambahan di sini juga dilarang dipakai sebagai nomor pembeli tamu.
    |
    */
    'seller_phones' => array_values(array_filter(array_map(
        static fn (string $phone): string => trim($phone),
        explode(',', (string) env('CUSTOM_ORDER_SELLER_PHONES', ''))
    ))),
];
