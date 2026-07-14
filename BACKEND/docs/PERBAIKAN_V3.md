# Perbaikan dan hasil cross-check versi 3

## Masalah waktu

Penyebab yang ditemukan:

1. `config/app.php` masih memakai zona waktu UTC.
2. Perhitungan deadline memakai `endOfDay()`, sehingga jam deadline selalu sekitar 23:59.

Perbaikan:

- Menambahkan `APP_TIMEZONE=Asia/Jakarta`.
- Menggunakan konfigurasi timezone dari `.env`.
- Deadline mempertahankan jam saat pesanan dibuat dan menambah durasi paket.
- Tampilan waktu diberi penanda WIB.

## Masalah hasil revisi tidak terlihat klien

Penyebab dan celah yang ditemukan:

1. Halaman klien hanya mengandalkan satu relasi `currentSubmission`.
2. Relasi hasil terbaru hanya bergantung pada flag `is_current`, sehingga data lama yang tidak konsisten dapat membuat hasil yang salah terbaca.
3. Riwayat semua versi hasil tidak ditampilkan.
4. Pengajuan revisi langsung terlihat freelancer tanpa tahap admin.
5. Revisi yang dikirim freelancer belum dihubungkan secara eksplisit dengan pengajuan revisinya.
6. Semua revisi aktif sebelumnya ditutup secara massal ketika freelancer upload.

Perbaikan:

- Hasil terbaru ditentukan berdasarkan nomor versi tertinggi.
- Semua submission dimuat dan ditampilkan kepada klien.
- Menambahkan `result_submission_id` pada revisi.
- Hanya revisi aktif terkait yang diselesaikan saat freelancer upload.
- Menambahkan tahap `pending_admin`, `forwarded`, `in_progress`, `completed`, dan `rejected`.
- Menambahkan halaman admin untuk meneruskan atau menolak revisi.
- Freelancer tidak dapat melihat catatan sebelum diteruskan admin.
- Klien dapat mengajukan revisi kembali setelah hasil versi baru dikirim, selama kuota masih tersedia.

## File utama yang ditambah

```text
app/Http/Controllers/Admin/OrderRevisionController.php
database/migrations/2026_07_03_000100_enhance_revision_workflow.php
resources/views/admin/revisions/index.blade.php
docs/PERBAIKAN_V3.md
```

## File utama yang diperbarui

```text
.env
.env.example
config/app.php
app/Providers/AppServiceProvider.php
app/Models/Order.php
app/Models/OrderRevision.php
app/Services/OrderPricingService.php
app/Services/OrderWorkflowService.php
app/Services/PaymentService.php
app/Http/Controllers/Client/OrderController.php
app/Http/Controllers/Freelancer/FreelancerController.php
app/Http/Controllers/Admin/DashboardController.php
app/Http/Controllers/Admin/OrderController.php
routes/web.php
resources/views/layouts/app.blade.php
resources/views/client/orders/index.blade.php
resources/views/client/orders/show.blade.php
resources/views/freelancer/tasks/index.blade.php
resources/views/freelancer/tasks/show.blade.php
resources/views/admin/orders/index.blade.php
resources/views/admin/orders/show.blade.php
database/seeders/DemoDataSeeder.php
tests/Feature/CoreWorkflowTest.php
```
