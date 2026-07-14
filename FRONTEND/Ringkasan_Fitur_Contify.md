# Ringkasan Fitur Contify Setelah Integrasi API

Contify memakai frontend HTML, CSS, dan Vanilla JavaScript yang terhubung ke backend Laravel melalui API Sanctum.

## Data Nyata dari Backend

Data berikut tidak lagi memakai mock utama:

- akun login;
- paket layanan;
- pesanan klien;
- pembayaran pesanan;
- Job Board freelancer;
- tugas freelancer;
- file hasil dan riwayat versi;
- revisi klien;
- pemantauan pesanan admin;
- pengelolaan paket admin.

## Alur Klien

1. Login sebagai klien.
2. Memilih paket dari database.
3. Mengisi brief dan mengunggah aset.
4. Memilih tanggal, kecepatan, dan channel pembayaran.
5. Pesanan serta transaksi pembayaran dibuat melalui API.
6. Setelah pembayaran terverifikasi, pesanan masuk Job Board.
7. Klien memantau status dan melihat seluruh versi hasil freelancer.
8. Klien dapat meminta revisi atau menerima hasil.

## Alur Freelancer

1. Login sebagai freelancer.
2. Job Board hanya menampilkan pekerjaan sesuai keahlian yang disetujui.
3. Freelancer mengambil pekerjaan.
4. Aset klien dapat dibuka dari penyimpanan backend.
5. Freelancer mengunggah hasil awal atau hasil revisi.

## Alur Admin

1. Login sebagai admin.
2. Melihat seluruh pesanan dalam Kanban.
3. Melihat transaksi pembayaran.
4. Mengelola paket.
5. Memeriksa, meneruskan, atau menolak pengajuan revisi.

## Bagian yang Masih Prototipe Lokal

- pendaftaran freelancer publik;
- approval kandidat freelancer;
- CMS voucher;
- wallet dan withdraw;
- kuota bidang freelancer.

Bagian tersebut belum dipanggilkan ke backend karena belum terdapat endpoint terkait pada API yang tersedia.
