<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionSlot extends Model
{
    public const STATUS_OPEN = 'open';
    public const STATUS_FULL = 'full';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'service_package_id',
        'production_date',
        'total_slots',
        'reserved_slots',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'production_date' => 'date',
            'total_slots' => 'integer',
            'reserved_slots' => 'integer',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class, 'service_package_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function hasAvailability(): bool
    {
        return $this->status === self::STATUS_OPEN
            && $this->reserved_slots < $this->total_slots;
    }
}
