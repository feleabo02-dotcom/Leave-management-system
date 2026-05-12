<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeSkill;
use App\Models\ResumeLine;
use App\Models\ResumeLineType;
use App\Models\Skill;
use App\Models\SkillLevel;
use App\Models\SkillType;
use Illuminate\Http\Request;

class SkillsController extends Controller
{
    public function index()
    {
        $this->authorize('skills.read');
        $skillTypes = SkillType::with(['skills', 'levels'])->get();
        $skills = Skill::with('skillType')->get();

        return view('erp.skills.index', compact('skillTypes', 'skills'));
    }

    public function storeSkillType(Request $request)
    {
        $this->authorize('skills.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'active' => 'nullable|boolean',
            'sequence' => 'nullable|integer',
            'color' => 'nullable|string|max:50',
            'is_certification' => 'nullable|boolean',
        ]);

        SkillType::create($validated);

        return back()->with('success', 'Skill type created successfully.');
    }

    public function storeSkill(Request $request)
    {
        $this->authorize('skills.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'skill_type_id' => 'required|exists:skill_types,id',
            'sequence' => 'nullable|integer',
            'color' => 'nullable|string|max:50',
        ]);

        Skill::create($validated);

        return back()->with('success', 'Skill created successfully.');
    }

    public function destroySkillType(SkillType $skillType)
    {
        $this->authorize('skills.delete');

        if ($skillType->skills()->count() > 0) {
            return back()->with('error', 'Cannot delete skill type with linked skills.');
        }

        $skillType->delete();

        return back()->with('success', 'Skill type deleted successfully.');
    }

    public function destroySkill(Skill $skill)
    {
        $this->authorize('skills.delete');
        $skill->delete();

        return back()->with('success', 'Skill deleted successfully.');
    }

    public function employeeSkills()
    {
        $this->authorize('skills.read');
        $employees = Employee::with(['user', 'skills'])->get();
        $skillTypes = SkillType::with('levels')->get();

        return view('erp.skills.employees', compact('employees', 'skillTypes'));
    }

    public function storeEmployeeSkill(Request $request)
    {
        $this->authorize('skills.create');
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'skill_id' => 'required|exists:skills,id',
            'skill_level_id' => 'nullable|exists:skill_levels,id',
            'skill_type_id' => 'required|exists:skill_types,id',
        ]);

        EmployeeSkill::create($validated);

        return back()->with('success', 'Employee skill assigned successfully.');
    }

    public function destroyEmployeeSkill(EmployeeSkill $employeeSkill)
    {
        $this->authorize('skills.delete');
        $employeeSkill->delete();

        return back()->with('success', 'Employee skill removed successfully.');
    }

    public function resumeLines()
    {
        $this->authorize('skills.read');
        $lineTypes = ResumeLineType::all();
        $resumeLines = ResumeLine::with(['employee.user', 'lineType'])->latest()->get();

        return view('erp.skills.resume-lines', compact('lineTypes', 'resumeLines'));
    }

    public function storeResumeLine(Request $request)
    {
        $this->authorize('skills.create');
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'name' => 'required|string|max:255',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date',
            'description' => 'nullable|string',
            'line_type_id' => 'required|exists:resume_line_types,id',
        ]);

        ResumeLine::create($validated);

        return back()->with('success', 'Resume line created successfully.');
    }

    public function destroyResumeLine(ResumeLine $resumeLine)
    {
        $this->authorize('skills.delete');
        $resumeLine->delete();

        return back()->with('success', 'Resume line deleted successfully.');
    }
}
