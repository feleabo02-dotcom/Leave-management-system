<?php

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;

class LeaveRequestPolicy
{
    public function view(User $user, LeaveRequest $request): bool
    {
        return $user->id === $request->user_id || $user->hasAnyRole(['manager', 'admin', 'hr']);
    }

    public function approve(User $user, LeaveRequest $request): bool
    {
        return $user->hasAnyRole(['manager', 'admin', 'hr']);
    }

    public function reject(User $user, LeaveRequest $request): bool
    {
        return $user->hasAnyRole(['manager', 'admin', 'hr']);
    }
}
