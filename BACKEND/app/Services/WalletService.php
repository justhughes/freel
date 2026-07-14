<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WalletService
{
    public function __construct(private readonly CodeGenerator $codes)
    {
    }

    public function walletFor(User $freelancer): Wallet
    {
        return Wallet::query()->firstOrCreate(
            ['freelancer_id' => $freelancer->id],
            ['available_balance' => 0, 'held_balance' => 0, 'withdrawn_balance' => 0]
        );
    }

    public function creditOrderEarning(Order $order): WalletTransaction
    {
        if (! $order->freelancer_id || $order->freelancer_earning <= 0) {
            throw ValidationException::withMessages([
                'order' => 'Pesanan tidak memiliki pendapatan freelancer yang valid.',
            ]);
        }

        return DB::transaction(function () use ($order) {
            $wallet = $this->walletFor($order->freelancer);
            $wallet = Wallet::query()->lockForUpdate()->findOrFail($wallet->id);

            $existing = WalletTransaction::query()
                ->where('wallet_id', $wallet->id)
                ->where('order_id', $order->id)
                ->where('type', WalletTransaction::TYPE_EARNING)
                ->first();

            if ($existing) {
                return $existing;
            }

            $before = $wallet->available_balance;
            $wallet->available_balance += $order->freelancer_earning;
            $wallet->save();

            return WalletTransaction::query()->create([
                'transaction_code' => $this->codes->walletTransaction(),
                'wallet_id' => $wallet->id,
                'order_id' => $order->id,
                'type' => WalletTransaction::TYPE_EARNING,
                'direction' => 'credit',
                'amount' => $order->freelancer_earning,
                'balance_before' => $before,
                'balance_after' => $wallet->available_balance,
                'status' => 'completed',
                'description' => "Pendapatan pesanan {$order->order_code}",
                'transacted_at' => now(),
            ]);
        });
    }

    public function requestWithdrawal(User $freelancer, int $amount, array $bankData): Withdrawal
    {
        if ($amount < 50000) {
            throw ValidationException::withMessages([
                'amount' => 'Minimal penarikan saldo adalah Rp50.000.',
            ]);
        }

        return DB::transaction(function () use ($freelancer, $amount, $bankData) {
            $wallet = $this->walletFor($freelancer);
            $wallet = Wallet::query()->lockForUpdate()->findOrFail($wallet->id);

            if ($wallet->available_balance < $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Saldo tersedia tidak mencukupi.',
                ]);
            }

            $before = $wallet->available_balance;
            $wallet->available_balance -= $amount;
            $wallet->held_balance += $amount;
            $wallet->save();

            $withdrawal = Withdrawal::query()->create([
                'withdrawal_code' => $this->codes->withdrawal(),
                'wallet_id' => $wallet->id,
                'freelancer_id' => $freelancer->id,
                'amount' => $amount,
                'destination_type' => $bankData['destination_type'],
                'destination_provider' => $bankData['destination_provider'],
                'destination_account_number' => $bankData['destination_account_number'],
                'destination_account_holder' => $bankData['destination_account_holder'],
                'status' => Withdrawal::STATUS_PENDING,
                'requested_at' => now(),
            ]);

            WalletTransaction::query()->create([
                'transaction_code' => $this->codes->walletTransaction(),
                'wallet_id' => $wallet->id,
                'withdrawal_id' => $withdrawal->id,
                'type' => WalletTransaction::TYPE_WITHDRAWAL_HOLD,
                'direction' => 'debit',
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $wallet->available_balance,
                'status' => 'completed',
                'description' => "Saldo ditahan untuk {$withdrawal->withdrawal_code}",
                'transacted_at' => now(),
            ]);

            return $withdrawal;
        });
    }

    public function approveWithdrawal(Withdrawal $withdrawal, User $admin): Withdrawal
    {
        if ($withdrawal->status !== Withdrawal::STATUS_PENDING) {
            throw ValidationException::withMessages(['withdrawal' => 'Withdraw sudah diproses.']);
        }

        $withdrawal->update([
            'status' => Withdrawal::STATUS_APPROVED,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        return $withdrawal->fresh();
    }

    public function markWithdrawalPaid(Withdrawal $withdrawal, User $admin, ?string $proofPath = null): Withdrawal
    {
        return DB::transaction(function () use ($withdrawal, $admin, $proofPath) {
            $locked = Withdrawal::query()->lockForUpdate()->findOrFail($withdrawal->id);

            if (! in_array($locked->status, [Withdrawal::STATUS_PENDING, Withdrawal::STATUS_APPROVED], true)) {
                throw ValidationException::withMessages(['withdrawal' => 'Withdraw tidak dapat ditandai dibayar.']);
            }

            $wallet = Wallet::query()->lockForUpdate()->findOrFail($locked->wallet_id);

            if ($wallet->held_balance < $locked->amount) {
                throw ValidationException::withMessages(['withdrawal' => 'Saldo tertahan tidak mencukupi.']);
            }

            $before = $wallet->available_balance;
            $wallet->held_balance -= $locked->amount;
            $wallet->withdrawn_balance += $locked->amount;
            $wallet->save();

            $locked->update([
                'status' => Withdrawal::STATUS_PAID,
                'reviewed_by' => $admin->id,
                'reviewed_at' => $locked->reviewed_at ?? now(),
                'paid_at' => now(),
                'proof_path' => $proofPath,
            ]);

            WalletTransaction::query()->create([
                'transaction_code' => $this->codes->walletTransaction(),
                'wallet_id' => $wallet->id,
                'withdrawal_id' => $locked->id,
                'type' => WalletTransaction::TYPE_WITHDRAWAL_PAID,
                'direction' => 'debit',
                'amount' => $locked->amount,
                'balance_before' => $before,
                'balance_after' => $wallet->available_balance,
                'status' => 'completed',
                'description' => "Withdraw {$locked->withdrawal_code} telah dibayar",
                'transacted_at' => now(),
            ]);

            return $locked->fresh();
        });
    }

    public function rejectWithdrawal(Withdrawal $withdrawal, User $admin, string $reason): Withdrawal
    {
        return DB::transaction(function () use ($withdrawal, $admin, $reason) {
            $locked = Withdrawal::query()->lockForUpdate()->findOrFail($withdrawal->id);

            if (! in_array($locked->status, [Withdrawal::STATUS_PENDING, Withdrawal::STATUS_APPROVED], true)) {
                throw ValidationException::withMessages(['withdrawal' => 'Withdraw sudah selesai diproses.']);
            }

            $wallet = Wallet::query()->lockForUpdate()->findOrFail($locked->wallet_id);
            $before = $wallet->available_balance;
            $wallet->held_balance = max(0, $wallet->held_balance - $locked->amount);
            $wallet->available_balance += $locked->amount;
            $wallet->save();

            $locked->update([
                'status' => Withdrawal::STATUS_REJECTED,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'rejection_reason' => $reason,
            ]);

            WalletTransaction::query()->create([
                'transaction_code' => $this->codes->walletTransaction(),
                'wallet_id' => $wallet->id,
                'withdrawal_id' => $locked->id,
                'type' => WalletTransaction::TYPE_WITHDRAWAL_RELEASE,
                'direction' => 'credit',
                'amount' => $locked->amount,
                'balance_before' => $before,
                'balance_after' => $wallet->available_balance,
                'status' => 'completed',
                'description' => "Pengembalian saldo withdraw {$locked->withdrawal_code}",
                'transacted_at' => now(),
            ]);

            return $locked->fresh();
        });
    }
}
