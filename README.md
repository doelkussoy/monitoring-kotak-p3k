# 🩹 P3K Digital Monitoring System - PT CBA Chemical Industry

[![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/doelkussoy/monitoring-kotak-p3k)
[![PHP](https://img.shields.io/badge/PHP-8.x-777bb4.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-DB-4479a1.svg)](https://www.mysql.com/)
[![Aesthetic](https://img.shields.io/badge/UI/UX-Glassmorphism-brightgreen.svg)]()

**P3K Digital Monitoring System** adalah solusi manajemen inventaris cerdas yang dirancang khusus untuk PT CBA Chemical Industry. Sistem ini mentransformasi proses pemantauan kotak P3K konvensional menjadi platform digital yang efisien, transparan, dan akurat.

---

## 🚀 Fitur Unggulan

### 📊 Dashboard Monitoring Premium
*   **Real-time Stats**: Pantau total lokasi, item expired, stok rendah, dan *Capacity Health* secara instan.
*   **Visual Urgency**: Sistem warna cerdas (Danger/Warning/Success) yang memudahkan identifikasi masalah dalam sekejap.
*   **Activity Timeline**: Log otomatis setiap kali ada perubahan stok atau update data.

### 📝 Manajemen Inventaris Spreadsheet-Style
*   **Inline Editing**: Edit stok, batas minimum, dan tanggal kadaluarsa langsung di tabel tanpa berpindah halaman.
*   **Bulk Management**: Sistem otomatis untuk pengisian item standar (20+ item wajib) ke lokasi baru.
*   **Satuan Custom**: Mendukung berbagai jenis satuan (pcs, botol, box, dll).

### 🔔 Sistem Alert Multi-Tier
*   **🔴 LEWAT EXPIRED**: Notifikasi khusus untuk item yang sudah melewati tanggal kadaluarsa (Paling Kritis).
*   **🟡 MENDEKATI EXPIRED**: Peringatan otomatis untuk item yang akan kadaluarsa dalam 30 hari ke depan.
*   **🔴 STOK RENDAH**: Alert otomatis jika stok item berada di bawah ambang batas minimum.

### 👥 User Management & Security
*   **Role-Based Access**: Perbedaan akses antara **Admin** (Kontrol Penuh) dan **PIC** (Hanya area tanggung jawab).
*   **Secure Actions**: Semua penghapusan data diamankan dengan konfirmasi **SweetAlert2** dan metode **POST** untuk mencegah data loss.

---

## 🛠️ Teknologi yang Digunakan

| Komponen | Teknologi |
| :--- | :--- |
| **Core Engine** | PHP 8.x (Native) |
| **Database** | MySQL / MariaDB |
| **UI Framework** | Bootstrap 5.3.2 |
| **Icons** | Lucide Icons |
| **Animations** | CSS3 Transitions & Keyframes |
| **Notifications** | SweetAlert2 |
| **Design Style** | Modern Glassmorphism |

---

## 📂 Struktur Direktori

```text
monitoring-kotak-p3k/
├── assets/
│   ├── css/          # Custom Glassmorphism Styles
│   └── images/       # Branding & Logo Assets
├── db.php            # Konfigurasi Koneksi Database
├── index.php         # Halaman Login Utama
├── dashboard.php     # Panel Kendali Utama
├── location_detail.php # Manajemen Inventaris Per Lokasi
├── laporan.php       # Rekapitulasi Kondisi Bulanan
└── pengaturan.php    # Manajemen User & Profil
```

---

## ⚙️ Instalasi & Konfigurasi

### 1. Persiapan Database
Buat database baru dengan nama `kotakp3k` dan jalankan query berikut:

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE,
    pass VARCHAR(255),
    nama VARCHAR(100),
    role ENUM('Admin', 'PIC')
);

CREATE TABLE p3k_lokasi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_lokasi VARCHAR(100),
    pic VARCHAR(50),
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE p3k_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lokasi_id INT,
    nama_item VARCHAR(100),
    stok INT DEFAULT 0,
    min_stok INT DEFAULT 0,
    satuan VARCHAR(20),
    tgl_kadaluarsa DATE
);

CREATE TABLE p3k_activity (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lokasi_id INT,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 2. Konfigurasi Koneksi
Buka file `db.php` dan sesuaikan kredensial database Anda:

```php
$host = "localhost";
$user = "root";
$pass = "";
$db = "kotakp3k";
```

---

## 📝 Akun Default
Untuk login pertama kali, gunakan akun berikut:
- **Username**: `admin`
- **Password**: `admin123`

---

## 👨‍💻 Kontribusi
Aplikasi ini dikembangkan dan dikelola oleh:
**Team IT Pabrik - PT CBA Chemical Industry**
*Digital Transformation for Better Safety.*
