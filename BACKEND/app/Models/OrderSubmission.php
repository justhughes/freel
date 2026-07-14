<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderSubmission extends Model
{
    protected $fillable = [
        'order_id',
        'freelancer_id',
        'version',
        'submission_type',
        'notes',
        'submitted_at',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'submitted_at' => 'datetime',
            'is_current' => 'boolean',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(SubmissionFile::class);
    }
}
