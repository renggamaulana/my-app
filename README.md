# Panduan Login

Aplikasi menyediakan dua jenis akun untuk kebutuhan pengujian, yaitu **Administrator** dan **Customer**.

## Login Administrator

1. Jalankan aplikasi.
2. Buka halaman login administrator melalui URL:

```
http://localhost:5000/admin
```

3. Login menggunakan akun berikut:

| Username | Password |
|----------|----------|
| `admin` | `admin123` |

Setelah berhasil login, Anda akan diarahkan ke **Dashboard Admin** untuk mengelola seluruh data aplikasi.

---

## Login Customer

Customer dapat melakukan login melalui halaman login pada website.

Gunakan akun berikut:

| Username | Password |
|----------|----------|
| `user` | `password123` |

Setelah berhasil login, pengguna akan diarahkan ke halaman utama (Storefront) dan dapat melakukan pemesanan produk.

> **Catatan**
>
> Database seeder juga membuat akun customer tambahan dengan email:
>
> `user@corndogalle.com`
>
> dan nama pengguna (**username**) `user`.
>
> Proses login menggunakan **username**, bukan email.