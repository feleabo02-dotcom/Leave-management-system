<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GamificationKarmaTracking extends Model
{
    protected $fillable = ['user_id', 'old_value', 'new_value', 'gain', 'reason', 'tracking_date'];

    protected $casts = ['tracking_date' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
