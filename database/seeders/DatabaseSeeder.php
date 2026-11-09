<?php

namespace Database\Seeders;

use App\Enums\BentukKue;
use App\Enums\StatusStok;
use App\Models\CakeOption;
use App\Models\Category;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Pengaturan Toko (Store Settings)
        StoreSetting::updateOrCreate(
            ['id' => 1],
            [
                'store_name' => 'Nak Mbarep',
                'store_description' => 'Kue Lezat, Momen Lebih Manis',
                'store_address' => 'Jl. Letjend Suprapto Gang 1 No. 4, Kepatihan, Tulungagung, Jawa Timur',
                'store_phone' => '08123456789',
                'bank_name' => 'BCA',
                'bank_account_number' => '1234567890',
                'bank_account_holder' => 'Nak Mbarep Cake',
            ]
        );

        // 2. Akun Demo
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Nak Mbarep',
                'username' => 'admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'username' => 'testuser',
                'password' => bcrypt('password'),
                'role' => 'staff',
            ]
        );

        // 3. Kategori Kue
        $categories = [
            'Kue Tart' => 'kue-tart',
            'Roti Gulung' => 'roti-gulung',
            'Donat' => 'donat',
            'Spiku' => 'spiku',
        ];

        $categoryModels = [];
        foreach ($categories as $name => $slug) {
            $categoryModels[$name] = Category::firstOrCreate(
                ['slug' => $slug],
                ['nama_kategori' => $name]
            );
        }

        // 4. Produk Kue (Sesuai Katalog dan Rekomendasi di Beranda)
        $products = [
            [
                'nama_kue' => 'Tart Pink',
                'category_id' => $categoryModels['Kue Tart']->id,
                'slug' => 'tart-pink',
                'deskripsi' => 'Kue tart lembut berbalut krim pink lezat dengan hiasan buah ceri dan cokelat leleh. Ukuran 16cm, cocok untuk ulang tahun dan perayaan spesial.',
                'harga' => 65_000,
                'stok' => 15,
                'max_per_order' => 5,
                'status_stok' => StatusStok::Ready,
                'foto' => 'products/tart-pink.png',
            ],
            [
                'nama_kue' => 'Rainbow Cake',
                'category_id' => $categoryModels['Kue Tart']->id,
                'slug' => 'rainbow-cake',
                'deskripsi' => 'Rainbow cake warna-warni cantik dengan lapisan krim lembut dan taburan biskuit cokelat. Ukuran 14cm favorit anak-anak dan keluarga.',
                'harga' => 70_000,
                'stok' => 12,
                'max_per_order' => 5,
                'status_stok' => StatusStok::Ready,
                'foto' => 'products/rainbow-cake.png',
            ],
            [
                'nama_kue' => 'Tart Karakter Princess',
                'category_id' => $categoryModels['Kue Tart']->id,
                'slug' => 'tart-karakter-princess',
                'deskripsi' => 'Kue tart tingkat bertema karakter Princess cantik ukuran 16cm. Dibuat dengan bahan pilihan dan hiasan detail yang rapi.',
                'harga' => 90_000,
                'stok' => 8,
                'max_per_order' => 5,
                'status_stok' => StatusStok::Ready,
                'foto' => 'products/tart-princess.png',
            ],
            [
                'nama_kue' => 'Donat Glaze',
                'category_id' => $categoryModels['Donat']->id,
                'slug' => 'donat-glaze',
                'deskripsi' => 'Satu box donat glaze lembut isi 12 biji dengan aneka topping warna-warni lezat.',
                'harga' => 30_000,
                'stok' => 20,
                'max_per_order' => 5,
                'status_stok' => StatusStok::Ready,
                'foto' => 'products/donat-glaze.png',
            ],
            [
                'nama_kue' => 'Roti Gulung Roll',
                'category_id' => $categoryModels['Roti Gulung']->id,
                'slug' => 'roti-gulung-roll',
                'deskripsi' => 'Roti gulung lembut dengan isian selai manis lezat khas dapur Nak Mbarep.',
                'harga' => 45_000,
                'stok' => 10,
                'max_per_order' => 5,
                'status_stok' => StatusStok::Ready,
                'foto' => 'products/roti-gulung.png',
            ],
            [
                'nama_kue' => 'Spiku Lapis Surabaya',
                'category_id' => $categoryModels['Spiku']->id,
                'slug' => 'spiku-lapis-surabaya',
                'deskripsi' => 'Spiku lapis surabaya klasik dengan tekstur legit, aroma harum mentega spesial, dan rasa lezat.',
                'harga' => 55_000,
                'stok' => 10,
                'max_per_order' => 5,
                'status_stok' => StatusStok::Ready,
                'foto' => 'products/spiku.png',
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['slug' => $productData['slug']],
                $productData
            );
        }

        // 5. Opsi Custom Cake
        $cakeOptions = [
            ['bentuk' => BentukKue::Lingkaran, 'ukuran' => '16 cm', 'harga_dasar' => 120_000],
            ['bentuk' => BentukKue::Lingkaran, 'ukuran' => '18 cm', 'harga_dasar' => 140_000],
            ['bentuk' => BentukKue::Lingkaran, 'ukuran' => '20 cm', 'harga_dasar' => 160_000],
            ['bentuk' => BentukKue::Kotak, 'ukuran' => '18 cm', 'harga_dasar' => 150_000],
            ['bentuk' => BentukKue::Kotak, 'ukuran' => '20 cm', 'harga_dasar' => 175_000],
        ];

        foreach ($cakeOptions as $option) {
            CakeOption::firstOrCreate(
                ['bentuk' => $option['bentuk'], 'ukuran' => $option['ukuran']],
                $option
            );
        }
    }
}
