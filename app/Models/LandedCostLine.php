<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandedCostLine extends Model
{
    protected $fillable = ['landed_cost_id', 'name', 'amount', 'split_method'];

    public function landedCost(): BelongsTo { return $this->belongsTo(LandedCost::class); }
}
