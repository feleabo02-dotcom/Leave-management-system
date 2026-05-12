<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickingBatch extends Model
{
    protected $fillable = [
        'name', 'description', 'user_id', 'company_id', 'state',
        'scheduled_date', 'is_wave',
    ];

    protected $casts = ['scheduled_date' => 'datetime', 'is_wave' => 'boolean'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
}
