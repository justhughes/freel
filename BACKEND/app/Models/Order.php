<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_QUEUE = 'queue';
    public const STATUS_PROCESS = 'process';
    public const STATUS_REVIEW = 'review';
    public const STATUS_REVISION_REQUESTED = 'revision_requested';
    public const STATUS_REVISION = 'revision';
    public const STATUS_DONE = 'done';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_PROBLEM = 'problem';

    public const SPEED_REGULAR = 'regular';
    public const SPEED_FAST = 'fast';
    public const SPEED_EXPRESS = 'express';

    protected $fillable = [
        'order_code',
        'client_id',
        'service_package_id',
        'production_slot_id',
        'freelancer_id',
        'voucher_id',
        'title',
        'business_name',
        'product_description',
        'target_audience',
        'visual_reference',
        'brief',
        'platform',
        'content_size',
        'quantity',
        'speed_type',
        'booking_date',
        'start_date',
        'deadline_at',
        'base_price',
        'speed_fee',
        'subtotal',
        'discount_amount',
        'total_amount',
        'freelancer_earning',
        'platform_margin',
        'revision_limit',
        'revision_used',
        'status',
        'paid_at',
        'taken_at',
        'submitted_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'start_date' => 'date',
            'deadline_at' => 'datetime',
            'base_price' => 'integer',
            'speed_fee' => 'integer',
            'subtotal' => 'integer',
            'discount_amount' => 'integer',
            'total_amount' => 'integer',
            'freelancer_earning' => 'integer',
            'platform_margin' => 'integer',
            'revision_limit' => 'integer',
            'revision_used' => 'integer',
            'paid_at' => 'datetime',
            'taken_at' => 'datetime',
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class, 'service_package_id');
    }

    public function productionSlot(): BelongsTo
    {
        return $this->belongsTo(ProductionSlot::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(OrderAsset::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(OrderAssignment::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(OrderSubmission::class)
            ->orderByDesc('version');
    }

    /**
     * Versi hasil terbaru. Relasi ini memakai nomor versi, sehingga tetap benar
     * walaupun flag is_current pada data lama tidak konsisten.
     */
    public function currentSubmission(): HasOne
    {
        return $this->hasOne(OrderSubmission::class)
            ->latestOfMany('version');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(OrderRevision::class)
            ->orderByDesc('requested_at');
    }

    public function freelancerRevisions(): HasMany
    {
        return $this->hasMany(OrderRevision::class)
            ->whereIn('status', [
                OrderRevision::STATUS_FORWARDED,
                OrderRevision::STATUS_IN_PROGRESS,
                OrderRevision::STATUS_COMPLETED,
            ])
            ->orderByDesc('forwarded_at');
    }

    public function activeRevision(): HasOne
    {
        return $this->hasOne(OrderRevision::class)
            ->whereIn('status', [
                OrderRevision::STATUS_PENDING_ADMIN,
                OrderRevision::STATUS_FORWARDED,
                OrderRevision::STATUS_IN_PROGRESS,
            ])
            ->latestOfMany();
    }

    public function scopeJobBoard(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_QUEUE)
            ->whereNull('freelancer_id');
    }

    public function scopeForClient(Builder $query, int $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeForFreelancer(Builder $query, int $freelancerId): Builder
    {
        return $query->where('freelancer_id', $freelancerId);
    }

    public function canRequestRevision(): bool
    {
        if ($this->status !== self::STATUS_REVIEW) {
            return false;
        }

        if ($this->revision_used >= $this->revision_limit) {
            return false;
        }

        if (! $this->currentSubmission()->exists()) {
            return false;
        }

        return ! $this->revisions()
            ->whereIn('status', [
                OrderRevision::STATUS_PENDING_ADMIN,
                OrderRevision::STATUS_FORWARDED,
                OrderRevision::STATUS_IN_PROGRESS,
            ])
            ->exists();
    }

    public function getRemainingRevisionsAttribute(): int
    {
        return max(0, $this->revision_limit - $this->revision_used);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING_PAYMENT => 'Menunggu Pembayaran',
            self::STATUS_QUEUE => 'Antrean',
            self::STATUS_PROCESS => 'Sedang Diproses',
            self::STATUS_REVIEW => 'Review Hasil',
            self::STATUS_REVISION_REQUESTED => 'Menunggu Pemeriksaan Revisi',
            self::STATUS_REVISION => 'Sedang Direvisi',
            self::STATUS_DONE => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            self::STATUS_PROBLEM => 'Bermasalah',
            default => ucfirst((string) $this->status),
        };
    }
}
