<?php

namespace App\Http\Controllers;

use App\Models\JobPosition;
use App\Models\User;
use Illuminate\Http\Request;

class RecruitmentController extends Controller
{
    public function index()
    {
        $this->authorize('recruitment.read');
        $positions = JobPosition::with(['hiringManager', 'creator'])
            ->withCount('applications')
            ->latest()
            ->paginate(20);
        $hiringManagers = User::whereHas('roles', fn($q) => $q->whereIn('slug', ['admin', 'super_admin', 'manager', 'hr_manager']))->get();

        return view('erp.recruitment.index', compact('positions', 'hiringManagers'));
    }

    public function store(Request $request)
    {
        $this->authorize('recruitment.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'status' => 'required|in:draft,open,closed,filled',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'hiring_manager_id' => 'nullable|exists:users,id',
        ]);

        $validated['created_by'] = auth()->id();
        JobPosition::create($validated);

        return back()->with('success', 'Job position created successfully.');
    }

    public function show(JobPosition $jobPosition)
    {
        $this->authorize('recruitment.read');
        $jobPosition->load(['hiringManager', 'creator', 'applications' => function ($q) {
            $q->latest();
        }, 'applications.reviewer', 'applications.interviews']);

        return view('erp.recruitment.show', compact('jobPosition'));
    }

    public function update(Request $request, JobPosition $jobPosition)
    {
        $this->authorize('recruitment.update');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'status' => 'required|in:draft,open,closed,filled',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'hiring_manager_id' => 'nullable|exists:users,id',
        ]);

        $jobPosition->update($validated);

        return back()->with('success', 'Job position updated successfully.');
    }

    public function destroy(JobPosition $jobPosition)
    {
        $this->authorize('recruitment.delete');
        $jobPosition->delete();

        return redirect()->route('recruitment.index')->with('success', 'Job position deleted.');
    }
}
