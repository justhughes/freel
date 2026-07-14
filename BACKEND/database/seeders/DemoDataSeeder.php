<?php

namespace Database\Seeders;

use App\Models\FreelancerProfile;
use App\Models\FreelancerSkill;
use App\Models\Order;
use App\Models\OrderAssignment;
use App\Models\OrderRevision;
use App\Models\OrderSubmission;
use App\Models\Payment;
use App\Models\PaymentChannel;
use App\Models\PaymentMethod;
use App\Models\ProductionSlot;
use App\Models\ServiceCategory;
use App\Models\ServicePackage;
use App\Models\SubmissionFile;
use App\Models\User;
use App\Models\Voucher;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    private string $date;

    public function run(): void
    {
        $this->date = now()->format('Ymd');
        Storage::disk('public')->deleteDirectory('demo');

        $users = $this->users();
        $categories = $this->categories();
        $packages = $this->packages($categories);
        $this->freelancers($users, $categories);
        $channels = $this->payments();
        $vouchers = $this->vouchers();
        $slots = $this->slots($packages);
        $this->orders($users, $packages, $channels, $vouchers, $slots);

        $this->command?->info('Data demo Contify tanpa penambahan API berhasil dibuat.');
        $this->command?->table(
            ['Role', 'Username', 'Password'],
            [
                ['Klien', 'a', 'a'],
                ['Admin', 'b', 'b'],
                ['Freelancer aktif', 'f', 'f'],
                ['Pelamar freelancer', 'candidate', 'candidate123'],
            ]
        );
    }

    private function users(): array
    {
        return [
            'admin' => User::query()->create([
                'username' => 'b',
                'name' => 'Admin Contify',
                'email' => 'admin@contify.test',
                'phone' => '081200000001',
                'password' => 'b',
                'role' => User::ROLE_ADMIN,
                'account_status' => User::STATUS_ACTIVE,
            ]),
            'client' => User::query()->create([
                'username' => 'a',
                'name' => 'UMKM Kopi Nusantara',
                'email' => 'client@contify.test',
                'phone' => '081200000002',
                'password' => 'a',
                'role' => User::ROLE_CLIENT,
                'account_status' => User::STATUS_ACTIVE,
            ]),
            'client2' => User::query()->create([
                'username' => 'rian',
                'name' => 'Rian Hidayat',
                'email' => 'rian@contify.test',
                'phone' => '081200000003',
                'password' => 'client123',
                'role' => User::ROLE_CLIENT,
                'account_status' => User::STATUS_ACTIVE,
            ]),
            'freelancer' => User::query()->create([
                'username' => 'f',
                'name' => 'Fara Creative',
                'email' => 'freelancer@contify.test',
                'phone' => '081200000004',
                'password' => 'f',
                'role' => User::ROLE_FREELANCER,
                'account_status' => User::STATUS_ACTIVE,
            ]),
            'copywriter' => User::query()->create([
                'username' => 'copywriter',
                'name' => 'Dewi Copy',
                'email' => 'dewi@contify.test',
                'phone' => '081200000005',
                'password' => 'freelancer123',
                'role' => User::ROLE_FREELANCER,
                'account_status' => User::STATUS_ACTIVE,
            ]),
            'candidate' => User::query()->create([
                'username' => 'candidate',
                'name' => 'Budi Santoso',
                'email' => 'candidate@contify.test',
                'phone' => '081200000006',
                'password' => 'candidate123',
                'role' => User::ROLE_FREELANCER,
                'account_status' => User::STATUS_PENDING,
            ]),
        ];
    }

    private function categories(): array
    {
        $data = [
            'foto' => ['FOTO', 'Edit Foto', 'Pengolahan foto produk dan materi visual.', 5],
            'video' => ['VIDE', 'Video TikTok / Reels', 'Editing video vertikal untuk promosi.', 4],
            'copy' => ['COPY', 'Copy Writing', 'Penulisan caption, iklan, dan materi promosi.', 5],
            'strategi' => ['STRA', 'Strategi Konten', 'Perencanaan konten dan kalender publikasi.', 3],
        ];

        $categories = [];
        foreach ($data as $key => [$code, $name, $description, $quota]) {
            $categories[$key] = ServiceCategory::query()->create([
                'code' => $code,
                'name' => $name,
                'description' => $description,
                'freelancer_quota' => $quota,
                'is_active' => true,
            ]);
        }

        return $categories;
    }

    private function packages(array $categories): array
    {
        $rows = [
            'foto' => [
                'FOTO0001', 'Edit Foto Produk',
                'Foto produk dibuat lebih bersih, cerah, dan siap diunggah.',
                ['Maksimal 5 foto', 'Koreksi warna', 'Retouching ringan', 'File JPG/PNG'],
                45000, 3, 2, 1, 30, 60, 1, 10, 80,
            ],
            'video' => [
                'VIDE0001', 'Video TikTok / Reels',
                'Video vertikal siap posting dengan musik dan teks promosi.',
                ['Video maksimal 60 detik', 'Cut dan transisi', 'Teks promosi', 'Format 9:16'],
                150000, 3, 2, 1, 30, 60, 2, 5, 80,
            ],
            'copy' => [
                'COPY0001', 'Copy Writing Promosi',
                'Caption dan teks iklan yang disesuaikan dengan target audiens.',
                ['Maksimal 5 caption', 'Headline', 'Call to action', 'Hashtag'],
                35000, 2, 1, 1, 30, 60, 1, 10, 75,
            ],
            'strategi' => [
                'STRA0001', 'Strategi Konten Bulanan',
                'Content plan satu bulan dengan jadwal dan konsep yang terstruktur.',
                ['Analisis akun', 'Content pillar', '12 ide konten', 'Jadwal publikasi'],
                350000, 5, 3, 2, 30, 60, 2, 5, 70,
            ],
        ];

        $packages = [];
        foreach ($rows as $key => $row) {
            [$code, $name, $description, $includes, $price, $regular, $fast, $express,
                $fastFee, $expressFee, $revision, $slot, $freelancerFee] = $row;

            $packages[$key] = ServicePackage::query()->create([
                'service_category_id' => $categories[$key]->id,
                'code' => $code,
                'name' => $name,
                'slug' => Str::slug($name).'-'.strtolower($code),
                'description' => $description,
                'includes' => $includes,
                'base_price' => $price,
                'regular_days' => $regular,
                'fast_days' => $fast,
                'express_days' => $express,
                'fast_fee_percent' => $fastFee,
                'express_fee_percent' => $expressFee,
                'revision_limit' => $revision,
                'total_slot' => $slot,
                'freelancer_fee_percent' => $freelancerFee,
                'is_active' => true,
            ]);
        }

        return $packages;
    }

    private function freelancers(array $users, array $categories): void
    {
        FreelancerProfile::query()->create([
            'user_id' => $users['freelancer']->id,
            'bio' => 'Editor foto dan video untuk kebutuhan promosi UMKM.',
            'experience_years' => 3,
            'portfolio_url' => 'https://example.com/fara-portfolio',
            'application_status' => FreelancerProfile::STATUS_APPROVED,
            'reviewed_by' => $users['admin']->id,
            'reviewed_at' => now()->subMonth(),
            'payout_type' => 'bank',
            'payout_provider' => 'BCA',
            'payout_account_number' => '0123456789',
            'payout_account_holder' => 'Fara Creative',
        ]);

        foreach (['foto', 'video'] as $key) {
            FreelancerSkill::query()->create([
                'freelancer_id' => $users['freelancer']->id,
                'service_category_id' => $categories[$key]->id,
                'status' => FreelancerSkill::STATUS_APPROVED,
                'approved_by' => $users['admin']->id,
                'approved_at' => now()->subMonth(),
            ]);
        }

        FreelancerProfile::query()->create([
            'user_id' => $users['copywriter']->id,
            'bio' => 'Copywriter dan content strategist.',
            'experience_years' => 2,
            'portfolio_url' => 'https://example.com/dewi-portfolio',
            'application_status' => FreelancerProfile::STATUS_APPROVED,
            'reviewed_by' => $users['admin']->id,
            'reviewed_at' => now()->subWeeks(3),
            'payout_type' => 'bank',
            'payout_provider' => 'BRI',
            'payout_account_number' => '9876543210',
            'payout_account_holder' => 'Dewi Copy',
        ]);

        foreach (['copy', 'strategi'] as $key) {
            FreelancerSkill::query()->create([
                'freelancer_id' => $users['copywriter']->id,
                'service_category_id' => $categories[$key]->id,
                'status' => FreelancerSkill::STATUS_APPROVED,
                'approved_by' => $users['admin']->id,
                'approved_at' => now()->subWeeks(3),
            ]);
        }

        FreelancerProfile::query()->create([
            'user_id' => $users['candidate']->id,
            'bio' => 'Pelamar video editor dan copywriter.',
            'experience_years' => 2,
            'portfolio_url' => 'https://example.com/budi-portfolio',
            'application_status' => FreelancerProfile::STATUS_PENDING,
        ]);

        foreach (['video', 'copy'] as $key) {
            FreelancerSkill::query()->create([
                'freelancer_id' => $users['candidate']->id,
                'service_category_id' => $categories[$key]->id,
                'status' => FreelancerSkill::STATUS_PENDING,
            ]);
        }

        Wallet::query()->create(['freelancer_id' => $users['freelancer']->id]);
        Wallet::query()->create(['freelancer_id' => $users['copywriter']->id]);
    }

    private function payments(): array
    {
        $bank = PaymentMethod::query()->create(['code' => 'bank_transfer', 'name' => 'Transfer Bank']);
        $qris = PaymentMethod::query()->create(['code' => 'qris', 'name' => 'QRIS']);
        $wallet = PaymentMethod::query()->create(['code' => 'e_wallet', 'name' => 'E-Wallet']);

        return [
            'bca' => PaymentChannel::query()->create([
                'payment_method_id' => $bank->id, 'code' => 'BCA', 'name' => 'BCA Virtual Account',
                'account_name' => 'PT Contify Digital', 'account_number' => '880812345678',
                'instructions' => 'Transfer sesuai nominal transaksi.',
            ]),
            'bri' => PaymentChannel::query()->create([
                'payment_method_id' => $bank->id, 'code' => 'BRI', 'name' => 'BRI Virtual Account',
                'account_name' => 'PT Contify Digital', 'account_number' => '990812345678',
            ]),
            'mandiri' => PaymentChannel::query()->create([
                'payment_method_id' => $bank->id, 'code' => 'MANDIRI', 'name' => 'Mandiri Virtual Account',
                'account_name' => 'PT Contify Digital', 'account_number' => '770812345678',
            ]),
            'qris' => PaymentChannel::query()->create([
                'payment_method_id' => $qris->id, 'code' => 'QRIS', 'name' => 'QRIS',
                'instructions' => 'Pindai kode QR melalui aplikasi pembayaran.',
            ]),
            'gopay' => PaymentChannel::query()->create([
                'payment_method_id' => $wallet->id, 'code' => 'GOPAY', 'name' => 'GoPay',
            ]),
            'ovo' => PaymentChannel::query()->create([
                'payment_method_id' => $wallet->id, 'code' => 'OVO', 'name' => 'OVO',
            ]),
        ];
    }

    private function vouchers(): array
    {
        return [
            'welcome' => Voucher::query()->create([
                'code' => 'WELCOME10',
                'description' => 'Diskon 10% untuk klien baru.',
                'discount_type' => Voucher::TYPE_PERCENT,
                'discount_percent' => 10,
                'minimum_order_amount' => 50000,
                'maximum_discount_amount' => 50000,
                'usage_limit' => 100,
                'used_count' => 0,
                'is_active' => true,
            ]),
            'umkm' => Voucher::query()->create([
                'code' => 'UMKM50000',
                'description' => 'Potongan Rp50.000.',
                'discount_type' => Voucher::TYPE_FIXED,
                'discount_amount' => 50000,
                'minimum_order_amount' => 300000,
                'usage_limit' => 50,
                'used_count' => 0,
                'is_active' => true,
            ]),
        ];
    }

    private function slots(array $packages): array
    {
        $slots = [];
        foreach ($packages as $key => $package) {
            for ($day = 0; $day < 14; $day++) {
                $slot = ProductionSlot::query()->create([
                    'service_package_id' => $package->id,
                    'production_date' => now()->addDays($day)->toDateString(),
                    'total_slots' => $package->total_slot,
                    'reserved_slots' => 0,
                    'status' => ProductionSlot::STATUS_OPEN,
                ]);
                if ($day === 1) {
                    $slots[$key] = $slot;
                }
            }
        }

        return $slots;
    }

    private function orders(array $users, array $packages, array $channels, array $vouchers, array $slots): void
    {
        $definitions = [
            ['foto', 'pending_payment', null, 'Foto Produk Kopi Baru', 3, 'regular', 'bca', false],
            ['video', 'queue', null, 'Video Reels Promo Kopi', 1, 'fast', 'qris', true],
            ['foto', 'process', 'freelancer', 'Retouch Foto Katalog', 5, 'fast', 'gopay', true],
            ['video', 'review', 'freelancer', 'Video TikTok Launching Menu', 1, 'express', 'qris', true],
            ['copy', 'done', 'copywriter', 'Caption Promo Bulanan', 5, 'regular', 'ovo', true],
            ['strategi', 'revision', 'copywriter', 'Strategi Konten Kedai', 1, 'regular', 'mandiri', true],
        ];

        foreach ($definitions as $index => [$packageKey, $status, $freelancerKey, $title, $quantity, $speed, $channelKey, $paid]) {
            $package = $packages[$packageKey];
            $base = $package->base_price * $quantity;
            $speedPercent = match ($speed) {
                Order::SPEED_FAST => (float) $package->fast_fee_percent,
                Order::SPEED_EXPRESS => (float) $package->express_fee_percent,
                default => 0,
            };
            $speedFee = (int) round($base * $speedPercent / 100);
            $subtotal = $base + $speedFee;
            $discount = 0;
            $total = $subtotal - $discount;
            $earning = (int) round($total * ((float) $package->freelancer_fee_percent / 100));
            $code = sprintf('CNT-%s-%04d', $this->date, $index + 1);

            $order = Order::query()->create([
                'order_code' => $code,
                'client_id' => in_array($status, [Order::STATUS_REVIEW, Order::STATUS_REVISION], true)
                    ? $users['client']->id
                    : ($index % 2 === 0 ? $users['client']->id : $users['client2']->id),
                'service_package_id' => $package->id,
                'production_slot_id' => $slots[$packageKey]->id,
                'freelancer_id' => $freelancerKey ? $users[$freelancerKey]->id : null,
                'title' => $title,
                'business_name' => $index % 2 === 0 ? 'Kopi Nusantara' : 'Rian Creative Shop',
                'product_description' => 'Produk UMKM yang akan dipromosikan melalui konten digital.',
                'target_audience' => 'Pengguna media sosial usia 18 sampai 35 tahun.',
                'visual_reference' => 'Warm tones, modern, dan minimalis.',
                'brief' => 'Buat konten yang bersih, menarik, dan sesuai identitas brand UMKM.',
                'platform' => $packageKey === 'video' ? 'TikTok' : 'Instagram',
                'content_size' => $packageKey === 'video' ? '9:16' : '1:1',
                'quantity' => $quantity,
                'speed_type' => $speed,
                'booking_date' => now()->addDay()->toDateString(),
                'start_date' => now()->addDay()->toDateString(),
                'deadline_at' => now()->addDays($package->daysForSpeed($speed)),
                'base_price' => $base,
                'speed_fee' => $speedFee,
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'total_amount' => $total,
                'freelancer_earning' => $earning,
                'platform_margin' => $total - $earning,
                'revision_limit' => $package->revision_limit,
                'revision_used' => $status === Order::STATUS_REVISION ? 1 : 0,
                'status' => $status,
                'paid_at' => $paid ? now()->subDays(2) : null,
                'taken_at' => $freelancerKey ? now()->subDay() : null,
                'submitted_at' => in_array($status, [Order::STATUS_REVIEW, Order::STATUS_DONE, Order::STATUS_REVISION], true) ? now()->subHours(4) : null,
                'completed_at' => $status === Order::STATUS_DONE ? now()->subHour() : null,
            ]);
            $slots[$packageKey]->increment('reserved_slots');

            $payment = Payment::query()->create([
                'payment_code' => sprintf('PAY-%s-%04d', $this->date, $index + 1),
                'order_id' => $order->id,
                'payment_method_id' => $channels[$channelKey]->payment_method_id,
                'payment_channel_id' => $channels[$channelKey]->id,
                'attempt_number' => 1,
                'gateway_reference' => $paid ? 'DEMO-'.str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT) : null,
                'amount' => $total,
                'status' => $paid ? Payment::STATUS_PAID : Payment::STATUS_PENDING,
                'expires_at' => now()->addDay(),
                'paid_at' => $paid ? now()->subDays(2) : null,
                'metadata' => ['seeded' => true],
            ]);

            if ($freelancerKey) {
                OrderAssignment::query()->create([
                    'order_id' => $order->id,
                    'freelancer_id' => $users[$freelancerKey]->id,
                    'assigned_by' => $users[$freelancerKey]->id,
                    'action' => 'taken',
                    'assigned_at' => now()->subDay(),
                ]);
            }

            if (in_array($status, [Order::STATUS_REVIEW, Order::STATUS_DONE, Order::STATUS_REVISION], true)) {
                $path = "demo/results/result-{$order->id}.txt";
                Storage::disk('public')->put($path, "Hasil demo untuk {$order->order_code}");
                $submission = OrderSubmission::query()->create([
                    'order_id' => $order->id,
                    'freelancer_id' => $users[$freelancerKey]->id,
                    'version' => 1,
                    'submission_type' => $status === Order::STATUS_DONE ? 'final' : 'draft',
                    'notes' => 'Hasil pengerjaan versi pertama.',
                    'submitted_at' => now()->subHours(4),
                    'is_current' => true,
                ]);
                SubmissionFile::query()->create([
                    'order_submission_id' => $submission->id,
                    'original_name' => "result-{$order->id}.txt",
                    'file_path' => $path,
                    'mime_type' => 'text/plain',
                    'file_size' => Storage::disk('public')->size($path),
                    'is_final' => $status === Order::STATUS_DONE,
                ]);

                if ($status === Order::STATUS_REVISION) {
                    OrderRevision::query()->create([
                        'order_id' => $order->id,
                        'order_submission_id' => $submission->id,
                        'requested_by' => $order->client_id,
                        'revision_number' => 1,
                        'notes' => 'Tolong sesuaikan warna dan gaya bahasa dengan identitas brand.',
                        'forwarded_by' => $users['admin']->id,
                        'approved_revision_number' => 1,
                        'status' => OrderRevision::STATUS_FORWARDED,
                        'requested_at' => now()->subHours(2),
                        'forwarded_at' => now()->subHour(),
                    ]);
                }
            }

            if ($status === Order::STATUS_DONE) {
                $wallet = Wallet::query()->where('freelancer_id', $users[$freelancerKey]->id)->firstOrFail();
                $wallet->update(['available_balance' => $earning]);
                WalletTransaction::query()->create([
                    'transaction_code' => sprintf('WTX-%s-%04d', $this->date, $index + 1),
                    'wallet_id' => $wallet->id,
                    'order_id' => $order->id,
                    'type' => WalletTransaction::TYPE_EARNING,
                    'direction' => 'credit',
                    'amount' => $earning,
                    'balance_before' => 0,
                    'balance_after' => $earning,
                    'status' => 'completed',
                    'description' => "Pendapatan pesanan {$order->order_code}",
                    'transacted_at' => now()->subHour(),
                ]);
            }
        }

        $wallet = Wallet::query()->where('freelancer_id', $users['freelancer']->id)->firstOrFail();
        $wallet->update(['available_balance' => 250000, 'held_balance' => 100000]);

        WalletTransaction::query()->create([
            'transaction_code' => sprintf('WTX-%s-%04d', $this->date, 7),
            'wallet_id' => $wallet->id,
            'type' => WalletTransaction::TYPE_ADJUSTMENT,
            'direction' => 'credit',
            'amount' => 350000,
            'balance_before' => 0,
            'balance_after' => 350000,
            'status' => 'completed',
            'description' => 'Saldo demo awal freelancer',
            'transacted_at' => now()->subDays(2),
        ]);

        $withdrawal = Withdrawal::query()->create([
            'withdrawal_code' => sprintf('WDR-%s-%04d', $this->date, 1),
            'wallet_id' => $wallet->id,
            'freelancer_id' => $users['freelancer']->id,
            'amount' => 100000,
            'destination_type' => 'bank',
            'destination_provider' => 'BCA',
            'destination_account_number' => '0123456789',
            'destination_account_holder' => 'Fara Creative',
            'status' => Withdrawal::STATUS_PENDING,
            'requested_at' => now()->subHours(2),
        ]);

        WalletTransaction::query()->create([
            'transaction_code' => sprintf('WTX-%s-%04d', $this->date, 8),
            'wallet_id' => $wallet->id,
            'withdrawal_id' => $withdrawal->id,
            'type' => WalletTransaction::TYPE_WITHDRAWAL_HOLD,
            'direction' => 'debit',
            'amount' => 100000,
            'balance_before' => 350000,
            'balance_after' => 250000,
            'status' => 'completed',
            'description' => 'Saldo ditahan untuk withdraw demo',
            'transacted_at' => now()->subHours(2),
        ]);
    }
}
