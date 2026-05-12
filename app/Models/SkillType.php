<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkillType extends Model
{
    protected $fillable = ['name', 'active', 'sequence', 'color', 'is_certification'];

    protected $casts = ['active' => 'boolean', 'is_certification' => 'boolean'];

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class, 'skill_type_id');
    }

    public function levels(): HasMany
    {
        return $this->hasMany(SkillLevel::class, 'skill_type_id');
    }
}
