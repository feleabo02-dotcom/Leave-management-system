<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'partner_id', 'date', 'due_date', 'amount_total',
        'amount_tax', 'amount_untaxed', 'status', 'ref_number', 'journal_id', 'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'amount_total' => 'decimal:2',
        'amount_tax' => 'decimal:2',
        'amount_untaxed' => 'decimal:2',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'partner_id');
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(AccountPayment::class, 'invoice_id');
    }
}
