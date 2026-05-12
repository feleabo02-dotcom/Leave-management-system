<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResumeLineType extends Model
{
    protected $fillable = ['name', 'sequence', 'is_course'];

    protected $casts = ['is_course' => 'boolean'];
}
