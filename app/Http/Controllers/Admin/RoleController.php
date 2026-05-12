<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $this->authorize('super_admin');
        $roles = Role::withCount('users', 'permissions')->get();
        return view('erp.admin.roles.index', compact('roles'));
    }

    public function editPermissions(Role $role)
    {
        $this->authorize('super_admin');
        
        $role->load('permissions');
        $permissions = Permission::all()->groupBy('module');
        
        return view('erp.admin.roles.permissions', compact('role', 'permissions'));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $this->authorize('super_admin');
        
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', "Permissions for role '{$role->name}' updated successfully.");
    }
}
