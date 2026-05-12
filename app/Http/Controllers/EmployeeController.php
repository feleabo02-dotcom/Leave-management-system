<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['user', 'department', 'position'])->latest()->paginate(15);
        return view('erp.employees.index', compact('employees'));
    }

    public function create()
    {
        $departments = Department::all();
        $positions = Position::all();
        $managers = User::whereHas('roles', function($q) {
            $q->whereIn('slug', ['super_admin', 'admin', 'hr_manager']);
        })->get();

        return view('erp.employees.create', compact('departments', 'positions', 'managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'employee_code' => 'required|string|unique:employees',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'manager_id' => 'nullable|exists:users,id',
            'hire_date' => 'required|date',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make('password123'),
                'department_id' => $validated['department_id'],
                'manager_id' => $validated['manager_id'],
            ]);

            // Assign basic employee role
            $role = \App\Models\Role::where('slug', 'employee')->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }

            Employee::create([
                'employee_code' => $validated['employee_code'],
                'user_id' => $user->id,
                'department_id' => $validated['department_id'],
                'position_id' => $validated['position_id'],
                'manager_id' => $validated['manager_id'],
                'hire_date' => $validated['hire_date'],
                'status' => 'active',
            ]);
        });

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['user', 'department', 'position', 'manager', 'contracts', 'documents', 'histories.changer']);
        return view('erp.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::all();
        $positions = Position::all();
        $managers = User::whereHas('roles', function($q) {
            $q->whereIn('slug', ['super_admin', 'admin', 'hr_manager']);
        })->get();

        return view('erp.employees.edit', compact('employee', 'departments', 'positions', 'managers'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,probation,terminated,suspended',
        ]);

        $employee->update($validated);

        if ($employee->user) {
            $employee->user->update([
                'department_id' => $validated['department_id'],
                'manager_id' => $validated['manager_id'],
            ]);
        }

        return redirect()->route('employees.show', $employee)->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        // Soft delete or handle user account appropriately, e.g.:
        // $employee->user->delete();
        
        return redirect()->route('employees.index')->with('success', 'Employee removed successfully.');
    }

    public function orgChart()
    {
        // Get all departments with their manager and employees
        $departments = Department::with(['manager', 'employees.user', 'employees.position'])->get();
        
        // Group users by manager to build a tree
        $users = User::with(['department', 'roles'])->get();
        
        // Build hierarchy based on manager_id
        $hierarchy = $this->buildHierarchy($users);

        return view('erp.employees.org-chart', compact('departments', 'hierarchy'));
    }

    private function buildHierarchy($users, $parentId = null)
    {
        $branch = [];
        foreach ($users as $user) {
            if ($user->manager_id == $parentId) {
                $children = $this->buildHierarchy($users, $user->id);
                if ($children) {
                    $user->children = $children;
                }
                $branch[] = $user;
            }
        }
        return $branch;
    }
}
