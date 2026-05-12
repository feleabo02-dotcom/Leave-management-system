<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicHoliday extends Model
{
    protected $fillable = [
        'name',
        'date',
        'company_id',
        'recurring_yearly',
        'color',
    ];

    protected $casts = [
        'date' => 'date',
        'recurring_yearly' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
