<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'type', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function journalItems(): HasMany
    {
        return $this->hasMany(JournalItem::class);
    }

    public function getBalanceAttribute()
    {
        $debit = $this->journalItems()->sum('debit');
        $credit = $this->journalItems()->sum('credit');
        
        // Normal balance logic (simplified)
        if (in_array($this->type, ['asset', 'expense', 'receivable'])) {
            return $debit - $credit;
        }
        return $credit - $debit;
    }
}
