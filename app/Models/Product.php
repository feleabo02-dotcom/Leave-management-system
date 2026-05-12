<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'type', 'cost', 'price', 'is_active'];

    protected $casts = [
        'cost' => 'decimal:2',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function stockLevels(): HasMany
    {
        return $this->hasMany(StockLevel::class);
    }

    public function stockMoves(): HasMany
    {
        return $this->hasMany(StockMove::class);
    }

    public function totalStock()
    {
        return $this->stockLevels()->sum('quantity');
    }
}
