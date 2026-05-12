<?php

namespace Tests\Feature;

use App\Models\LeaveAllocation;
use App\Models\LeaveHistory;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveRequestApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_submit_and_cancel_approved_request_and_restore_balance(): void
    {
        $employeeRole = Role::query()->create(['name' => 'Employee', 'slug' => 'employee']);
        $managerRole = Role::query()->create(['name' => 'Manager', 'slug' => 'manager']);

        /** @var User $manager */
        $manager = User::factory()->create();
        $manager->roles()->sync([$managerRole->id]);

        /** @var User $employee */
        $employee = User::factory()->create([
            'manager_id' => $manager->id,
            'hire_date' => now()->subMonths(14)->toDateString(),
        ]);
        $employee->roles()->sync([$employeeRole->id]);

        $leaveType = LeaveType::query()->create([
            'name' => 'Annual Leave',
            'code' => 'ANNUAL',
            'is_paid' => true,
            'requires_manager_approval' => true,
            'requires_hr_approval' => false,
            'carry_forward' => true,
            'carry_forward_cap' => 5,
            'active' => true,
            'allocation_type' => 'fixed',
            'validation_type' => 'manager',
            'request_unit' => 'day',
            'allow_half_day' => true,
            'allow_hour' => false,
            'accrual_rate' => 0,
            'accrual_cap' => null,
            'requires_allocation' => true,
        ]);

        LeaveAllocation::query()->create([
            'user_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'year' => (int) now()->format('Y'),
            'allocated_days' => 20,
            'used_days' => 0,
            'carried_over_days' => 2,
        ]);

        $requestResponse = $this->actingAs($employee)->postJson('/api/v1/leave/requests', [
            'leave_type_id' => $leaveType->id,
            'start_date' => now()->addDays(10)->toDateString(),
            'end_date' => now()->addDays(11)->toDateString(),
            'request_unit' => 'day',
            'reason' => 'Personal travel',
        ]);

        $requestResponse
            ->assertCreated()
            ->assertJsonPath('message', 'Leave request submitted.');

        $leaveRequestId = (int) $requestResponse->json('data.id');

        $this->actingAs($manager)
            ->postJson("/api/v1/leave/requests/{$leaveRequestId}/approve/manager")
            ->assertOk()
            ->assertJsonPath('data.status', 'approved');

        $allocationAfterApprove = LeaveAllocation::query()
            ->where('user_id', '=', $employee->id)
            ->where('leave_type_id', '=', $leaveType->id)
            ->firstOrFail();

        $this->assertEquals(2.0, (float) $allocationAfterApprove->used_days);

        $this->actingAs($employee)
            ->postJson("/api/v1/leave/requests/{$leaveRequestId}/cancel", ['reason' => 'Plan changed'])
            ->assertOk()
            ->assertJsonPath('data.status', 'cancelled');

        $allocationAfterCancel = LeaveAllocation::query()
            ->where('user_id', '=', $employee->id)
            ->where('leave_type_id', '=', $leaveType->id)
            ->firstOrFail();

        $this->assertEquals(0.0, (float) $allocationAfterCancel->used_days);

        $this->assertDatabaseHas('leave_histories', [
            'reference_id' => $leaveRequestId,
            'action' => 'approve_deduct',
        ]);

        $this->assertDatabaseHas('leave_histories', [
            'reference_id' => $leaveRequestId,
            'action' => 'cancel_restore',
        ]);
    }

    public function test_balance_and_history_endpoints_return_employee_data(): void
    {
        $employeeRole = Role::query()->create(['name' => 'Employee', 'slug' => 'employee']);

        /** @var User $employee */
        $employee = User::factory()->create();
        $employee->roles()->sync([$employeeRole->id]);

        $leaveType = LeaveType::query()->create([
            'name' => 'Sick Leave',
            'code' => 'SICK',
            'is_paid' => true,
            'requires_manager_approval' => true,
            'requires_hr_approval' => false,
            'carry_forward' => false,
            'carry_forward_cap' => 0,
            'active' => true,
            'allocation_type' => 'fixed',
            'validation_type' => 'manager',
            'request_unit' => 'day',
            'allow_half_day' => true,
            'allow_hour' => true,
            'accrual_rate' => 0,
            'accrual_cap' => null,
            'requires_allocation' => true,
        ]);

        LeaveAllocation::query()->create([
            'user_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'year' => (int) now()->format('Y'),
            'allocated_days' => 10,
            'used_days' => 2,
            'carried_over_days' => 1,
        ]);

        LeaveHistory::query()->create([
            'user_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'year' => (int) now()->format('Y'),
            'action' => 'approve_deduct',
            'delta_days' => -2,
            'before_days' => 11,
            'after_days' => 9,
            'reference_type' => LeaveRequest::class,
            'reference_id' => 999,
            'actor_id' => null,
            'metadata' => ['source' => 'test'],
        ]);

        $this->actingAs($employee)
            ->getJson('/api/v1/leave/balances')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.remaining_days', 9);

        $this->actingAs($employee)
            ->getJson('/api/v1/leave/history')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.action', 'approve_deduct');
    }

    public function test_non_privileged_user_cannot_approve_requests(): void
    {
        $employeeRole = Role::query()->create(['name' => 'Employee', 'slug' => 'employee']);

        /** @var User $employee */
        $employee = User::factory()->create();
        $employee->roles()->sync([$employeeRole->id]);

        $leaveRequest = LeaveRequest::query()->create([
            'user_id' => $employee->id,
            'leave_type_id' => LeaveType::query()->create([
                'name' => 'Emergency',
                'code' => 'EMG',
                'is_paid' => true,
                'requires_manager_approval' => true,
                'requires_hr_approval' => false,
                'carry_forward' => false,
                'carry_forward_cap' => 0,
                'active' => true,
                'allocation_type' => 'fixed',
                'validation_type' => 'manager',
                'request_unit' => 'day',
                'allow_half_day' => false,
                'allow_hour' => false,
                'accrual_rate' => 0,
                'accrual_cap' => null,
                'requires_allocation' => true,
            ])->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'days' => 2,
            'reason' => 'test',
            'status' => 'submitted',
            'request_unit' => 'day',
            'manager_status' => 'pending',
            'hr_status' => 'pending',
        ]);

        $this->actingAs($employee)
            ->postJson("/api/v1/leave/requests/{$leaveRequest->id}/approve/manager")
            ->assertForbidden();
    }
}
