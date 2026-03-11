<?php

namespace Tests\Feature;

use App\Models\LeavePolicy;
use App\Models\LeaveType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeavePolicyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_activate_leave_policy_via_api(): void
    {
        $adminRole = Role::query()->create(['name' => 'Admin', 'slug' => 'admin']);

        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->roles()->sync([$adminRole->id]);

        $leaveType = LeaveType::query()->create([
            'name' => 'Annual Leave',
            'code' => 'ANNUAL',
            'is_paid' => true,
            'requires_manager_approval' => true,
            'requires_hr_approval' => true,
            'carry_forward' => true,
            'carry_forward_cap' => 5,
            'active' => true,
            'allocation_type' => 'fixed',
            'validation_type' => 'both',
            'request_unit' => 'day',
            'allow_half_day' => true,
            'allow_hour' => false,
            'accrual_rate' => 0,
            'accrual_cap' => null,
            'requires_allocation' => true,
        ]);

        $oldPolicy = LeavePolicy::query()->create([
            'leave_type_id' => $leaveType->id,
            'version' => 1,
            'min_service_months' => 0,
            'max_days_per_year' => 15,
            'max_unpaid_days_per_year' => null,
            'allow_backdate' => false,
            'allow_future_apply_days' => 365,
            'yearly_reset' => true,
            'expiry_days' => null,
            'carry_forward_limit' => 3,
            'effective_from' => now()->subYear()->startOfYear()->toDateString(),
            'effective_to' => null,
            'is_active' => true,
        ]);

        $createResponse = $this->actingAs($admin)->postJson('/api/v1/leave/policies', [
            'leave_type_id' => $leaveType->id,
            'min_service_months' => 11,
            'max_days_per_year' => 20,
            'max_unpaid_days_per_year' => null,
            'allow_backdate' => false,
            'allow_future_apply_days' => 365,
            'yearly_reset' => true,
            'expiry_days' => null,
            'carry_forward_limit' => 5,
            'effective_from' => now()->startOfYear()->toDateString(),
            'effective_to' => null,
            'is_active' => false,
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('message', 'Leave policy created.')
            ->assertJsonPath('data.version', 2)
            ->assertJsonPath('data.is_active', false);

        $newPolicyId = (int) $createResponse->json('data.id');

        $this->assertDatabaseHas('leave_policies', [
            'id' => $newPolicyId,
            'leave_type_id' => $leaveType->id,
            'version' => 2,
            'is_active' => false,
        ]);

        $activateResponse = $this->actingAs($admin)
            ->putJson("/api/v1/leave/policies/{$newPolicyId}/activate");

        $activateResponse
            ->assertOk()
            ->assertJsonPath('message', 'Leave policy activated.')
            ->assertJsonPath('data.id', $newPolicyId)
            ->assertJsonPath('data.is_active', true);

        $this->assertDatabaseHas('leave_policies', [
            'id' => $oldPolicy->id,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('leave_policies', [
            'id' => $newPolicyId,
            'is_active' => true,
        ]);
    }

    public function test_non_admin_cannot_manage_leave_policies_via_api(): void
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

        $response = $this->actingAs($employee)->postJson('/api/v1/leave/policies', [
            'leave_type_id' => $leaveType->id,
            'min_service_months' => 0,
            'allow_backdate' => false,
            'yearly_reset' => true,
            'effective_from' => now()->startOfYear()->toDateString(),
            'is_active' => true,
        ]);

        $response->assertForbidden();
    }
}
