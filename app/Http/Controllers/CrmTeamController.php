<?php

namespace App\Http\Controllers;

use App\Models\CrmTeam;
use App\Models\User;
use Illuminate\Http\Request;

class CrmTeamController extends Controller
{
    public function index()
    {
        $this->authorize('crm.read');
        $teams = CrmTeam::with('leader')->get();
        $users = User::all();
        return view('erp.crm.teams', compact('teams', 'users'));
    }

    public function store(Request $request)
    {
        $this->authorize('crm.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'leader_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean',
        ]);

        CrmTeam::create($validated);

        return back()->with('success', 'Team created successfully.');
    }

    public function destroy(CrmTeam $crmTeam)
    {
        $this->authorize('crm.delete');
        $crmTeam->delete();

        return back()->with('success', 'Team deleted successfully.');
    }
}
