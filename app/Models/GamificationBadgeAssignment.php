<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GamificationBadgeAssignment extends Model
{
    protected $fillable = ['badge_id', 'user_id', 'sender_id', 'challenge_id', 'employee_id', 'comment'];

    public function badge(): BelongsTo
    {
        return $this->belongsTo(GamificationBadge::class, 'badge_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(GamificationChallenge::class, 'challenge_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
