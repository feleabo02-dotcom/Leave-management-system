<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSkill extends Model
{
    protected $fillable = ['employee_id', 'skill_id', 'skill_level_id', 'skill_type_id', 'valid_from', 'valid_to'];

    protected $casts = ['valid_from' => 'date', 'valid_to' => 'date'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function skillLevel(): BelongsTo
    {
        return $this->belongsTo(SkillLevel::class, 'skill_level_id');
    }

    public function skillType(): BelongsTo
    {
        return $this->belongsTo(SkillType::class, 'skill_type_id');
    }
}
