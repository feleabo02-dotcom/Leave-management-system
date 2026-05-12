<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use App\Models\Shift;
use App\Models\SalaryStructure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['user', 'department', 'position']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $employees = $query->latest()->paginate(15);
        $departments = Department::all();
        return view('erp.employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        $departments = Department::all();
        $positions = Position::all();
        $managers = User::whereHas('roles', function($q) {
            $q->whereIn('slug', ['super_admin', 'admin', 'hr_manager']);
        })->get();
        $shifts = Shift::where('is_active', true)->get();
        $salaryStructures = SalaryStructure::where('is_active', true)->get();

        return view('erp.employees.create', compact('departments', 'positions', 'managers', 'shifts', 'salaryStructures'));
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
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'shift_id' => 'nullable|exists:shifts,id',
            'salary_structure_id' => 'nullable|exists:salary_structures,id',
            'emergency_contact' => 'nullable|json',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make('password123'),
                'department_id' => $validated['department_id'],
                'manager_id' => $validated['manager_id'],
            ]);

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
                'dob' => $validated['dob'],
                'gender' => $validated['gender'],
                'shift_id' => $validated['shift_id'],
                'salary_structure_id' => $validated['salary_structure_id'],
                'emergency_contact' => $validated['emergency_contact'] ? json_decode($validated['emergency_contact'], true) : null,
                'status' => 'active',
            ]);
        });

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load([
            'user', 'department', 'position', 'manager', 
            'contracts', 'documents', 'histories.changer',
            'skills.skill', 'skills.skillLevel', 'resumeLines.lineType', 'shift', 'salaryStructure',
            'user.leaveAllocations.leaveType', 'user.leaveRequests.leaveType',
        ]);
        return view('erp.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::all();
        $positions = Position::all();
        $managers = User::whereHas('roles', function($q) {
            $q->whereIn('slug', ['super_admin', 'admin', 'hr_manager']);
        })->get();
        $shifts = Shift::where('is_active', true)->get();
        $salaryStructures = SalaryStructure::where('is_active', true)->get();

        return view('erp.employees.edit', compact('employee', 'departments', 'positions', 'managers', 'shifts', 'salaryStructures'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,probation,terminated,suspended',
            'shift_id' => 'nullable|exists:shifts,id',
            'salary_structure_id' => 'nullable|exists:salary_structures,id',
            'emergency_contact' => 'nullable|json',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
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
        
        return redirect()->route('employees.index')->with('success', 'Employee removed successfully.');
    }

    public function orgChart()
    {
        $departments = Department::with(['manager', 'employees.user', 'employees.position'])->get();
        
        $users = User::with(['department', 'roles'])->get();
        
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
