@extends('components.layouts.erp')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.roles.index') }}" class="p-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-400 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Access Control Matrix</h1>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-widest mt-1">Role: <span class="text-indigo-600">{{ $role->name }}</span></p>
        </div>
    </div>
</div>

<form action="{{ route('admin.roles.permissions.update', $role) }}" method="POST">
    @csrf
    
    <div class="space-y-8">
        @foreach($permissions as $module => $group)
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-black text-slate-900 text-sm uppercase tracking-wider">{{ $module }} Module</h3>
                    <button type="button" 
                            @click="document.querySelectorAll('.check-{{ $module }}').forEach(el => el.checked = true)"
                            class="text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:underline">
                        Select All
                    </button>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($group as $permission)
                            <label class="relative flex items-center gap-3 p-4 border border-slate-100 rounded-xl hover:bg-slate-50 cursor-pointer group transition">
                                <div class="flex items-center justify-center">
                                    <input type="checkbox" 
                                           name="permissions[]" 
                                           value="{{ $permission->id }}"
                                           class="check-{{ $module }} w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600/20"
                                           {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-slate-900 leading-none mb-1">{{ strtoupper(explode('.', $permission->slug)[1] ?? $permission->name) }}</p>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">{{ $permission->slug }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="sticky bottom-8 mt-12 bg-white/80 backdrop-blur-md border border-slate-200 p-4 rounded-2xl shadow-2xl flex items-center justify-between z-50">
        <div class="flex items-center gap-4 text-xs font-bold text-slate-500">
            <i class="ph-bold ph-info text-indigo-500 text-lg"></i>
            Changes will take effect immediately for all users in this security group.
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.roles.index') }}" class="px-6 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700 transition">Cancel</a>
            <button type="submit" class="px-8 py-2.5 bg-indigo-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                Save Permissions
            </button>
        </div>
    </div>
</form>
@endsection
