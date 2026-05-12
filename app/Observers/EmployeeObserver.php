<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\EmployeeHistory;
use Illuminate\Support\Facades\Auth;

class EmployeeObserver
{
    /**
     * Handle the Employee "updated" event.
     */
    public function updated(Employee $employee): void
    {
        $changes = $employee->getChanges();
        $original = $employee->getOriginal();

        // We only want to track certain fields
        $trackedFields = ['department_id', 'position_id', 'manager_id', 'status', 'salary_structure_id'];
        
        $oldValues = [];
        $newValues = [];
        $hasTrackedChanges = false;

        foreach ($trackedFields as $field) {
            if (array_key_exists($field, $changes)) {
                $oldValues[$field] = $original[$field];
                $newValues[$field] = $changes[$field];
                $hasTrackedChanges = true;
            }
        }

        if ($hasTrackedChanges) {
            // Determine change type
            $changeType = 'General Update';
            if (isset($changes['position_id'])) $changeType = 'Promotion/Role Change';
            if (isset($changes['department_id'])) $changeType = 'Transfer';
            if (isset($changes['status'])) $changeType = 'Status Change';

            EmployeeHistory::create([
                'employee_id' => $employee->id,
                'change_type' => $changeType,
                'old_value' => $oldValues,
                'new_value' => $newValues,
                'changed_by' => Auth::id(),
            ]);
        }
    }

    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
        EmployeeHistory::create([
            'employee_id' => $employee->id,
            'change_type' => 'Onboarding',
            'old_value' => null,
            'new_value' => [
                'department_id' => $employee->department_id,
                'position_id' => $employee->position_id,
                'status' => $employee->status,
            ],
            'changed_by' => Auth::id(),
        ]);
    }
}
