<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GamificationChallenge extends Model
{
    protected $fillable = [
        'name', 'description', 'state', 'manager_id', 'period',
        'start_date', 'end_date', 'reward_badge_id', 'visibility_mode',
    ];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function rewardBadge(): BelongsTo
    {
        return $this->belongsTo(GamificationBadge::class, 'reward_badge_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(GamificationChallengeLine::class, 'challenge_id');
    }
}
