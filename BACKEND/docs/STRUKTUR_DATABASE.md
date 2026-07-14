# Struktur Database Contify

## Prinsip Penentuan Tipe Data

| Kebutuhan | Tipe | Alasan |
|---|---|---|
| Primary key / foreign key | unsigned BIGINT | Konsisten dengan Laravel dan kapasitas besar |
| Uang rupiah | unsigned BIGINT | Rupiah tidak membutuhkan pecahan dan aman dari pembulatan float |
| Persentase | DECIMAL(5,2) | Menyimpan angka seperti 80,00 secara presisi |
| Kode tetap | CHAR | Panjang selalu sama |
| Nomor telepon/rekening | VARCHAR | Bisa diawali nol dan tidak dihitung secara matematis |
| Jumlah hari/revisi | unsigned TINYINT | Nilainya kecil dan tidak negatif |
| Slot/quantity | unsigned SMALLINT | Nilainya tidak negatif dan lebih besar dari tiny integer |
| Deskripsi | TEXT | Panjang fleksibel |
| Brief | LONGTEXT | Dapat berisi instruksi panjang |
| File path | VARCHAR(500) | Menyimpan lokasi file |
| Status | VARCHAR(15–30) | Fleksibel untuk pengembangan dan portable |

## Tabel Utama

### users

Menyimpan admin, klien, dan freelancer.

- `id` BIGINT PK
- `username` VARCHAR(50) UNIQUE
- `name` VARCHAR(120)
- `email` VARCHAR(160) UNIQUE
- `phone` VARCHAR(20) UNIQUE NULL
- `password` VARCHAR(255)
- `role` VARCHAR(20) INDEX
- `account_status` VARCHAR(20) INDEX

### service_categories

Bidang utama yang menjadi dasar paket dan keahlian freelancer.

- `id` BIGINT PK
- `code` CHAR(4) UNIQUE
- `name` VARCHAR(80) UNIQUE
- `freelancer_quota` UNSIGNED SMALLINT
- `is_active` BOOLEAN

### service_packages

- `id` BIGINT PK
- `service_category_id` BIGINT FK
- `code` CHAR(8) UNIQUE
- `base_price` UNSIGNED BIGINT
- `regular_days`, `fast_days`, `express_days` UNSIGNED TINYINT
- `fast_fee_percent`, `express_fee_percent` DECIMAL(5,2)
- `revision_limit` UNSIGNED TINYINT
- `total_slot` UNSIGNED SMALLINT
- `freelancer_fee_percent` DECIMAL(5,2)

### freelancer_profiles

Data khusus pelamar dan freelancer aktif.

### freelancer_skills

Pivot freelancer dan kategori. Status dapat berbeda per bidang sehingga admin bisa menerima satu bidang dan menolak bidang lain.

### production_slots

Menyimpan slot per paket dan tanggal supaya frontend dapat menampilkan apakah jadwal masih tersedia atau penuh.

### orders

Menyimpan snapshot harga, batas revisi, jadwal, freelancer, dan status proses.

Kode pesanan menggunakan `CHAR(17)` dengan format:

```text
CNT-YYYYMMDD-0001
```

### payment_methods dan payment_channels

Kategori pembayaran dipisahkan dari penyedianya:

- `bank_transfer` → BCA, BRI, Mandiri
- `qris` → QRIS
- `e_wallet` → GoPay, OVO

### payments

Satu order dapat memiliki beberapa percobaan pembayaran melalui `attempt_number`.

### order_assignments

Riwayat freelancer yang mengambil atau menerima pekerjaan.

### order_submissions dan submission_files

Menyimpan versi hasil kerja dan file yang dikirim freelancer.

### order_revisions

Menyimpan setiap permintaan revisi secara terpisah agar riwayatnya tidak hilang. Struktur revisi juga menyimpan:

- submission awal yang dikomentari klien;
- hasil submission baru yang menjawab revisi;
- admin yang meneruskan atau menolak;
- nomor pengajuan dan nomor revisi yang benar-benar memakai kuota;
- catatan klien dan catatan admin;
- waktu diajukan, diteruskan, selesai, atau ditolak;
- status `pending_admin`, `forwarded`, `in_progress`, `completed`, atau `rejected`.

`revision_number` memakai unsigned tiny integer sebagai urutan pengajuan. `approved_revision_number` juga unsigned tiny integer dan hanya terisi saat admin meneruskan revisi, sehingga pengajuan yang ditolak tidak mengurangi batas revisi paket.

### wallets

Memisahkan:

- saldo tersedia;
- saldo ditahan;
- saldo yang sudah ditarik.

### wallet_transactions

Setiap perubahan saldo dicatat agar dapat diaudit.

### withdrawals

Menyimpan permintaan tarik dana, rekening tujuan, status, reviewer, dan bukti transfer.

## Foreign Key Penting

- paket → kategori;
- skill → freelancer dan kategori;
- order → klien, paket, slot, freelancer, voucher;
- payment → order, metode, channel;
- submission → order dan freelancer;
- revision → order, submission awal, submission hasil revisi, requester, dan admin penerus;
- wallet → freelancer;
- withdrawal → wallet dan freelancer.

Semua foreign key dibuat dengan aturan hapus yang sesuai. Data transaksi penting umumnya memakai `restrictOnDelete()` atau `nullOnDelete()`, sedangkan data turunan seperti file pesanan memakai `cascadeOnDelete()`.
