<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::with('department')->latest()->paginate(15);
        return view('erp.positions.index', compact('positions'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('erp.positions.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'level' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        Position::create($validated);

        return redirect()->route('positions.index')->with('success', 'Position created successfully.');
    }

    public function show(Position $position)
    {
        $position->load('department', 'employees.user');
        return view('erp.positions.show', compact('position'));
    }

    public function edit(Position $position)
    {
        $departments = Department::all();
        return view('erp.positions.edit', compact('position', 'departments'));
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'level' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $position->update($validated);

        return redirect()->route('positions.index')->with('success', 'Position updated successfully.');
    }

    public function destroy(Position $position)
    {
        $position->delete();
        return redirect()->route('positions.index')->with('success', 'Position removed successfully.');
    }
}
