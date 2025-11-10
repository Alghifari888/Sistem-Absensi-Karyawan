<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3-38B2AC?style=for-the-badge&logo=tailwind-css)
</p>
# 💼 Sistem Absensi & Penggajian Karyawan (Laravel 12)

Selamat datang di Sistem Absensi & HRIS, sebuah aplikasi web komprehensif yang dibangun dari nol menggunakan Laravel 12. Proyek ini dirancang sebagai studi kasus nyata untuk mendemonstrasikan implementasi arsitektur Laravel yang bersih, aman, dan profesional.

Aplikasi ini mengelola alur kerja esensial manajemen sumber daya manusia, mulai dari absensi harian dengan validasi canggih, alur persetujuan lembur dan cuti, hingga perhitungan gaji otomatis yang siap cetak dalam format PDF. Semua disajikan dalam antarmuka yang modern, responsif, dan intuitif berkat Tailwind CSS.

## ✨ Kutipan
> "Aku tidak berilmu; yang berilmu hanyalah DIA. Jika tampak ilmu dariku, itu hanyalah pantulan dari Cahaya-Nya."

## 📖 Daftar Isi
1. [Fitur Utama](#-fitur-utama)
2. [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
3. [Panduan Instalasi](#-panduan-instalasi)
4. [Akun Demo untuk Login](#-akun-demo-untuk-login)
5. [Struktur Folder](#-struktur-folder--file)
6. [Panduan Kontribusi](#-panduan-kontribusi)

   ## 🎥 Demo
![Demo](./ujicoba.gif)

## ✨ Fitur Utama
Sistem ini dilengkapi dengan serangkaian fitur komprehensif untuk manajemen karyawan:

### 👤 Autentikasi Multi-Role
Sistem login yang aman dengan tiga tingkat akses berbeda:
- **Admin**: Memiliki akses penuh ke seluruh sistem, termasuk manajemen pengguna dan pengaturan.
- **Atasan**: Dapat menyetujui atau menolak pengajuan lembur dan cuti dari bawahannya.
- **Karyawan**: Dapat melakukan absensi, melihat riwayat, dan mengajukan lembur atau cuti.

### ✅ Absensi Real-time dengan Validasi Canggih
- **Validasi QR Code**: Kode QR unik yang digenerate setiap hari untuk mencegah kecurangan.
- **Validasi GPS & Radius**: Memastikan karyawan hanya bisa absen di dalam radius lokasi kantor yang telah ditentukan.

### 🕒 Manajemen Lembur & Cuti
- Alur pengajuan yang terstruktur dari Karyawan ke Atasan.
- Fitur persetujuan (Approve/Reject) dengan kolom catatan untuk feedback.
- Kemampuan untuk mengunggah dokumen pendukung (misalnya, surat sakit).

### 💰 Penggajian Otomatis
- Perhitungan gaji yang akurat berdasarkan data kehadiran, potongan, dan total jam lembur.
- Fitur cetak Slip Gaji individual dalam format PDF yang profesional.

### 📊 Pelaporan & Audit
- Laporan Absensi Bulanan per karyawan.
- Audit Log untuk melacak semua aktivitas penting yang terjadi di dalam sistem.

### ⚙️ Pengaturan Sistem Dinamis
- Konfigurasi jam kerja (masuk dan pulang), lokasi kantor (latitude & longitude), serta radius absensi yang valid.

## 🛠️ Teknologi yang Digunakan
| Komponen       | Teknologi                               |
|----------------|-----------------------------------------|
| Backend        | PHP 8.2, Laravel 12                     |
| Frontend       | Blade, Tailwind CSS, Alpine.js          |
| Database       | MySQL / MariaDB                         |
| Server Lokal   | XAMPP / Laragon                         |
| Library Utama  | laravel/breeze, barryvdh/laravel-dompdf |

## 🚀 Panduan Instalasi

Bagian ini memuat tiga skenario instalasi: untuk pengembangan lokal, deployment ke server VPS (atau shared hosting dengan SSH), dan deployment ke shared hosting tanpa SSH (via cPanel).

---

### A. Instalasi di Komputer Lokal (Untuk Pengembangan)

Langkah-langkah ini ditujukan untuk menyiapkan lingkungan pengembangan di mesin Anda sendiri (misalnya, laptop atau PC).

#### Prasyarat
Pastikan perangkat lunak berikut sudah terinstal di komputer Anda:
- **PHP 8.2+**: [Download PHP](https://www.php.net/downloads.php)
- **Composer**: [Get Composer](https://getcomposer.org/download/)
- **Node.js 16+** (beserta npm): [Download Node.js](https://nodejs.org/)
- **Server Database** (MySQL 5.7+ atau MariaDB): [XAMPP](https://www.apachefriends.org/index.html) atau [Laragon](https://laragon.org/) adalah pilihan populer.
- **Git**: [Download Git](https://git-scm.com/downloads)

#### Langkah-langkah Instalasi

1.  **Clone Repositori**
    Buka terminal, navigasikan ke direktori kerja Anda, lalu jalankan:
    ```bash
    git clone [https://github.com/Alghifari888/sistem_cuti_karyawan.git](https://github.com/Alghifari888/sistem_cuti_karyawan.git)
    cd sistem_cuti_karyawan
    ```

2.  **Install Dependensi (Backend & Frontend)**
    ```bash
    composer install
    npm install
    ```

3.  **Konfigurasi Lingkungan (.env)**
    Salin file konfigurasi contoh dan generate kunci aplikasi unik.
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Setup Database**
    - Buat sebuah database baru di server MySQL Anda (misalnya, melalui phpMyAdmin) dengan nama `db_sistem_absensi`.
    - Buka file `.env` dan sesuaikan konfigurasinya:
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=db_sistem_absensi
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5.  **Migrasi & Seeding Database**
    Perintah ini akan membuat semua tabel dan mengisinya dengan data awal (akun admin, atasan, karyawan).
    ```bash
    php artisan migrate --seed
    ```

6.  **Buat Symbolic Link untuk Storage**
    Penting agar file yang di-upload dapat diakses dari web.
    ```bash
    php artisan storage:link
    ```

7.  **Jalankan Server Pengembangan**
    - **Terminal 1 (Vite):** Meng-compile aset CSS & JS.
      ```bash
      npm run dev
      ```
    - **Terminal 2 (Server Laravel):** Menjalankan aplikasi.
      ```bash
      php artisan serve
      ```

8.  **Akses Aplikasi**
    Buka `http://127.0.0.1:8000` di browser Anda.

---

### B. Deployment ke Server VPS / Shared Hosting (Dengan Akses SSH)

Panduan ini untuk server yang memberikan Anda akses terminal/SSH.

1.  **Clone Repositori**: Hubungkan via SSH, `cd` ke direktori web Anda, lalu `git clone ...`.
2.  **Konfigurasi .env Produksi**:
    - `cp .env.example .env`
    - Edit file `.env`: set `APP_ENV=production`, `APP_DEBUG=false`, dan isi detail database produksi.
    - Jalankan `php artisan key:generate`.
3.  **Install Dependensi Produksi**:
    - `composer install --optimize-autoloader --no-dev`
    - `npm install`
    - `npm run build`
4.  **Migrasi & Optimasi**:
    - `php artisan migrate --seed --force` (gunakan `--seed` hanya jika butuh data awal).
    - `php artisan config:cache`
    - `php artisan route:cache`
    - `php artisan view:cache`
5.  **Konfigurasi Web Server**: Arahkan *Document Root* ke folder `/public` proyek Anda.
6.  **Atur Hak Akses**: `sudo chown -R www-data:www-data storage bootstrap/cache` dan `sudo chmod -R 775 storage bootstrap/cache`.
7.  **Symbolic Link**: `php artisan storage:link`.
8.  **Setup Cron Job**: Untuk generate QR Code harian, tambahkan cron job berikut di server Anda (edit dengan `crontab -e`):
    ```cron
    * * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
    ```

---

### C. Deployment ke Shared Hosting (via cPanel / Tanpa SSH)

Metode ini bersifat manual jika tidak ada akses terminal.

1.  **Persiapan di Lokal**:
    - Jalankan `composer install --optimize-autoloader --no-dev` dan `npm run build`.
    - Hapus folder `node_modules`.
    - Kompres semua file proyek (termasuk folder `vendor`) ke dalam satu file `.zip`.

2.  **Unggah & Ekstrak di cPanel**:
    - Login ke cPanel, buka `File Manager`, masuk ke `public_html`.
    - `Upload` dan `Extract` file `.zip` Anda ke dalam sebuah folder (misal: `sistem-absensi`).

3.  **Atur Struktur Folder**:
    - Pindahkan semua isi dari `sistem-absensi/public` ke `public_html`.
    - Edit file `public_html/index.php`, ubah path-nya:
      ```php
      // Ganti 'sistem-absensi' dengan nama folder Anda
      require __DIR__.'/sistem-absensi/vendor/autoload.php';
      $app = require_once __DIR__.'/sistem-absensi/bootstrap/app.php';
      ```

4.  **Setup Database**:
    - Gunakan `MySQL Database Wizard` di cPanel untuk membuat database, user, dan password.
    - Ekspor database lokal Anda ke file `.sql`.
    - Impor file `.sql` tersebut ke database baru melalui `phpMyAdmin` di cPanel.

5.  **Konfigurasi Final**:
    - Edit file `.env` di dalam folder `sistem-absensi`.
    - Atur `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://domainanda.com`.
    - Masukkan detail koneksi database dari cPanel.
    - **Symbolic Link**: Minta bantuan support hosting Anda untuk membuat symbolic link dari `public_html/storage` ke `sistem-absensi/storage/app/public`.
    - **Cron Job**: Cari menu `Cron Jobs` di cPanel. Atur perintah untuk berjalan setiap hari:
      ```bash
      /usr/local/bin/php /home/user_cpanel/public_html/sistem-absensi/artisan schedule:run >> /dev/null 2>&1
      ```
      *(Path PHP mungkin berbeda, cek dokumentasi hosting Anda)*.

## 🔑 Akun Demo untuk Login
Setelah instalasi dan seeding berhasil, Anda dapat login menggunakan akun berikut:

-   **Admin**
    -   **Email:** `admin@example.com`
    -   **Password:** `password`

-   **Atasan**
    -   **Email:** `atasan@example.com`
    -   **Password:** `password`

-   **Karyawan**
    -   **Email:** `karyawan@example.com`
    -   **Password:** `password`

## 📁 Struktur Folder & File
```
sistem-absensi/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/      # Controller untuk admin
│   │   │   ├── Atasan/     # Controller untuk atasan
│   │   │   └── Karyawan/   # Controller untuk karyawan
│   ├── Models/             # Model database
│   └── Observers/          # Model observers
├── database/
│   ├── migrations/         # Skema database
│   └── seeders/            # Data awal
└── resources/
    └── views/              # Tampilan Blade
```

## 📣 Panduan Kontribusi

Kami menyambut baik kontribusi dari siapa pun.

### Melalui Fork (Untuk Non-Kolaborator)
1. Fork repositori ini.
2. Clone fork Anda: `git clone https://github.com/NAMA_ANDA/sistem_cuti_karyawan.git`
3. Buat branch baru: `git checkout -b fitur/nama-fitur-baru`
4. Lakukan perubahan, commit, dan push.
5. Buat Pull Request dari fork Anda ke repositori ini.

### ✅ Pedoman Kontribusi
- Ikuti standar PSR-12 dan gaya kode Laravel.
- Gunakan format Conventional Commits untuk pesan commit yang jelas.
- Fokus pada satu fitur atau perbaikan per Pull Request.

Terima kasih telah berkontribusi! 🙌
