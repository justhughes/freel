<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    public const TYPE_PERCENT = 'percent';
    public const TYPE_FIXED = 'fixed';

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_percent',
        'discount_amount',
        'minimum_order_amount',
        'maximum_discount_amount',
        'usage_limit',
        'used_count',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_percent' => 'decimal:2',
            'discount_amount' => 'integer',
            'minimum_order_amount' => 'integer',
            'maximum_discount_amount' => 'integer',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    public function isUsableFor(int $subtotal): bool
    {
        if (! $this->is_active || $subtotal < $this->minimum_order_amount) {
            return false;
        }

        if ($this->starts_at && now()->isBefore($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && now()->isAfter($this->ends_at)) {
            return false;
        }

        return $this->usage_limit === null || $this->used_count < $this->usage_limit;
    }

    public function calculateDiscount(int $subtotal): int
    {
        if (! $this->isUsableFor($subtotal)) {
            return 0;
        }

        $discount = $this->discount_type === self::TYPE_PERCENT
            ? (int) round($subtotal * ((float) $this->discount_percent / 100))
            : (int) $this->discount_amount;

        if ($this->maximum_discount_amount !== null) {
            $discount = min($discount, $this->maximum_discount_amount);
        }

        return min($discount, $subtotal);
    }
}
