<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_position_id', 'candidate_name', 'candidate_email', 'candidate_phone',
        'resume_path', 'cover_letter', 'status', 'rating', 'notes',
        'reviewer_id', 'created_by',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class, 'job_position_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(JobInterview::class, 'job_application_id');
    }
}
