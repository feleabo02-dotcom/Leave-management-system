<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamificationKarmaRank extends Model
{
    protected $fillable = ['name', 'description', 'karma_min'];
}
