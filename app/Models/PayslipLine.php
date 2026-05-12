<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayslipLine extends Model
{
    use HasFactory;

    protected $fillable = ['payslip_id', 'name', 'code', 'type', 'amount'];

    protected $casts = ['amount' => 'decimal:2'];

    public function payslip(): BelongsTo
    {
        return $this->belongsTo(Payslip::class);
    }
}
