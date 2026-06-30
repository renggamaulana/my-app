# 🌭 Corndog Alle - POS & Cashier System

Corndog Alle adalah aplikasi **Point of Sale (POS)** dan **Cashier System** berbasis web yang dikembangkan menggunakan:

- **Frontend** : Svelte
- **Backend API** : Laravel 12
- **Database** : MySQL

Aplikasi ini dirancang untuk membantu proses pemesanan makanan, pengelolaan produk, transaksi pelanggan, hingga administrasi toko dalam satu sistem terintegrasi.

---

# Teknologi yang Digunakan

## Backend

- Laravel 12
- PHP 8.2+
- MySQL
- Laravel Eloquent ORM
- Laravel Sanctum (Authentication)

## Frontend

- Svelte
- JavaScript
- Vite

## Database

- MySQL (XAMPP)

---

# Konfigurasi Database

Project menggunakan database **MySQL** yang berjalan melalui **XAMPP** (atau MySQL lokal lainnya).

Konfigurasi default pada file `.env`:

| Konfigurasi | Nilai |
|-------------|--------|
| Database | MySQL |
| Host | 127.0.0.1 |
| Port | 3306 |
| Database | corndog_alle |
| Username | root |
| Password | *(kosong)* |

> **Catatan**
>
> Pastikan service **Apache** dan **MySQL** pada XAMPP sudah berjalan sebelum menjalankan aplikasi.

---

# Akun Login

Database seeder telah menyediakan beberapa akun untuk proses pengujian aplikasi.

| Role | Username | Password | Redirect |
|------|----------|----------|----------|
| Administrator | `admin` | `password123` | `/admin` |
| Customer | `user` | `password123` | `/` |
| Customer | `rena` | `password123` | `/` |

---

# Instalasi Project

## Prasyarat

Pastikan perangkat telah terinstall:

- PHP >= 8.2
- Composer
- Node.js
- npm
- XAMPP (Apache & MySQL)

---

# Backend (Laravel)

Masuk ke folder backend

```bash
cd Backend
```

Install dependency

```bash
composer install
```

Copy file environment

```bash
cp .env.example .env
```

Generate application key

```bash
php artisan key:generate
```

Jalankan migration beserta seeder

```bash
php artisan migrate:fresh --seed
```

Menjalankan server Laravel

```bash
php artisan serve
```

Backend API akan berjalan pada:

```
http://127.0.0.1:8000
```

---

# Frontend (Svelte)

Masuk ke folder frontend

```bash
cd Frontend
```

Install dependency

```bash
npm install
```

Menjalankan development server

```bash
npm run dev
```

Frontend akan berjalan pada:

```
http://localhost:5000
```

*(atau sesuai port yang ditampilkan pada terminal)*

---

# Struktur Aplikasi

```
Corndog Alle
│
├── Backend
│   ├── app
│   ├── routes
│   ├── database
│   └── ...
│
├── Frontend
│   ├── src
│   ├── public
│   └── ...
│
└── README.md
```

---

# Fitur Utama

## Customer

- Menampilkan daftar menu berdasarkan kategori
- Pencarian produk secara realtime
- Shopping cart
- Badge jumlah item pada cart
- Detail produk melalui modal
- Menentukan jumlah pembelian
- Menambahkan catatan pesanan
- Checkout pesanan
- Riwayat informasi pelanggan

---

## Checkout

- Form informasi pelanggan
- Auto-fill data pelanggan yang sudah login
- Ringkasan pesanan
- Perhitungan subtotal
- Biaya pengiriman
- Diskon promo

Kode promo yang tersedia:

```
HEMAT
```

Potongan harga:

```
Rp10.000
```

---

## Admin Dashboard

### Dashboard

- Total pesanan baru
- Total pendapatan
- Jumlah pengguna

### Pelanggan

- Daftar pelanggan
- Riwayat alamat checkout

### Data Master

#### Produk

- Tambah produk
- Edit produk
- Hapus produk

#### Kategori

- Tambah kategori
- Edit kategori
- Hapus kategori

### Transaksi

- Daftar pesanan
- Status transaksi
- Detail invoice
- Cetak invoice

### Pengaturan

- Informasi toko
- Rekening pembayaran
- Konfigurasi checkout

---

# Alur Penggunaan

1. Jalankan MySQL dan Apache melalui XAMPP.
2. Jalankan Backend Laravel.
3. Jalankan Frontend Svelte.
4. Login menggunakan akun yang tersedia.
5. Customer dapat melakukan pemesanan.
6. Admin dapat mengelola seluruh data melalui dashboard.

---

# Screenshot

Silakan tambahkan screenshot aplikasi pada bagian ini.

Contoh:

- Halaman Home
- Halaman Checkout
- Dashboard Admin
- Detail Invoice
- Manajemen Produk

---

# Lisensi

Project ini dibuat untuk keperluan pembelajaran dan pengembangan aplikasi Point of Sale (POS).