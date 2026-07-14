# Daftar File Final Backend Contify

## Migration

- `0001_01_01_000000_create_users_table.php`
- `0001_01_01_000001_create_cache_table.php`
- `0001_01_01_000002_create_jobs_table.php`
- `2026_07_01_000100_create_service_catalog_tables.php`
- `2026_07_01_000200_create_freelancer_tables.php`
- `2026_07_01_000300_create_voucher_tables.php`
- `2026_07_01_000400_create_production_slots_table.php`
- `2026_07_01_000500_create_orders_and_assets_tables.php`
- `2026_07_01_000600_create_payment_tables.php`
- `2026_07_01_000700_create_workflow_tables.php`
- `2026_07_01_000800_create_wallets_table.php`
- `2026_07_01_000900_create_withdrawals_table.php`
- `2026_07_01_001000_create_wallet_transactions_table.php`

## Model

- User
- ServiceCategory
- ServicePackage
- FreelancerProfile
- FreelancerSkill
- Voucher dan VoucherUsage
- ProductionSlot
- Order dan OrderAsset
- PaymentMethod, PaymentChannel, dan Payment
- OrderAssignment
- OrderSubmission dan SubmissionFile
- OrderRevision
- Wallet, WalletTransaction, dan Withdrawal

## Service Bisnis

- `CodeGenerator`
- `OrderPricingService`
- `ProductionSlotService`
- `PaymentService`
- `FreelancerApprovalService`
- `OrderWorkflowService`
- `WalletService`

Service tersebut menyimpan logika backend tanpa membuat API baru.

## Controller Web

- `AuthController`
- `Client/OrderController`
- `Freelancer/FreelancerController`
- `Admin/DashboardController`
- `Admin/ClientController`
- `Admin/FreelancerController`
- `Admin/PackageController`
- `Admin/OrderController`
- `Admin/PaymentController`
- `Admin/WithdrawalController`
- `Admin/VoucherController`

## Route

Hanya memakai:

```text
routes/web.php
routes/console.php
```

Tidak ditambahkan:

```text
routes/api.php
app/Http/Controllers/Api/
API token authentication
```

## Seeder

- `DatabaseSeeder.php`
- `DemoDataSeeder.php`

Seeder membuat data kategori, paket, metode pembayaran, channel pembayaran, klien, freelancer, pelamar, slot, pesanan, submission, revisi, wallet, dan withdraw.
