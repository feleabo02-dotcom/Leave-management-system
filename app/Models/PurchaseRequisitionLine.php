<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequisitionLine extends Model
{
    protected $fillable = ['purchase_requisition_id', 'product_id', 'quantity', 'received_qty', 'specifications'];

    public function requisition(): BelongsTo { return $this->belongsTo(PurchaseRequisition::class, 'purchase_requisition_id'); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
