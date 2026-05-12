<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkillLevel extends Model
{
    protected $fillable = ['skill_type_id', 'name', 'level_progress', 'default_level'];

    protected $casts = ['default_level' => 'boolean'];

    public function skillType(): BelongsTo
    {
        return $this->belongsTo(SkillType::class, 'skill_type_id');
    }
}
