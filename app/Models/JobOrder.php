<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobOrder extends Model
{
    use HasFactory;

    protected $primaryKey = 'job_order_id';

    protected $fillable = [
        'job_order_number',
        'form_id',
        'created_by',
        'control_number',
        'date_prepared',
        'received_by',
        'requestor_name',
        'department',
        'request_description',
        'assistance',
        'repair_repaint',
        'installation',
        'cleaning',
        'check_up_inspection',
        'construction_fabrication',
        'pull_out_transfer',
        'replacement',
        'findings',
        'actions_taken',
        'date_received',
        'recommendations',
        'job_completed_by',
        'date_completed',
        'job_completed',
        'for_further_action',
        'requestor_comments',
        'requestor_signature',
        'requestor_signature_date',
        'status',
        'job_started_at',
        'job_completed_at',
        'work_duration_minutes'
    ];

    protected $casts = [
        'job_started_at' => 'datetime',
        'job_completed_at' => 'datetime',
        'date_prepared' => 'date',
        'date_received' => 'date',
        'date_completed' => 'date',
        'requestor_signature_date' => 'date',
        'assistance' => 'boolean',
        'repair_repaint' => 'boolean',
        'installation' => 'boolean',
        'cleaning' => 'boolean',
        'check_up_inspection' => 'boolean',
        'construction_fabrication' => 'boolean',
        'pull_out_transfer' => 'boolean',
        'replacement' => 'boolean',
        'job_completed' => 'boolean',
        'for_further_action' => 'boolean'
    ];

    // Relationships
    public function formRequest(): BelongsTo
    {
        return $this->belongsTo(FormRequest::class, 'form_id', 'form_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'accnt_id');
    }

    public function created_by_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'accnt_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to', 'accnt_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(JobOrderProgress::class, 'job_order_id', 'job_order_id');
    }

    // Scopes and Static Methods
    public static function needingFeedbackForUser($userId)
    {
        return self::whereHas('formRequest', function ($query) use ($userId) {
            $query->where('requested_by', $userId);
        })
            ->where('status', 'Completed')
            ->where('job_completed', true)
            ->get();
    }

    public static function userHasPendingFeedback($userId): bool
    {
        return self::needingFeedbackForUser($userId)->count() > 0;
    }

    // Instance Methods
    public function needsFeedback(): bool
    {
        return $this->status === 'Completed' && $this->job_completed;
    }

    public function canStart(): bool
    {
        return $this->status === 'Pending';
    }

    public function canComplete(): bool
    {
        return $this->status === 'In Progress';
    }

    public function start(): bool
    {
        if (!$this->canStart()) {
            return false;
        }

        $this->update([
            'status' => 'In Progress',
            'job_started_at' => now()
        ]);

        return true;
    }

    public function complete(array $data = []): bool
    {
        if (!$this->canComplete()) {
            return false;
        }

        $updateData = array_merge([
            'status' => 'Completed',
            'job_completed_at' => now(),
            'job_completed' => true
        ], $data);

        $this->update($updateData);

        return true;
    }

    // Status helpers
    public function isPending(): bool
    {
        return $this->status === 'Pending';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'In Progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'Completed';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Pending' => 'yellow',
            'In Progress' => 'blue',
            'Completed' => 'green',
            'For Further Action' => 'orange',
            default => 'gray'
        };
    }
}
