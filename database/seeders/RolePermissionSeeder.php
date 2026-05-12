<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * All ERP permissions in dot-notation: module.action
     */
    protected array $permissions = [
        // Employees
        'employees.create', 'employees.read', 'employees.update', 'employees.delete',
        'employees.approve', 'employees.export',
        // Attendance
        'attendance.create', 'attendance.read', 'attendance.update', 'attendance.delete',
        'attendance.approve', 'attendance.export',
        // Leave
        'leave.create', 'leave.read', 'leave.update', 'leave.delete',
        'leave.approve', 'leave.export',
        // Payroll
        'payroll.create', 'payroll.read', 'payroll.update', 'payroll.delete',
        'payroll.approve', 'payroll.export',
        // Assets
        'assets.create', 'assets.read', 'assets.update', 'assets.delete',
        'assets.approve', 'assets.export',
        // Inventory
        'inventory.create', 'inventory.read', 'inventory.update', 'inventory.delete',
        'inventory.approve', 'inventory.export',
        // Procurement
        'procurement.create', 'procurement.read', 'procurement.update', 'procurement.delete',
        'procurement.approve', 'procurement.export',
        // CRM
        'crm.create', 'crm.read', 'crm.update', 'crm.delete',
        'crm.approve', 'crm.export',
        // Sales
        'sales.create', 'sales.read', 'sales.update', 'sales.delete',
        'sales.approve', 'sales.export',
        // Accounting
        'accounting.create', 'accounting.read', 'accounting.update', 'accounting.delete',
        'accounting.approve', 'accounting.export',
        // Manufacturing
        'manufacturing.create', 'manufacturing.read', 'manufacturing.update', 'manufacturing.delete',
        'manufacturing.approve', 'manufacturing.export',
        // Projects
        'projects.create', 'projects.read', 'projects.update', 'projects.delete',
        'projects.approve', 'projects.export',
        // Helpdesk
        'helpdesk.create', 'helpdesk.read', 'helpdesk.update', 'helpdesk.delete',
        'helpdesk.approve', 'helpdesk.export',
        // Reports
        'reports.read', 'reports.export',
        // Settings / Companies
        'companies.create', 'companies.read', 'companies.update', 'companies.delete',
        'settings.read', 'settings.update',
        // Users / Roles
        'users.create', 'users.read', 'users.update', 'users.delete',
        'roles.create', 'roles.read', 'roles.update', 'roles.delete',
    ];

    /**
     * Role definitions with their permission sets.
     */
    protected array $roles = [
        [
            'name' => 'Super Admin',
            'slug' => 'super_admin',
            'permissions' => '*', // all
        ],
        [
            'name' => 'Admin',
            'slug' => 'admin',
            'permissions' => '*',
        ],
        [
            'name' => 'HR Manager',
            'slug' => 'hr_manager',
            'permissions' => [
                'employees.*', 'attendance.*', 'leave.*', 'payroll.*',
                'reports.read', 'reports.export',
            ],
        ],
        [
            'name' => 'HR Officer',
            'slug' => 'hr_officer',
            'permissions' => [
                'employees.read', 'employees.create', 'employees.update',
                'attendance.read', 'attendance.create', 'attendance.update',
                'leave.read', 'leave.create', 'leave.update', 'leave.approve',
            ],
        ],
        [
            'name' => 'Accountant',
            'slug' => 'accountant',
            'permissions' => [
                'accounting.*', 'payroll.read', 'payroll.export',
                'sales.read', 'procurement.read', 'reports.read', 'reports.export',
            ],
        ],
        [
            'name' => 'Procurement Officer',
            'slug' => 'procurement_officer',
            'permissions' => [
                'procurement.*', 'inventory.read', 'inventory.create',
                'reports.read',
            ],
        ],
        [
            'name' => 'Warehouse Manager',
            'slug' => 'warehouse_manager',
            'permissions' => [
                'inventory.*', 'reports.read', 'reports.export',
            ],
        ],
        [
            'name' => 'Sales Manager',
            'slug' => 'sales_manager',
            'permissions' => [
                'sales.*', 'crm.*', 'inventory.read', 'reports.read', 'reports.export',
            ],
        ],
        [
            'name' => 'Project Manager',
            'slug' => 'project_manager',
            'permissions' => [
                'projects.*', 'helpdesk.read', 'reports.read',
            ],
        ],
        [
            'name' => 'Manager',
            'slug' => 'manager',
            'permissions' => [
                'leave.read', 'leave.approve',
                'attendance.read',
                'projects.read', 'reports.read',
            ],
        ],
        [
            'name' => 'Employee',
            'slug' => 'employee',
            'permissions' => [
                'leave.create', 'leave.read',
                'attendance.read',
                'projects.read', 'helpdesk.create', 'helpdesk.read',
            ],
        ],
    ];

    public function run(): void
    {
        // ── 1. Upsert all permissions ─────────────────────────────────────
        $permissionMap = [];
        foreach ($this->permissions as $slug) {
            [$module, $action] = explode('.', $slug);
            $perm = Permission::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'   => ucfirst($action) . ' ' . ucfirst($module),
                    'module' => $module,
                    'action' => $action,
                ]
            );
            $permissionMap[$slug] = $perm->id;
        }

        // ── 2. Upsert roles and attach permissions ────────────────────────
        $allPermissionIds = array_values($permissionMap);

        foreach ($this->roles as $roleDef) {
            $role = Role::updateOrCreate(
                ['slug' => $roleDef['slug']],
                ['name' => $roleDef['name']]
            );

            if ($roleDef['permissions'] === '*') {
                $role->permissions()->sync($allPermissionIds);
            } else {
                $ids = [];
                foreach ($roleDef['permissions'] as $pattern) {
                    if (str_ends_with($pattern, '.*')) {
                        $prefix = str_replace('.*', '.', $pattern);
                        foreach ($permissionMap as $slug => $id) {
                            if (str_starts_with($slug, $prefix)) {
                                $ids[] = $id;
                            }
                        }
                    } elseif (isset($permissionMap[$pattern])) {
                        $ids[] = $permissionMap[$pattern];
                    }
                }
                $role->permissions()->sync(array_unique($ids));
            }
        }

        $this->command->info('✅ Roles and permissions seeded successfully.');
    }
}
