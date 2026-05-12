<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = Hash::make('password123');
        $employeeRole = Role::where('slug', 'employee')->first();
        $managerRole = Role::where('slug', 'manager')->first();

        // 1. Departments
        $depts = [
            ['name' => 'Engineering', 'code' => 'ENG'],
            ['name' => 'Marketing', 'code' => 'MKT'],
            ['name' => 'Sales', 'code' => 'SAL'],
            ['name' => 'Finance', 'code' => 'FIN'],
            ['name' => 'Customer Success', 'code' => 'CS'],
        ];

        foreach ($depts as $dept) {
            Department::updateOrCreate(['code' => $dept['code']], $dept);
        }

        $engDept = Department::where('code', 'ENG')->first();
        $mktDept = Department::where('code', 'MKT')->first();
        $salDept = Department::where('code', 'SAL')->first();

        // 2. Positions
        $positions = [
            ['title' => 'Software Engineer', 'department_id' => $engDept->id, 'level' => 5],
            ['title' => 'Product Manager', 'department_id' => $engDept->id, 'level' => 7],
            ['title' => 'UX Designer', 'department_id' => $engDept->id, 'level' => 6],
            ['title' => 'Marketing Specialist', 'department_id' => $mktDept->id, 'level' => 4],
            ['title' => 'Account Executive', 'department_id' => $salDept->id, 'level' => 6],
        ];

        foreach ($positions as $pos) {
            Position::updateOrCreate(['title' => $pos['title']], $pos);
        }

        // 3. Realistic Employees
        $people = [
            [
                'name' => 'Alex Rivera',
                'email' => 'alex.rivera@xobiyahr.com',
                'role' => 'manager',
                'dept' => 'ENG',
                'pos' => 'Product Manager',
                'code' => 'EMP001',
            ],
            [
                'name' => 'Jordan Smith',
                'email' => 'jordan.smith@xobiyahr.com',
                'role' => 'employee',
                'dept' => 'ENG',
                'pos' => 'Software Engineer',
                'code' => 'EMP002',
                'manager_email' => 'alex.rivera@xobiyahr.com'
            ],
            [
                'name' => 'Elena Rodriguez',
                'email' => 'elena.r@xobiyahr.com',
                'role' => 'employee',
                'dept' => 'ENG',
                'pos' => 'UX Designer',
                'code' => 'EMP003',
                'manager_email' => 'alex.rivera@xobiyahr.com'
            ],
            [
                'name' => 'Marcus Chen',
                'email' => 'marcus.chen@xobiyahr.com',
                'role' => 'manager',
                'dept' => 'SAL',
                'pos' => 'Account Executive',
                'code' => 'EMP004',
            ],
            [
                'name' => 'Sophie Dupont',
                'email' => 'sophie.d@xobiyahr.com',
                'role' => 'employee',
                'dept' => 'MKT',
                'pos' => 'Marketing Specialist',
                'code' => 'EMP005',
            ],
        ];

        foreach ($people as $person) {
            $user = User::updateOrCreate(['email' => $person['email']], [
                'name' => $person['name'],
                'password' => $defaultPassword,
                'email_verified_at' => now(),
                'status' => 'active',
            ]);

            // Assign Role
            $role = $person['role'] === 'manager' ? $managerRole : $employeeRole;
            $user->roles()->sync([$role->id]);

            // Create Employee Record
            $dept = Department::where('code', $person['dept'])->first();
            $pos = Position::where('title', $person['pos'])->first();

            $employee = Employee::updateOrCreate(['employee_code' => $person['code']], [
                'user_id' => $user->id,
                'department_id' => $dept->id,
                'position_id' => $pos->id,
                'hire_date' => Carbon::now()->subMonths(rand(6, 24)),
                'status' => 'active',
                'gender' => rand(0, 1) ? 'male' : 'female',
                'dob' => Carbon::now()->subYears(rand(22, 45)),
            ]);
        }

        // 4. Link Managers (Post-creation)
        foreach ($people as $person) {
            if (isset($person['manager_email'])) {
                $mgrUser = User::where('email', $person['manager_email'])->first();
                $empUser = User::where('email', $person['email'])->first();
                
                if ($mgrUser && $empUser) {
                    $empUser->update(['manager_id' => $mgrUser->id]);
                    Employee::where('user_id', $empUser->id)->update(['manager_id' => $mgrUser->id]);
                }
            }
        }
    }
}
