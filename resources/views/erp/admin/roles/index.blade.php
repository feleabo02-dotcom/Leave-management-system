@extends('components.layouts.erp')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Security Groups</h1>
        <p class="text-xs text-slate-500 font-medium uppercase tracking-widest mt-1">Manage Roles & System Access Control</p>
    </div>
    <div class="flex items-center gap-3">
        <div class="px-4 py-2 bg-indigo-50 border border-indigo-100 rounded-xl flex items-center gap-3">
            <i class="ph-bold ph-shield-check text-indigo-600"></i>
            <span class="text-xs font-bold text-indigo-900">{{ $roles->count() }} Defined Roles</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($roles as $role)
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition group">
            <div class="p-6 border-b border-slate-50">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                        <i class="ph-bold ph-user-circle-plus text-2xl"></i>
                    </div>
                    <span class="text-[10px] font-black px-2 py-1 bg-slate-100 text-slate-500 rounded uppercase tracking-tighter">
                        {{ $role->users_count }} Users
                    </span>
                </div>
                
                <h3 class="text-lg font-black text-slate-900 mb-1 capitalize">{{ str_replace('_', ' ', $role->name) }}</h3>
                <p class="text-xs text-slate-500 font-medium line-clamp-2 h-8">{{ $role->description ?? 'No description provided for this security group.' }}</p>
            </div>
            
            <div class="px-6 py-4 bg-slate-50/50 flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Permissions</span>
                    <span class="text-sm font-black text-slate-900">{{ $role->permissions_count }}</span>
                </div>
                
                <a href="{{ route('admin.roles.permissions', $role) }}" 
                   class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition shadow-sm">
                    Manage Access
                </a>
            </div>
        </div>
    @endforeach
</div>
@endsection
