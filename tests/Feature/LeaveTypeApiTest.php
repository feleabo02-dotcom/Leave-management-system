<?php

namespace Tests\Feature;

use App\Models\LeaveType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveTypeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_create_and_update_leave_types_via_api(): void
    {
        $adminRole = Role::query()->create(['name' => 'Admin', 'slug' => 'admin']);

        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->roles()->sync([$adminRole->id]);

        $existing = LeaveType::query()->create([
            'name' => 'Annual Leave',
            'code' => 'ANNUAL',
            'is_paid' => true,
            'requires_manager_approval' => true,
            'requires_hr_approval' => true,
            'carry_forward' => true,
            'carry_forward_cap' => 5,
            'max_days_per_request' => 10,
            'active' => true,
            'allocation_type' => 'accrual',
            'validation_type' => 'both',
            'request_unit' => 'day',
            'allow_half_day' => true,
            'allow_hour' => false,
            'accrual_rate' => 1.67,
            'accrual_cap' => 20,
            'requires_allocation' => false,
        ]);

        $listResponse = $this->actingAs($admin)->getJson('/api/v1/leave/types');

        $listResponse
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $existing->id);

        $createResponse = $this->actingAs($admin)->postJson('/api/v1/leave/types', [
            'name' => 'Study Leave',
            'code' => 'study',
            'is_paid' => false,
            'validation_type' => 'manager',
            'carry_forward_cap' => 0,
            'allocation_type' => 'fixed',
            'request_unit' => 'day',
            'allow_half_day' => false,
            'allow_hour' => false,
            'accrual_rate' => 0,
            'accrual_cap' => null,
            'active' => true,
            'max_days_per_request' => 15,
            'color' => '#123456',
        ]);

        $createResponse
            ->assertCreated()
            ->assertJsonPath('message', 'Leave type created.')
            ->assertJsonPath('data.code', 'STUDY')
            ->assertJsonPath('data.requires_manager_approval', true)
            ->assertJsonPath('data.requires_hr_approval', false)
            ->assertJsonPath('data.requires_allocation', true);

        $newTypeId = (int) $createResponse->json('data.id');

        $this->assertDatabaseHas('leave_types', [
            'id' => $newTypeId,
            'name' => 'Study Leave',
            'code' => 'STUDY',
            'is_paid' => 0,
            'requires_manager_approval' => 1,
            'requires_hr_approval' => 0,
        ]);

        $updateResponse = $this->actingAs($admin)->putJson("/api/v1/leave/types/{$newTypeId}", [
            'name' => 'Study & Research Leave',
            'code' => 'study',
            'is_paid' => true,
            'validation_type' => 'both',
            'carry_forward_cap' => 4,
            'allocation_type' => 'accrual',
            'request_unit' => 'half_day',
            'allow_half_day' => true,
            'allow_hour' => false,
            'accrual_rate' => 0.5,
            'accrual_cap' => 10,
            'active' => true,
            'max_days_per_request' => 20,
            'color' => '#abcdef',
        ]);

        $updateResponse
            ->assertOk()
            ->assertJsonPath('message', 'Leave type updated.')
            ->assertJsonPath('data.name', 'Study & Research Leave')
            ->assertJsonPath('data.validation_type', 'both')
            ->assertJsonPath('data.carry_forward_cap', 4)
            ->assertJsonPath('data.requires_hr_approval', true)
            ->assertJsonPath('data.requires_allocation', false);

        $this->assertDatabaseHas('leave_types', [
            'id' => $newTypeId,
            'name' => 'Study & Research Leave',
            'code' => 'STUDY',
            'validation_type' => 'both',
            'carry_forward_cap' => 4,
            'requires_hr_approval' => 1,
        ]);
    }

    public function test_non_admin_cannot_manage_leave_types_via_api(): void
    {
        $employeeRole = Role::query()->create(['name' => 'Employee', 'slug' => 'employee']);

        /** @var User $employee */
        $employee = User::factory()->create();
        $employee->roles()->sync([$employeeRole->id]);

        $response = $this->actingAs($employee)->postJson('/api/v1/leave/types', [
            'name' => 'Special Leave',
            'code' => 'SPECIAL',
            'is_paid' => true,
            'validation_type' => 'manager',
            'carry_forward_cap' => 0,
            'allocation_type' => 'fixed',
            'request_unit' => 'day',
            'allow_half_day' => false,
            'allow_hour' => false,
            'accrual_rate' => 0,
            'accrual_cap' => null,
        ]);

        $response->assertForbidden();
    }
}
