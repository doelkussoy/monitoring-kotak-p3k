# Sistem Sarana Prasarana (CBA)

Sistem Sarana Prasarana adalah aplikasi berbasis web yang dirancang untuk mengelola dan memantau pemeliharaan fasilitas perusahaan secara terpusat. Aplikasi ini membantu tim maintenance dalam mencatat pemeriksaan rutin, kondisi aset, dan mencetak kartu riwayat perawatan untuk berbagai kategori sarana prasarana.

## 🚀 Fitur Utama

Aplikasi ini terdiri dari empat modul utama yang saling terintegrasi:

1.  **Perawatan APAR (Alat Pemadam Api Ringan)**
    *   Pendataan lokasi dan jenis APAR.
    *   Pencatatan pemeriksaan tekanan, segel, selang, dan nozzle.
    *   Cetak kartu riwayat pemeriksaan bulanan.
2.  **Perawatan Gedung**
    *   Monitoring kondisi fisik bangunan (atap, dinding, lantai, pintu, jendela).
    *   Pencatatan temuan kerusakan dan status perbaikan.
    *   Cetak laporan kondisi gedung.
3.  **Perawatan Hydrant**
    *   Pemeriksaan rutin instalasi hydrant (valve, nozzle, hose, box).
    *   Pencatatan tekanan air dan kondisi fisik komponen.
    *   Cetak riwayat pemeliharaan sistem proteksi kebakaran.
4.  **Perawatan Grease Trap (Penyaring Lemak)**
    *   Pencatatan pembersihan mingguan.
    *   Monitoring kondisi internal trap dan kelancaran saluran.
    *   Laporan rekapitulasi semesteran.

## 🛠️ Teknologi yang Digunakan

*   **Bahasa Pemrograman:** PHP (Native)
*   **Database:** MySQL / MariaDB
*   **Frontend Framework:** [Bootstrap 5](https://getbootstrap.com/)
*   **Icons:** [FontAwesome 6](https://fontawesome.com/)
*   **Desain UI:** Modern Blue-White Branding dengan Glassmorphism effect pada login.

## 📋 Prasyarat Instalasi

Sebelum menjalankan aplikasi ini, pastikan Anda telah menginstal:

*   Web Server (Apache/Nginx)
*   PHP versi 7.4 atau lebih tinggi
*   MySQL Server
*   Web Browser (Chrome/Firefox/Edge)

*(Disarankan menggunakan XAMPP untuk pengguna Windows)*

## ⚙️ Cara Instalasi

1.  **Clone atau Download Project:**
    Download source code ini dan letakkan di dalam folder server Anda (misal: `C:\xampp\htdocs\checklist`).

2.  **Persiapan Database:**
    *   Buka **phpMyAdmin**.
    *   Buat database baru dengan nama `apar`.
    *   Import file SQL yang berada di `config/saranaprasarana.sql`.

3.  **Konfigurasi Koneksi:**
    Buka file `config/koneksi.php` dan sesuaikan pengaturan database Anda:
    ```php
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "saranaprasarana";
    ```

4.  **Jalankan Aplikasi:**
    Akses aplikasi melalui browser di URL: `http://localhost/checklist`

## 📖 Dokumentasi Penggunaan

### 1. Dashboard Utama
Setelah login, Anda akan diarahkan ke dashboard utama. Pilih salah satu dari 4 modul (APAR, Gedung, Hydrant, Grease Trap) untuk mulai mengelola data.

### 2. Pengelolaan Data
Di setiap modul, Anda dapat:
*   **Melihat Daftar Aset:** Menampilkan semua aset yang terdaftar beserta lokasinya.
*   **Tambah/Ubah/Hapus Aset:** Tombol aksi tersedia untuk manajemen data master aset.
*   **Kartu Riwayat:** Klik pada nomor kode atau tombol detail untuk masuk ke halaman "Kartu Riwayat".

### 3. Pengisian Checklist Pemeriksaan
Halaman "Kartu Riwayat" adalah tempat utama untuk melakukan pencatatan:
*   Pilih **Tahun** pemeriksaan di pojok kanan atas.
*   Klik tombol **"Isi Perawatan"** atau klik pada minggu tertentu (W1-W5) untuk modul Grease Trap.
*   Isi status kondisi (Ok/Nok), tanggal pengecekan, paraf, dan catatan tambahan.
*   Data akan otomatis tersimpan dan memperbarui tampilan tabel riwayat.

### 4. Pencetakan Laporan (Kartu Riwayat)
Aplikasi ini dioptimalkan untuk pencetakan laporan fisik:
*   Klik tombol **"Cetak"** di halaman Kartu Riwayat.
*   Tampilan akan otomatis menyesuaikan ke format laporan resmi (Landscape).
*   Garis tabel dan warna status (Hijau untuk Ok, Merah untuk Nok) akan muncul dengan jelas pada hasil print.
*   Untuk Grease Trap, laporan akan terbagi menjadi 2 halaman (Semester 1 & Semester 2) jika dicetak.

## 📁 Struktur Folder

```text
checklist/
├── assets/             # Gambar, CSS tambahan, dan logo
├── config/             # Koneksi database dan file SQL
├── apar_*.php          # Modul pengelolaan APAR
├── gedung_*.php        # Modul pengelolaan Gedung
├── hydrant_*.php       # Modul pengelolaan Hydrant
├── grease_trap_*.php   # Modul pengelolaan Grease Trap
├── dashboard.php       # Menu utama aplikasi (2x2 Grid)
├── index.php           # Halaman login (Entry point)
├── login.php           # Logika autentikasi
├── tambah.php          # Form universal tambah data
├── ubah.php            # Form universal edit data
└── hapus.php           # Logika penghapusan data
```

## 🛠️ Pengembangan Lanjutan
Jika ingin menambahkan modul baru:
1.  Buat tabel baru di database `apar`.
2.  Duplikasi file `apar_home.php` dan `apar_kartu.php` sebagai template.
3.  Sesuaikan field input di `tambah.php` dan `ubah.php`.
4.  Daftarkan modul baru di `dashboard.php`.

---
**Team IT Pabrik - Sistem Sarana Prasarana**
