<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $this->authorize('projects.read');
        $projects = Project::with(['manager'])->withCount('tasks')->latest()->paginate(20);
        return view('erp.projects.index', compact('projects'));
    }

    public function create()
    {
        $this->authorize('projects.create');
        $users = User::all();
        return view('erp.projects.create', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorize('projects.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'manager_id' => 'required|exists:users,id',
            'status' => 'required|in:draft,active,closed',
        ]);

        $validated['code'] = 'PRJ/' . strtoupper(substr($validated['name'], 0, 3)) . '/' . str_pad(Project::count() + 1, 3, '0', STR_PAD_LEFT);

        Project::create($validated);

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['tasks.assignee', 'manager']);
        $users = User::all();
        return view('erp.projects.show', compact('project', 'users'));
    }

    public function storeTask(Request $request, Project $project)
    {
        $this->authorize('projects.update');
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'assigned_to' => 'required|exists:users,id',
            'deadline' => 'nullable|date',
        ]);

        $project->tasks()->create($validated);

        return back()->with('success', 'Task added to project.');
    }

    public function logTime(Request $request, Task $task)
    {
        $this->authorize('projects.update');
        $validated = $request->validate([
            'hours' => 'required|numeric|min:0.1',
            'date' => 'required|date',
            'description' => 'required|string',
        ]);

        $validated['user_id'] = auth()->id();
        $task->timesheets()->create($validated);

        return back()->with('success', 'Hours logged.');
    }

    public function updateTaskStatus(Request $request, Task $task)
    {
        $this->authorize('projects.update');
        $request->validate(['status' => 'required|in:todo,progress,done,blocked']);

        $task->update(['status' => $request->status]);

        return back()->with('success', 'Task status updated.');
    }
}
