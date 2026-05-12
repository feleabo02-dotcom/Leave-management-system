<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\AuditService;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount('branches', 'users')->latest()->paginate(15);
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'slug'     => 'required|string|max:100|unique:companies',
            'address'  => 'nullable|string',
            'phone'    => 'nullable|string|max:30',
            'email'    => 'nullable|email',
            'website'  => 'nullable|url',
            'currency' => 'required|string|max:10',
            'timezone' => 'required|string|max:50',
            'logo'     => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('company-logos', 'public');
        }

        $data['created_by'] = auth()->id();
        $company = Company::create($data);

        AuditService::log('created', $company, [], $company->toArray());

        return redirect()->route('companies.index')->with('success', 'Company created successfully.');
    }

    public function show(Company $company)
    {
        $company->loadCount('branches', 'users');
        return view('companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'slug'     => 'required|string|max:100|unique:companies,slug,' . $company->id,
            'address'  => 'nullable|string',
            'phone'    => 'nullable|string|max:30',
            'email'    => 'nullable|email',
            'website'  => 'nullable|url',
            'currency' => 'required|string|max:10',
            'timezone' => 'required|string|max:50',
            'logo'     => 'nullable|image|max:2048',
        ]);

        $old = $company->toArray();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('company-logos', 'public');
        }

        $data['updated_by'] = auth()->id();
        $company->update($data);

        AuditService::log('updated', $company, $old, $company->fresh()->toArray());

        return redirect()->route('companies.show', $company)->with('success', 'Company updated.');
    }

    public function destroy(Company $company)
    {
        AuditService::log('deleted', $company, $company->toArray(), []);
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'Company deleted.');
    }
}
