<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class CodeGenerator
{
    public function order(): string
    {
        return $this->generate(\App\Models\Order::class, 'order_code', 'CNT');
    }

    public function payment(): string
    {
        return $this->generate(\App\Models\Payment::class, 'payment_code', 'PAY');
    }

    public function withdrawal(): string
    {
        return $this->generate(\App\Models\Withdrawal::class, 'withdrawal_code', 'WDR');
    }

    public function walletTransaction(): string
    {
        return $this->generate(\App\Models\WalletTransaction::class, 'transaction_code', 'WTX');
    }

    private function generate(string $modelClass, string $column, string $prefix): string
    {
        /** @var Model $model */
        $model = new $modelClass();
        $date = now()->format('Ymd');
        $base = "{$prefix}-{$date}-";

        $lastCode = $modelClass::query()
            ->where($column, 'like', $base.'%')
            ->orderByDesc($column)
            ->value($column);

        $next = $lastCode ? ((int) substr($lastCode, -4)) + 1 : 1;

        return $base.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
