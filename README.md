# 🍰 Web E-Commerce Platform - Toko Kue UMKM

Sistem Informasi Penjualan dan Pemesanan Kue (Katalog Regulasi & Custom Cake Dynamic Pricing) berbasis Web yang dikembangkan menggunakan **Laravel**.

---

## 🚀 Fitur Unggulan

### 🛒 Sisi Pembeli (Buyer)
- **Landing Page & Top 10 Terlaris:** Menampilkan produk populer yang dinamis berdasarkan transaksi.
- **Katalog Produk Regular:** Informasi detail produk, harga, dan pesanan cepat.
- **Form Custom Cake 2-Step:**
  - Pilihan bentuk, ukuran, dan harga dasar kue secara dinamis.
  - **Dynamic Add-ons (Checkbox):** Pilihan rasa/kulit/bahan tambahan dengan kalkulasi harga otomatis.
  - Upload foto referensi desain kue.
- **Halaman Review / Recap Pesanan:** Konfirmasi rincian harga (`(Harga Dasar + Add-ons) x Jumlah`) dan preview foto sebelum submission final.

### ⚙️ Sisi Penjual (Admin Dashboard)
- **Dashboard Manajemen:** Monitoring pesanan masuk dan pembaruan status transaksi.
- **CRUD Produk & Kategori:** Kelola item kue regular.
- **Pengaturan Custom Cake (Dynamic Configuration):**
  - Kelola varian ukuran & bentuk kue beserta harganya.
  - Kelola daftar add-ons / varian tambahan berbayar tanpa *hardcoding*.

---

## 🛠️ Tech Stack

- **Framework:** Laravel 11.x
- **Language:** PHP 8.x
- **Database:** MySQL / MariaDB / SQLite
- **Styling:** CSS / Tailwind CSS / Bootstrap
- **Testing:** PHPUnit (Automated Feature & Schema Tests)

---

## ⚙️ Cara Instalasi & Menjalankan Local Project

1. **Clone Repository**
   ```bash
   git clone [https://github.com/Dead-Disk/toko-kue-umkm.git](https://github.com/Dead-Disk/toko-kue-umkm.git)
   cd toko-kue-umkm
   ```

2. **Install Dependencies**
    ```Bash

    composer install
    npm install 
    ```

    Konfigurasi Environment
    Bash

    cp .env.example .env
    php artisan key:generate

    Migrasi Database & Seeder
    Bash

    php artisan migrate --seed

    Link Storage (Untuk Foto Referensi & Produk)
    Bash

    php artisan storage:link

    Jalankan Local Server
    Bash

    php artisan serve

    Akses aplikasi di browser melalui http://127.0.0.1:8000.

📝 Pengujian (Testing)

Proyek ini telah dilengkapi dengan pengujian otomatis untuk validasi skema database dan alur logika pesanan:
Bash

php artisan test

Dikembangkan untuk Tugas Kelompok SMK RPL - UMKM Toko Kue.