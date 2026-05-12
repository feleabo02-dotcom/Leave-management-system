<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobInterview;
use App\Models\JobPosition;
use App\Models\User;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index()
    {
        $this->authorize('recruitment.read');
        $applications = JobApplication::with(['position', 'reviewer', 'creator'])
            ->latest()
            ->paginate(20);
        $positions = JobPosition::where('status', 'open')->get();
        $reviewers = User::whereHas('roles', fn($q) => $q->whereIn('slug', ['admin', 'super_admin', 'manager', 'hr_manager']))->get();

        return view('erp.recruitment.applications', compact('applications', 'positions', 'reviewers'));
    }

    public function store(Request $request)
    {
        $this->authorize('recruitment.create');
        $validated = $request->validate([
            'job_position_id' => 'required|exists:job_positions,id',
            'candidate_name' => 'required|string|max:255',
            'candidate_email' => 'required|email|max:255',
            'candidate_phone' => 'nullable|string|max:20',
            'cover_letter' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        JobApplication::create($validated);

        return back()->with('success', 'Application recorded successfully.');
    }

    public function show(JobApplication $jobApplication)
    {
        $this->authorize('recruitment.read');
        $jobApplication->load(['position', 'reviewer', 'creator', 'interviews.interviewer']);

        return view('erp.recruitment.application-show', compact('jobApplication'));
    }

    public function stage(Request $request, JobApplication $jobApplication)
    {
        $this->authorize('recruitment.update');
        $request->validate(['status' => 'required|in:new,screening,interview,offered,hired,rejected']);

        $jobApplication->update(['status' => $request->status]);

        if ($request->status === 'hired') {
            $jobApplication->position()->update(['status' => 'filled']);
        }

        return back()->with('success', 'Application status updated to ' . ucfirst($request->status) . '.');
    }

    public function rate(Request $request, JobApplication $jobApplication)
    {
        $this->authorize('recruitment.update');
        $request->validate(['rating' => 'required|integer|min:1|max:5']);

        $jobApplication->update(['rating' => $request->rating, 'reviewer_id' => auth()->id()]);

        return back()->with('success', 'Rating submitted.');
    }

    public function scheduleInterview(Request $request, JobApplication $jobApplication)
    {
        $this->authorize('recruitment.create');
        $validated = $request->validate([
            'interviewer_id' => 'required|exists:users,id',
            'interview_date' => 'required|date',
            'interview_mode' => 'required|in:in-person,video,phone',
            'notes' => 'nullable|string',
        ]);

        $validated['job_application_id'] = $jobApplication->id;
        JobInterview::create($validated);

        if ($jobApplication->status === 'new') {
            $jobApplication->update(['status' => 'screening']);
        }

        return back()->with('success', 'Interview scheduled.');
    }

    public function updateInterview(Request $request, JobInterview $jobInterview)
    {
        $this->authorize('recruitment.update');
        $validated = $request->validate([
            'status' => 'required|in:scheduled,completed,cancelled',
            'feedback' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $jobInterview->update($validated);

        return back()->with('success', 'Interview updated.');
    }
}
