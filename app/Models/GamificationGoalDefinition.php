<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamificationGoalDefinition extends Model
{
    protected $fillable = [
        'name', 'description', 'computation_mode', 'display_mode',
        'condition', 'suffix', 'domain', 'compute_code',
    ];
}
