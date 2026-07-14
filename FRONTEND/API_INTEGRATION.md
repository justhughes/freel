# Integrasi API Frontend Contify

## Struktur File

- `contify-v3.html` — struktur halaman
- `contify-v3.css` — tampilan utama
- `contify-core.js` — fungsi UI dan data prototipe lama
- `contify-config.js` — konfigurasi base URL dan channel pembayaran
- `contify-api.js` — integrasi API dan penimpaan fungsi mock yang sudah memiliki endpoint backend

`contify-api.js` sengaja diletakkan setelah `contify-core.js`. Dengan pola tersebut, desain dan interaksi awal tetap dipertahankan, sedangkan fungsi yang sudah memiliki API diganti menggunakan data backend.

## Endpoint yang Dipakai

| Bagian | Method | Endpoint |
|---|---|---|
| Login | POST | `/api/auth/login` |
| Sesi user | GET | `/api/auth/me` |
| Logout | POST | `/api/auth/logout` |
| Paket | GET | `/api/packages` |
| Tambah paket | POST | `/api/packages` |
| Edit paket | PUT | `/api/packages/{id}` |
| Hapus/nonaktifkan paket | DELETE | `/api/packages/{id}` |
| Daftar pesanan | GET | `/api/orders` |
| Buat pesanan | POST | `/api/orders` |
| Verifikasi pembayaran simulasi | POST | `/api/payments/{id}/verify` |
| Job Board | GET | `/api/freelancer/jobs` |
| Ambil pekerjaan | POST | `/api/freelancer/jobs/{id}/take` |
| My Tasks | GET | `/api/freelancer/tasks` |
| Upload hasil | POST | `/api/freelancer/tasks/{id}/submit` |
| Ajukan revisi | POST | `/api/client/orders/{id}/revision` |
| Terima hasil | POST | `/api/client/orders/{id}/approve` |
| Daftar revisi admin | GET | `/api/admin/revisions` |
| Teruskan revisi | POST | `/api/admin/revisions/{id}/forward` |
| Tolak revisi | POST | `/api/admin/revisions/{id}/reject` |

## Token

Token Sanctum disimpan pada browser dengan key:

```text
contify_api_token
```

Data user tersimpan dengan key:

```text
contify_api_user
```

Setiap request terautentikasi dikirim dengan header:

```http
Authorization: Bearer <token>
Accept: application/json
```

## Channel Pembayaran

ID channel mengikuti data awal pada `DemoDataSeeder`:

| ID | Channel |
|---:|---|
| 1 | BCA Virtual Account |
| 2 | BRI Virtual Account |
| 3 | Mandiri Virtual Account |
| 4 | QRIS |
| 5 | GoPay |
| 6 | OVO |

Konfigurasinya dapat diubah di `contify-config.js` apabila data channel pada database diubah.
