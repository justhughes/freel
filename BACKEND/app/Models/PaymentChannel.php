<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentChannel extends Model
{
    protected $fillable = [
        'payment_method_id',
        'code',
        'name',
        'account_name',
        'account_number',
        'instructions',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function method(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
