<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionFile extends Model
{
    protected $fillable = [
        'order_submission_id',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'is_final',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'is_final' => 'boolean',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(OrderSubmission::class, 'order_submission_id');
    }
}
