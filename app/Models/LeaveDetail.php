<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveDetail extends Model
{
    use HasFactory;

    protected $table = 'leave_details';
    protected $primaryKey = 'form_id'; // Using form_id as PK for 1-to-1
    public $incrementing = false; // form_id is not auto-incrementing here
    public $timestamps = false; // No timestamps in the leave_details table migration

    protected $fillable = [
        'form_id',
        'leave_type',
        'start_date',
        'end_date',
        'days',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days' => 'integer',
    ];

    public function formRequest(): BelongsTo
    {
        return $this->belongsTo(FormRequest::class, 'form_id', 'form_id');
    }
}
