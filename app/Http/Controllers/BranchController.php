<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use App\Services\AuditService;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function store(Request $request, Company $company)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone'   => 'nullable|string|max:30',
            'email'   => 'nullable|email',
        ]);

        $data['company_id'] = $company->id;
        $data['created_by'] = auth()->id();
        $branch = Branch::create($data);

        AuditService::log('created', $branch, [], $branch->toArray());

        return back()->with('success', 'Branch created.');
    }

    public function update(Request $request, Branch $branch)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone'   => 'nullable|string|max:30',
            'email'   => 'nullable|email',
        ]);

        $old = $branch->toArray();
        $data['updated_by'] = auth()->id();
        $branch->update($data);

        AuditService::log('updated', $branch, $old, $branch->fresh()->toArray());

        return back()->with('success', 'Branch updated.');
    }

    public function destroy(Branch $branch)
    {
        AuditService::log('deleted', $branch, $branch->toArray(), []);
        $branch->delete();
        return back()->with('success', 'Branch deleted.');
    }
}
