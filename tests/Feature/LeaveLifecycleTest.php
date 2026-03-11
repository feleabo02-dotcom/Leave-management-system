<?php

namespace Tests\Feature;

use App\Models\LeaveAllocation;
use App\Models\LeaveHistory;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Role;
use App\Models\User;
use App\Services\LeaveRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_request_is_approved_by_manager_and_hr_and_deducts_balance_with_ledger_entry(): void
    {
        $employeeRole = Role::query()->create(['name' => 'Employee', 'slug' => 'employee']);
        $managerRole = Role::query()->create(['name' => 'Manager', 'slug' => 'manager']);
        $hrRole = Role::query()->create(['name' => 'HR', 'slug' => 'hr']);

        /** @var User $manager */
        $manager = User::factory()->create();
        $manager->roles()->sync([$managerRole->id]);

        /** @var User $hr */
        $hr = User::factory()->create();
        $hr->roles()->sync([$hrRole->id]);

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
            'validation_type' => 'both',
            'allocation_type' => 'fixed',
            'request_unit' => 'day',
            'allow_half_day' => true,
            'allow_hour' => false,
            'requires_allocation' => true,
        ]);

        $year = (int) now()->format('Y');

        LeaveAllocation::query()->create([
            'user_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'year' => $year,
            'allocated_days' => 20,
            'used_days' => 0,
            'carried_over_days' => 2,
        ]);

        $startDate = now()->addDays(7)->startOfDay();
        $endDate = now()->addDays(8)->startOfDay();

        /** @var LeaveRequestService $service */
        $service = app(LeaveRequestService::class);

        $leaveRequest = $service->createRequest(
            $employee,
            $leaveType,
            $startDate,
            $endDate,
            'Family matter',
            'day',
        );

        $this->assertSame('submitted', $leaveRequest->status);
        $this->assertSame('pending', $leaveRequest->manager_status);
        $this->assertSame('pending', $leaveRequest->hr_status);

        $service->approveManager($leaveRequest, $manager);

        $leaveRequest->refresh();
        $this->assertSame('manager_approved', $leaveRequest->status);

        $service->approveHr($leaveRequest, $hr);

        $leaveRequest->refresh();
        $this->assertSame('approved', $leaveRequest->status);
        $this->assertNotNull($leaveRequest->approved_at);

        $allocation = LeaveAllocation::query()
            ->where('user_id', '=', $employee->id)
            ->where('leave_type_id', '=', $leaveType->id)
            ->where('year', '=', $year)
            ->firstOrFail();

        $this->assertEquals(2.0, (float) $leaveRequest->days);
        $this->assertEquals(2.0, (float) $allocation->used_days);

        $history = LeaveHistory::query()->where('reference_id', '=', $leaveRequest->id)->latest('id')->first();

        $this->assertNotNull($history);
        $this->assertSame('approve_deduct', $history->action);
        $this->assertEquals(-2.0, (float) $history->delta_days);
        $this->assertEquals(22.0, (float) $history->before_days);
        $this->assertEquals(20.0, (float) $history->after_days);
        $this->assertSame($hr->id, $history->actor_id);
    }
}
