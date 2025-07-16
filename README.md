Sistem Absensi & Penggajian Karyawan v1.0
Sebuah aplikasi web Human Resource Information System (HRIS) modern yang dibangun dari nol menggunakan Laravel 12. Proyek ini dirancang sebagai studi kasus nyata untuk mempelajari arsitektur Laravel secara profesional, mulai dari autentikasi multi-role, validasi data, hingga fitur kompleks seperti penggajian dan pembuatan laporan PDF.

Sistem ini mengelola alur kerja absensi, pengajuan lembur, cuti/izin, serta perhitungan gaji otomatis, yang semuanya dapat diakses melalui antarmuka yang bersih, responsif, dan aman.

✨ Kutipan
"Aku tidak berilmu; yang berilmu hanyalah DIA. Jika tampak ilmu dariku, itu hanyalah pantulan dari Cahaya-Nya."

✨ Fitur Utama
Sistem ini dilengkapi dengan serangkaian fitur komprehensif untuk manajemen karyawan:

👤 Autentikasi Multi-Role: Sistem login yang aman dengan tiga tingkat akses berbeda:

Admin: Memiliki akses penuh ke seluruh sistem, termasuk pengaturan, manajemen user, dan laporan.

Atasan: Dapat menyetujui atau menolak pengajuan lembur dan cuti dari karyawan.

Karyawan: Dapat melakukan absensi dan mengajukan lembur atau cuti.

✅ Absensi Real-time:

Validasi QR Code: Absensi dilakukan dengan memindai QR Code unik yang berubah setiap hari, memastikan kehadiran fisik.

Validasi GPS & Radius: Sistem mencatat koordinat GPS karyawan saat absen dan memvalidasinya dengan lokasi dan radius kantor yang telah ditetapkan oleh Admin.

🕒 Manajemen Lembur & Cuti:

Alur pengajuan dari Karyawan ke Atasan.

Fitur persetujuan (Approve/Reject) dengan kolom catatan.

Upload dokumen bukti (misalnya, surat sakit) saat mengajukan izin.

💰 Penggajian Otomatis:

Admin dapat mengatur Gaji Pokok dan Tarif Lembur untuk setiap karyawan.

Sistem secara otomatis menghitung total gaji berdasarkan kehadiran dan total jam lembur yang disetujui.

Cetak Slip Gaji PDF: Menghasilkan dokumen slip gaji profesional dalam format PDF untuk setiap karyawan.

📊 Pelaporan & Audit:

Laporan Absensi Bulanan: Menampilkan rekapitulasi kehadiran seluruh karyawan dalam format kalender, lengkap dengan highlight untuk keterlambatan.

Audit Log: Mencatat semua aktivitas penting (pembuatan, pembaruan data) untuk keamanan dan pelacakan.

⚙️ Pengaturan Sistem Dinamis:

Admin dapat dengan mudah mengubah parameter inti sistem seperti jam kerja, lokasi kantor, radius absensi, dan lainnya melalui antarmuka.

🛠️ Teknologi yang Digunakan
Proyek ini dibangun menggunakan tumpukan teknologi modern dan standar industri:

Backend: PHP 8.2, Laravel 12

Frontend: Blade, Tailwind CSS, Alpine.js

Database: MySQL

Server Lokal: XAMPP

Library Utama:

laravel/breeze: Starter kit untuk autentikasi.

barryvdh/laravel-dompdf: Untuk generate PDF.

simplesoftwareio/simple-qrcode: Untuk generate QR Code.

html5-qrcode: Library JavaScript untuk memindai QR Code.

📋 Spesifikasi Sistem
Untuk menjalankan proyek ini di lingkungan lokal, Anda memerlukan:

PHP versi 8.2 atau lebih tinggi.

Composer 2.

Node.js & NPM.

Web Server (disarankan menggunakan XAMPP atau sejenisnya).

Database Server (MySQL/MariaDB).

🚀 Panduan Instalasi
Ikuti langkah-langkah ini dengan teliti untuk menjalankan proyek di komputer lokal Anda dari awal.

1. Clone Repositori
Buka terminal atau Git Bash, masuk ke direktori kerja Anda (misalnya C:/xampp/htdocs), lalu jalankan perintah berikut:

git clone https://github.com/Alghifari888/sistem_cuti_karyawan.git

Pindah ke direktori proyek yang baru dibuat:

cd sistem_cuti_karyawan

2. Instal Dependensi
Install semua package PHP yang dibutuhkan melalui Composer.

composer install

Install semua package JavaScript yang dibutuhkan melalui NPM.

npm install

3. Konfigurasi Lingkungan (.env)
Salin file .env.example menjadi file .env baru. File ini akan menyimpan semua konfigurasi rahasia Anda.

copy .env.example .env

Selanjutnya, generate kunci aplikasi yang unik.

php artisan key:generate

4. Konfigurasi Database
Buka file .env yang baru saja Anda buat.

Sesuaikan konfigurasi database berikut dengan pengaturan XAMPP Anda. Biasanya, konfigurasinya seperti di bawah ini (password dikosongkan).

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_sistem_absensi
DB_USERNAME=root
DB_PASSWORD=

Buka phpMyAdmin, dan buat database baru dengan nama yang sama persis seperti di .env (contoh: db_sistem_absensi).

5. Migrasi & Seeding Database
Jalankan perintah ini untuk membuat semua tabel yang dibutuhkan di database dan mengisinya dengan data awal (user admin, atasan, karyawan).

php artisan migrate:fresh --seed

Perintah ini akan membuat semua tabel dan 3 user default:

Admin: admin@example.com

Atasan: atasan@example.com

Karyawan: karyawan@example.com
(Password untuk semua user adalah: password)

6. Buat Storage Link
Jalankan perintah ini untuk membuat shortcut dari public/storage ke storage/app/public. Ini penting agar file yang di-upload (seperti bukti cuti) bisa diakses dari browser.

php artisan storage:link

7. Jalankan Aplikasi
Terakhir, jalankan server pengembangan Laravel dan proses kompilasi aset frontend.

Buka satu terminal dan jalankan:

php artisan serve

Buka terminal kedua dan jalankan:

npm run dev

Aplikasi Anda sekarang berjalan dan bisa diakses di http://localhost:8000.

📁 Struktur Folder & File
Berikut adalah struktur folder dan file penting yang telah kita buat dalam proyek ini.

sistem-absensi/
├── app/
│   ├── Console/
│   │   ├── Commands/
│   │   │   └── GenerateDailyQrToken.php  # Perintah untuk generate QR harian
│   │   └── Kernel.php                      # Tempat penjadwalan tugas
│   ├── Helpers/
│   │   └── LocationHelper.php            # Fungsi penghitung jarak GPS
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── AuditLogController.php
│   │   │   │   ├── PayrollController.php
│   │   │   │   ├── QrCodeController.php
│   │   │   │   └── UserController.php
│   │   │   ├── Atasan/
│   │   │   │   ├── LeaveController.php
│   │   │   │   └── OvertimeController.php
│   │   │   ├── Karyawan/
│   │   │   │   ├── LeaveController.php
│   │   │   │   └── OvertimeController.php
│   │   │   ├── Laporan/
│   │   │   │   └── AbsensiController.php
│   │   │   ├── Auth/
│   │   │   └── AttendanceController.php
│   │   └── Middleware/
│   │       └── EnsureUserHasRole.php       # Middleware untuk validasi role
│   ├── Models/
│   │   ├── Attendance.php
│   │   ├── AuditLog.php
│   │   ├── Leave.php
│   │   ├── Overtime.php
│   │   ├── Setting.php
│   │   └── User.php
│   ├── Observers/
│   │   ├── LeaveObserver.php
│   │   ├── OvertimeObserver.php
│   │   └── UserObserver.php                # Mengamati perubahan data User
│   └── Providers/
│       ├── AppServiceProvider.php          # Mendaftarkan Observer
│       └── AuthServiceProvider.php         # Mendaftarkan Policy
├── config/
│   └── app.php                           # Konfigurasi aplikasi & pendaftaran package
├── database/
│   ├── migrations/                       # Semua file "cetak biru" database
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── UserSeeder.php                  # Data user awal
├── public/
│   └── storage/                          # Shortcut ke file yang di-upload
├── resources/
│   └── views/                            # Semua file tampilan Blade
├── routes/
│   └── web.php                           # Semua route aplikasi
└── composer.json                         # Mendaftarkan file helper

📣 Panduan Kontribusi
Kami sangat terbuka untuk kontribusi dari komunitas. Anda bisa berkontribusi melalui dua cara:

Melalui Fork (Jika Belum Jadi Kolaborator)
Ini adalah cara standar untuk berkontribusi pada proyek open-source di GitHub.

Fork Repositori: Klik tombol "Fork" di pojok kanan atas halaman ini. Ini akan membuat salinan repositori di akun GitHub Anda.

Clone Fork Anda: Clone repositori yang sudah Anda fork ke komputer lokal.

git clone https://github.com/NAMA_ANDA/sistem_cuti_karyawan.git

Buat Branch Baru: Buat branch baru untuk fitur atau perbaikan yang akan Anda kerjakan.

git checkout -b fitur/nama-fitur-baru

Lakukan Perubahan: Buat kode Anda di branch baru ini.

Commit & Push: Commit perubahan Anda dengan pesan yang jelas, lalu push ke fork Anda.

git add .
git commit -m "feat: Menambahkan fitur X yang luar biasa"
git push origin fitur/nama-fitur-baru

Buat Pull Request: Kembali ke halaman repositori asli, dan Anda akan melihat tombol untuk membuat "Pull Request" dari branch baru Anda. Klik tombol tersebut, berikan judul dan penjelasan yang detail, lalu kirim.

🚀 Panduan Sebagai Kolaborator Langsung (Sudah Diundang)
Jika Anda sudah diundang sebagai kolaborator, alurnya sedikit lebih sederhana.

Clone Repositori Asli:

git clone https://github.com/Alghifari888/sistem_cuti_karyawan.git

Buat Branch Baru: Selalu bekerja di branch baru, jangan pernah di main.

git checkout -b fix/perbaikan-bug-login

Lakukan Perubahan, Commit, & Push:

git add .
git commit -m "fix: Memperbaiki masalah tampilan pada halaman login"
git push origin fix/perbaikan-bug-login

Buat Pull Request: Buka halaman repositori di GitHub dan buat Pull Request dari branch Anda ke branch main.

🖥️ Jalankan proyek secara lokal (jika blm setup apapun pemula)
Cukup ikuti Panduan Instalasi di atas dari langkah 1 sampai 7.

✅ Pedoman Kontribusi
Gaya Kode: Ikuti standar PSR-12 dan gaya kode Laravel. Jalankan composer lint untuk merapikan kode secara otomatis.

Pesan Commit: Gunakan format Conventional Commits. Contoh: feat:, fix:, docs:, style:, refactor:.

Satu PR, Satu Fitur: Usahakan setiap Pull Request fokus pada satu fitur atau perbaikan agar mudah di-review.

Terima kasih telah berkontribusi! 🙌
