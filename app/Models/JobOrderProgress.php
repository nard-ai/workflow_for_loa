<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobOrderProgress extends Model
{
    protected $table = 'job_order_progress';

    protected $fillable = [
        'job_order_id',
        'user_id',
        'update_type',
        'progress_note',
        'percentage_complete',
        'current_location',
        'issues_encountered',
        'estimated_time_remaining',
    ];

    protected $casts = [
        'percentage_complete' => 'integer',
        'estimated_time_remaining' => 'integer',
    ];

    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class, 'job_order_id');
    }

    public function progressUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'accnt_id');
    }

    public function getEstimatedTimeRemainingFormattedAttribute(): ?string
    {
        if (!$this->estimated_time_remaining) return null;
        $minutes = (int) $this->estimated_time_remaining;
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return ($h ? $h . 'h ' : '') . ($m ? $m . 'm' : '');
    }
}
