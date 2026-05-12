<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryStructure extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'base_salary', 'is_active'];

    protected $casts = ['is_active' => 'boolean', 'base_salary' => 'decimal:2'];

    public function components(): HasMany
    {
        return $this->hasMany(SalaryComponent::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
