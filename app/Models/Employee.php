<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\EmployeeSkill;
use App\Models\ResumeLine;
use App\Models\Expense;
use App\Models\Shift;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_code', 'user_id', 'company_id', 'department_id', 
        'position_id', 'manager_id', 'shift_id', 'hire_date', 'dob', 'gender', 
        'status', 'salary_structure_id', 'emergency_contact'
    ];

    protected $casts = [
        'hire_date' => 'date',
        'dob' => 'date',
        'emergency_contact' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(EmployeeHistory::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(EmployeeSkill::class);
    }

    public function resumeLines(): HasMany
    {
        return $this->hasMany(ResumeLine::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
