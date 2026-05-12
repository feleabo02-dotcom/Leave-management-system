<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveHistory extends Model
{
    protected $fillable = [
        'user_id',
        'leave_type_id',
        'year',
        'action',
        'delta_days',
        'before_days',
        'after_days',
        'reference_type',
        'reference_id',
        'actor_id',
        'metadata',
    ];

    protected $casts = [
        'delta_days' => 'decimal:2',
        'before_days' => 'decimal:2',
        'after_days' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
