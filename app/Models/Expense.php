<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'name', 'description', 'employee_id', 'category_id', 'department_id',
        'manager_id', 'approved_by', 'amount', 'tax_amount', 'total_amount',
        'currency', 'expense_date', 'approval_date', 'state', 'payment_method',
        'receipt_path', 'notes', 'created_by',
    ];

    protected $casts = ['expense_date' => 'date', 'approval_date' => 'date'];

    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function category(): BelongsTo { return $this->belongsTo(ExpenseCategory::class, 'category_id'); }
    public function department(): BelongsTo { return $this->belongsTo(Department::class); }
    public function manager(): BelongsTo { return $this->belongsTo(User::class, 'manager_id'); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
