<?php

namespace App\Services;

use App\Models\LeaveHistory;
use App\Models\LeaveRequest;

class LeaveLedgerService
{
    public function recordApprovalDeduction(LeaveRequest $request, float $beforeDays, float $afterDays, ?int $actorId = null): LeaveHistory
    {
        return LeaveHistory::create([
            'user_id' => $request->user_id,
            'leave_type_id' => $request->leave_type_id,
            'year' => (int) $request->start_date->format('Y'),
            'action' => 'approve_deduct',
            'delta_days' => (float) (-1 * $request->days),
            'before_days' => $beforeDays,
            'after_days' => $afterDays,
            'reference_type' => LeaveRequest::class,
            'reference_id' => $request->id,
            'actor_id' => $actorId,
            'metadata' => [
                'status' => $request->status,
                'request_unit' => $request->request_unit,
            ],
        ]);
    }

    public function recordCancellationRestore(LeaveRequest $request, float $beforeDays, float $afterDays, ?int $actorId = null): LeaveHistory
    {
        return LeaveHistory::create([
            'user_id' => $request->user_id,
            'leave_type_id' => $request->leave_type_id,
            'year' => (int) $request->start_date->format('Y'),
            'action' => 'cancel_restore',
            'delta_days' => (float) $request->days,
            'before_days' => $beforeDays,
            'after_days' => $afterDays,
            'reference_type' => LeaveRequest::class,
            'reference_id' => $request->id,
            'actor_id' => $actorId,
            'metadata' => [
                'status' => $request->status,
                'request_unit' => $request->request_unit,
            ],
        ]);
    }
}
