<?php

namespace App\Services;

use App\Models\LeaveAllocation;
use App\Models\LeaveType;
use App\Models\User;

class AllocationService
{
    public function allocate(User $user, LeaveType $leaveType, int $year, float $days): LeaveAllocation
    {
        return LeaveAllocation::updateOrCreate(
            [
                'user_id' => $user->id,
                'leave_type_id' => $leaveType->id,
                'year' => $year,
            ],
            [
                'allocated_days' => $days,
            ]
        );
    }
}
