# Contify Backend Final — Tanpa Penambahan API

Backend Laravel ini memakai tiga role: **klien/UMKM, freelancer, dan admin**. Project hanya menggunakan route web, session login, controller Laravel, model Eloquent, migration, seeder, service bisnis, dan Blade untuk pengujian backend.


## Alur revisi final

```text
Freelancer upload hasil
→ Klien melihat hasil
→ Klien mengajukan revisi
→ Admin memeriksa pengajuan
→ Admin meneruskan revisi
→ Freelancer melihat catatan revisi
→ Freelancer upload hasil revisi
→ Hasil revisi tampil di akun klien
→ Klien menerima hasil atau meminta revisi lagi
```

Jumlah revisi hanya bertambah ketika admin meneruskan revisi kepada freelancer. Batas maksimal mengikuti `revision_limit` yang disalin dari paket ke pesanan.

## Persyaratan

- PHP 8.2 atau lebih baru
- Composer
- Node.js dan NPM
- Ekstensi PHP: `pdo_sqlite`, `sqlite3`, `fileinfo`, `mbstring`, `openssl`, `dom`, dan `xml`

Cek versi:

```bat
php -v
composer -V
node -v
npm -v
```

## Instalasi pertama

Masuk ke folder project:

```bat
cd lokasi\folder\Contify-Backend-Final-Tanpa-API-v3
```

Install dependency:

```bat
composer install
npm install
```

Buat `.env` jika belum tersedia:

```bat
copy .env.example .env
```

Pastikan bagian berikut ada pada `.env`:

```env
APP_URL=http://127.0.0.1:8000
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id
APP_FALLBACK_LOCALE=id
DB_CONNECTION=sqlite
```

Buat database SQLite jika belum tersedia:

```bat
if not exist database\database.sqlite type nul > database\database.sqlite
```

Generate application key:

```bat
php artisan key:generate
```

Buat ulang database dan isi data demo:

```bat
php artisan migrate:fresh --seed
```

Buat storage link:

```bat
php artisan storage:link
```

Bersihkan cache dan build aset:

```bat
php artisan optimize:clear
npm run build
```

Jalankan project:

```bat
php artisan serve
```

Buka:

```text
http://127.0.0.1:8000
```

Project juga dapat disiapkan melalui:

```text
setup-contify.bat
```

Setelah setup, jalankan:

```text
run-contify.bat
```

## Menjalankan project pada hari berikutnya

Biasanya cukup:

```bat
php artisan serve
```

Setelah mengubah route, controller, Blade, atau konfigurasi:

```bat
php artisan optimize:clear
php artisan serve
```

## Update dari versi sebelumnya tanpa menghapus data

```bat
php artisan migrate
php artisan optimize:clear
php artisan serve
```

Data lama dipertahankan. Migration baru akan menyesuaikan riwayat revisi lama agar tetap dapat digunakan.

Untuk pengujian bersih, gunakan:

```bat
php artisan migrate:fresh --seed
php artisan storage:link
php artisan optimize:clear
php artisan serve
```

> `migrate:fresh --seed` menghapus seluruh data lama.

## Akun demo

| Role | Username | Password |
|---|---|---|
| Klien utama | `a` | `a` |
| Admin | `b` | `b` |
| Freelancer aktif | `f` | `f` |
| Klien tambahan | `rian` | `client123` |
| Freelancer tambahan | `copywriter` | `freelancer123` |
| Pelamar freelancer | `candidate` | `candidate123` |

## Cara menguji alur revisi

1. Login sebagai klien `a/a`.
2. Buka pesanan dengan status **Review Hasil**.
3. Download hasil terbaru dan kirim pengajuan revisi.
4. Login sebagai admin `b/b`.
5. Buka menu **Revisi** lalu teruskan pengajuan kepada freelancer.
6. Login sebagai freelancer `f/f` atau freelancer yang tercantum pada pesanan.
7. Buka **My Tasks**, baca catatan revisi, lalu upload hasil baru.
8. Login kembali sebagai klien.
9. Hasil versi terbaru akan tampil pada **Hasil Terbaru dari Freelancer** dan **Riwayat Semua Hasil**.
10. Selama kuota masih tersedia, klien dapat memberikan catatan revisi lagi.

## Pemeriksaan project

```bat
php artisan route:list
php artisan migrate:status
php artisan test
```

## API

Project ini tidak menambah:

```text
routes/api.php
app/Http/Controllers/Api
API token
Laravel Sanctum
endpoint JSON baru
```

## Catatan tipe data

- Primary key dan foreign key memakai unsigned BIGINT melalui `$table->id()` dan `$table->foreignId()`.
- Nominal rupiah memakai `unsignedBigInteger`, bukan float.
- Persentase memakai `decimal(5,2)`.
- Batas revisi dan nomor versi memakai `unsignedTinyInteger` karena nilainya kecil dan tidak negatif.
- Jumlah slot dan jumlah pesanan memakai `unsignedSmallInteger`.
- Kode dengan panjang tetap memakai `char`.
- Nomor telepon dan rekening memakai `string` karena dapat diawali angka nol.
- Brief dan catatan panjang memakai `text` atau `longText`.
- Path file memakai `string(500)`.
