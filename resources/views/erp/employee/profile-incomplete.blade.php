@extends('components.layouts.erp')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[70vh] p-6 text-center">
    <div class="w-24 h-24 bg-amber-50 rounded-full flex items-center justify-center mb-6 border border-amber-100 shadow-sm">
        <i class="ph-bold ph-user-focus text-4xl text-amber-500"></i>
    </div>
    
    <h1 class="text-2xl font-black text-slate-900 mb-2 tracking-tight">Profile Setup Required</h1>
    <p class="text-slate-500 max-w-md mx-auto mb-8 leading-relaxed">
        Your user account has the <span class="font-bold text-slate-700 underline decoration-amber-300">Employee</span> role, but no linked employee record was found in the HR database.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl w-full">
        <div class="p-6 bg-white border border-slate-200 rounded-2xl text-left shadow-sm">
            <h3 class="font-bold text-slate-900 mb-1 flex items-center gap-2">
                <i class="ph ph-shield-check text-indigo-500"></i> Why am I seeing this?
            </h3>
            <p class="text-xs text-slate-500">
                To access the ERP modules, your account must be registered in the personnel directory with valid department and position data.
            </p>
        </div>
        
        <div class="p-6 bg-white border border-slate-200 rounded-2xl text-left shadow-sm">
            <h3 class="font-bold text-slate-900 mb-1 flex items-center gap-2">
                <i class="ph ph-info text-emerald-500"></i> What should I do?
            </h3>
            <p class="text-xs text-slate-500">
                Please contact your HR Manager or System Administrator to link your user profile to an employee ID.
            </p>
        </div>
    </div>

    <div class="mt-10 flex items-center gap-4">
        <a href="{{ route('logout') }}" 
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="px-6 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition shadow-lg shadow-slate-200">
            Sign Out
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
        <button onclick="window.location.reload()" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition underline underline-offset-4">
            Try Again
        </button>
    </div>
</div>
@endsection
