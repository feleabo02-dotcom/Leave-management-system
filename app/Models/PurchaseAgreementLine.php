<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseAgreementLine extends Model
{
    protected $fillable = ['purchase_agreement_id', 'product_id', 'quantity', 'received_qty', 'unit_price'];

    public function agreement(): BelongsTo { return $this->belongsTo(PurchaseAgreement::class, 'purchase_agreement_id'); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
