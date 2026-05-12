<?php

namespace App\Http\Controllers;

use App\Models\CrmStage;
use Illuminate\Http\Request;

class CrmStageController extends Controller
{
    public function index()
    {
        $this->authorize('crm.read');
        $stages = CrmStage::orderBy('sequence')->get();
        return view('erp.crm.stages', compact('stages'));
    }

    public function store(Request $request)
    {
        $this->authorize('crm.create');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sequence' => 'nullable|integer',
            'probability' => 'nullable|integer|min:0|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        CrmStage::create($validated);

        return back()->with('success', 'Stage created successfully.');
    }

    public function destroy(CrmStage $crmStage)
    {
        $this->authorize('crm.delete');
        $crmStage->delete();

        return back()->with('success', 'Stage deleted successfully.');
    }
}
