<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use Illuminate\Http\Request;

class MilestoneController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('projects.create');
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'deadline' => 'required|date',
        ]);

        Milestone::create($validated);

        return back()->with('success', 'Milestone created successfully.');
    }

    public function updateProgress(Request $request, Milestone $milestone)
    {
        $this->authorize('projects.update');
        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $milestone->update(['progress' => $validated['progress']]);

        return back()->with('success', 'Progress updated successfully.');
    }
}
