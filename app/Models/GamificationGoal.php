<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GamificationGoal extends Model
{
    protected $fillable = [
        'definition_id', 'user_id', 'line_id', 'challenge_id',
        'start_date', 'end_date', 'target_goal', 'current', 'completeness', 'state',
    ];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function definition(): BelongsTo
    {
        return $this->belongsTo(GamificationGoalDefinition::class, 'definition_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(GamificationChallenge::class, 'challenge_id');
    }
}
