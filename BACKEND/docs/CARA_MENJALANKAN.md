# Cara Menjalankan Contify Backend

## Setup pertama

```bat
composer install
npm install
copy .env.example .env
if not exist database\database.sqlite type nul > database\database.sqlite
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan optimize:clear
npm run build
php artisan serve
```

Pastikan `.env` memuat:

```env
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id
APP_FALLBACK_LOCALE=id
DB_CONNECTION=sqlite
```

Buka aplikasi:

```text
http://127.0.0.1:8000
```

## Menjalankan kembali

```bat
php artisan serve
```

## Setelah perubahan kode

```bat
php artisan optimize:clear
php artisan serve
```

## Menjalankan migration baru tanpa menghapus data

```bat
php artisan migrate
php artisan optimize:clear
php artisan serve
```

## Reset database pengembangan

```bat
php artisan migrate:fresh --seed
php artisan storage:link
php artisan optimize:clear
php artisan serve
```

`migrate:fresh --seed` menghapus seluruh data lama.
