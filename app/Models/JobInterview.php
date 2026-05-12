<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobInterview extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_application_id', 'interviewer_id', 'interview_date',
        'interview_mode', 'status', 'notes', 'feedback', 'rating',
    ];

    protected $casts = [
        'interview_date' => 'datetime',
        'rating' => 'integer',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }
}
