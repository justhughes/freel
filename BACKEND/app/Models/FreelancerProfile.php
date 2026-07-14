<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreelancerProfile extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PARTIAL = 'partially_approved';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'bio',
        'experience_years',
        'portfolio_url',
        'portfolio_file_path',
        'application_status',
        'reviewed_by',
        'reviewed_at',
        'admin_notes',
        'payout_type',
        'payout_provider',
        'payout_account_number',
        'payout_account_holder',
    ];

    protected function casts(): array
    {
        return [
            'experience_years' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
