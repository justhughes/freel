<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicePackage extends Model
{
    protected $fillable = [
        'service_category_id',
        'code',
        'name',
        'slug',
        'description',
        'includes',
        'base_price',
        'regular_days',
        'fast_days',
        'express_days',
        'fast_fee_percent',
        'express_fee_percent',
        'revision_limit',
        'total_slot',
        'freelancer_fee_percent',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'includes' => 'array',
            'base_price' => 'integer',
            'regular_days' => 'integer',
            'fast_days' => 'integer',
            'express_days' => 'integer',
            'fast_fee_percent' => 'decimal:2',
            'express_fee_percent' => 'decimal:2',
            'revision_limit' => 'integer',
            'total_slot' => 'integer',
            'freelancer_fee_percent' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function productionSlots(): HasMany
    {
        return $this->hasMany(ProductionSlot::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function feePercentForSpeed(string $speedType): float
    {
        return match ($speedType) {
            Order::SPEED_FAST => (float) $this->fast_fee_percent,
            Order::SPEED_EXPRESS => (float) $this->express_fee_percent,
            default => 0.0,
        };
    }

    public function daysForSpeed(string $speedType): int
    {
        return match ($speedType) {
            Order::SPEED_FAST => $this->fast_days ?? $this->regular_days,
            Order::SPEED_EXPRESS => $this->express_days ?? $this->fast_days ?? $this->regular_days,
            default => $this->regular_days,
        };
    }
}
