<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GamificationBadge extends Model
{
    protected $fillable = ['name', 'active', 'description', 'level', 'rule_auth'];

    protected $casts = ['active' => 'boolean'];

    public function assignments(): HasMany
    {
        return $this->hasMany(GamificationBadgeAssignment::class, 'badge_id');
    }
}
