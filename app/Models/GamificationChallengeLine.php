<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GamificationChallengeLine extends Model
{
    protected $fillable = ['challenge_id', 'definition_id', 'sequence', 'target_goal'];

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(GamificationChallenge::class, 'challenge_id');
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(GamificationGoalDefinition::class, 'definition_id');
    }
}
