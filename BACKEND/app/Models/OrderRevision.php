<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderRevision extends Model
{
    public const STATUS_PENDING_ADMIN = 'pending_admin';
    public const STATUS_FORWARDED = 'forwarded';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    // Alias untuk menjaga kompatibilitas dengan data/kode lama.
    public const STATUS_OPEN = self::STATUS_PENDING_ADMIN;

    protected $fillable = [
        'order_id',
        'order_submission_id',
        'result_submission_id',
        'requested_by',
        'forwarded_by',
        'revision_number',
        'approved_revision_number',
        'notes',
        'admin_notes',
        'status',
        'requested_at',
        'forwarded_at',
        'completed_at',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'revision_number' => 'integer',
            'approved_revision_number' => 'integer',
            'requested_at' => 'datetime',
            'forwarded_at' => 'datetime',
            'completed_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** Hasil yang dikomentari klien. */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(OrderSubmission::class, 'order_submission_id');
    }

    /** Hasil baru yang dikirim freelancer untuk menjawab revisi ini. */
    public function resultSubmission(): BelongsTo
    {
        return $this->belongsTo(OrderSubmission::class, 'result_submission_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function forwarder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'forwarded_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING_ADMIN => 'Menunggu admin',
            self::STATUS_FORWARDED => 'Diteruskan ke freelancer',
            self::STATUS_IN_PROGRESS => 'Sedang dikerjakan',
            self::STATUS_COMPLETED => 'Hasil revisi sudah dikirim',
            self::STATUS_REJECTED => 'Ditolak admin',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => ucfirst((string) $this->status),
        };
    }
}
