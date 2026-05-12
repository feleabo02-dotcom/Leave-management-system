<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeLine extends Model
{
    protected $fillable = ['employee_id', 'name', 'date_start', 'date_end', 'description', 'line_type_id', 'external_url'];

    protected $casts = ['date_start' => 'date', 'date_end' => 'date'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function lineType(): BelongsTo
    {
        return $this->belongsTo(ResumeLineType::class, 'line_type_id');
    }
}
