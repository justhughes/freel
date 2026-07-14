<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    public const TYPE_EARNING = 'earning';
    public const TYPE_WITHDRAWAL_HOLD = 'withdrawal_hold';
    public const TYPE_WITHDRAWAL_RELEASE = 'withdrawal_release';
    public const TYPE_WITHDRAWAL_PAID = 'withdrawal_paid';
    public const TYPE_ADJUSTMENT = 'adjustment';

    protected $fillable = [
        'transaction_code',
        'wallet_id',
        'order_id',
        'withdrawal_id',
        'type',
        'direction',
        'amount',
        'balance_before',
        'balance_after',
        'status',
        'description',
        'transacted_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'balance_before' => 'integer',
            'balance_after' => 'integer',
            'transacted_at' => 'datetime',
        ];
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function withdrawal(): BelongsTo
    {
        return $this->belongsTo(Withdrawal::class);
    }
}
