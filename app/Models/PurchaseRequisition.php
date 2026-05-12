<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequisition extends Model
{
    protected $fillable = [
        'name', 'description', 'status', 'requested_by',
        'approved_by', 'required_date', 'notes',
    ];

    protected $casts = ['required_date' => 'date'];

    public function requester(): BelongsTo { return $this->belongsTo(User::class, 'requested_by'); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function lines(): HasMany { return $this->hasMany(PurchaseRequisitionLine::class, 'purchase_requisition_id'); }
}
