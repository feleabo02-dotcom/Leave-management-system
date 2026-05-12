<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetDepreciation extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id', 'method', 'original_cost', 'salvage_value',
        'useful_life_years', 'annual_depreciation', 'accumulated_depreciation',
        'start_date', 'end_date',
    ];

    protected $casts = [
        'original_cost' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'annual_depreciation' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
