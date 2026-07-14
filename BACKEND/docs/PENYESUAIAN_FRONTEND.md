# Penyesuaian Backend terhadap Frontend Terbaru

Dokumen ini hanya memetakan data dan alur. Dokumen ini **tidak mendefinisikan API**.

## Role

- Klien: `users.role = client`
- Freelancer: `users.role = freelancer`
- Admin: `users.role = admin`

## Paket

Data kartu paket frontend berasal dari:

- `service_categories`
- `service_packages`
- `production_slots`

## Pembayaran

Tiga kategori frontend disimpan pada `payment_methods`:

- Transfer Bank
- QRIS
- E-Wallet

Pilihan detail disimpan pada `payment_channels`:

- BCA
- BRI
- Mandiri
- QRIS
- GoPay
- OVO

## Job Board

Job Board menggunakan order dengan kondisi:

```text
status = queue
freelancer_id = null
```

Pekerjaan hanya boleh terlihat bagi freelancer yang mempunyai `freelancer_skills.status = approved` pada kategori paket tersebut.

## My Tasks

Setelah pekerjaan diambil:

```text
freelancer_id = user freelancer
status = process
```

## Review dan Revisi

- Upload hasil → `order_submissions` + `submission_files`
- Status menjadi `review`
- Revisi → `order_revisions`, status order menjadi `revision`
- Terima hasil → status order menjadi `done`

## Saldo dan Withdraw

- Fee freelancer masuk ke `wallets.available_balance`
- Riwayat masuk ke `wallet_transactions`
- Permintaan tarik dana masuk ke `withdrawals`

Integrasi frontend melalui API sengaja belum dibuat dalam project ini.
