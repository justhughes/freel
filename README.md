# PROJECT CONTIFY — Frontend Terhubung API

Folder ini berisi dua bagian:

- `BACKEND` — Laravel 12 + Sanctum + SQLite
- `FRONTEND` — HTML, CSS, dan Vanilla JavaScript

Frontend sudah dipanggilkan ke endpoint API yang tersedia pada backend. Konfigurasi alamat API berada di `FRONTEND/contify-config.js` dan seluruh logika integrasi berada di `FRONTEND/contify-api.js`.

## Fitur yang Sudah Terhubung

### Autentikasi

- Login klien
- Login admin
- Login freelancer
- Pemulihan sesi dengan token Sanctum
- Logout

### Klien

- Mengambil daftar paket dari database
- Membuat pesanan beserta brief dan aset mentah
- Memilih channel pembayaran
- Membuat transaksi pembayaran
- Simulasi verifikasi pembayaran
- Melihat status pesanan
- Melihat seluruh versi hasil freelancer
- Mengajukan revisi
- Menyetujui hasil akhir

### Freelancer

- Melihat Job Board sesuai keahlian
- Mengambil pekerjaan
- Melihat My Tasks dan aset klien
- Mengunggah hasil awal
- Melihat revisi yang telah diteruskan admin
- Mengunggah hasil revisi

### Admin

- Melihat seluruh pesanan pada Kanban
- Melihat log pembayaran
- Mengelola paket melalui API
- Melihat permintaan revisi
- Meneruskan atau menolak revisi

## Fitur Frontend yang Belum Memiliki Endpoint API

Tampilan berikut tetap dipertahankan sebagai prototipe lokal karena endpoint-nya belum tersedia pada `routes/api.php`:

- pendaftaran freelancer publik;
- persetujuan kandidat freelancer;
- pengelolaan voucher;
- wallet dan withdraw freelancer;
- pengaturan kuota bidang freelancer.

Tidak ada endpoint baru untuk fitur tersebut yang ditambahkan pada pekerjaan integrasi ini.

## Instalasi Pertama

Buka CMD pada folder project lalu jalankan:

```bat
cd BACKEND
composer install
copy .env.example .env
if not exist database\database.sqlite type nul > database\database.sqlite
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan optimize:clear
```

Kembali ke folder utama:

```bat
cd ..
```

## Menjalankan Project

Backend dan frontend harus berjalan di dua terminal berbeda.

Terminal pertama:

```bat
cd BACKEND
php artisan serve --host=127.0.0.1 --port=8000
```

Terminal kedua:

```bat
php -S 127.0.0.1:5500 -t FRONTEND
```

Buka:

```text
http://127.0.0.1:5500/contify-v3.html
```

Jangan membuka file HTML langsung melalui `file:///`, karena frontend perlu dijalankan melalui web server agar koneksi API lebih stabil.

Project juga dapat dijalankan melalui:

```text
run-project.bat
```

## Akun Demo

| Role | Username | Password |
|---|---|---|
| Klien | `a` | `a` |
| Admin | `b` | `b` |
| Freelancer | `f` | `f` |

## Mengganti Alamat API

Buka:

```text
FRONTEND/contify-config.js
```

Kemudian ubah:

```js
apiBaseUrl: 'http://127.0.0.1:8000/api',
storageBaseUrl: 'http://127.0.0.1:8000/storage',
```

## Pemeriksaan

```bat
cd BACKEND
php artisan route:list --path=api
php artisan test
```

Pemeriksaan JavaScript:

```bat
node --check FRONTEND\contify-core.js
node --check FRONTEND\contify-config.js
node --check FRONTEND\contify-api.js
```

## Catatan Database

Perintah berikut menghapus semua data lama lalu membuat ulang data demo:

```bat
php artisan migrate:fresh --seed
```

Untuk menjalankan migration baru tanpa menghapus data:

```bat
php artisan migrate
```
