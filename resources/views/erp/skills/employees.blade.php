<x-layouts.erp :title="'Employee Skills'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Employee Skills</h1>
            <p class="text-sm text-gray-500 mt-0.5">View and assign skills to employees.</p>
        </div>
        <button onclick="document.getElementById('addEmployeeSkillModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
            <i class="ph ph-plus"></i> Add Skill to Employee
        </button>
    </div>

    {{-- Search / Filter --}}
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-6">
        <div class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Filter by Employee</label>
                <select id="employeeFilter" onchange="window.location.href=this.value" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    <option value="{{ route('skills.employees') }}">All Employees</option>
                    @foreach($employees as $emp)
                        <option value="{{ route('skills.employees', ['employee_id' => $emp->id]) }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Employee Skills List --}}
    @forelse($employeeSkillsGrouped as $employeeId => $skills)
        @php $employee = \App\Models\Employee::find($employeeId); @endphp
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-4">
            <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($employee->user->name ?? 'NA', 0, 2)) }}
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">{{ $employee->user->name ?? 'Unknown' }}</h2>
                        <p class="text-[10px] text-gray-400 font-medium">{{ $employee->employee_code }}</p>
                    </div>
                </div>
                <span class="text-xs text-gray-400">{{ $skills->count() }} skill(s)</span>
            </div>
            <div class="p-5">
                <div class="flex flex-wrap gap-2">
                    @foreach($skills as $es)
                        @php
                            $progressColors = ['bg-gray-200', 'bg-yellow-400', 'bg-orange-400', 'bg-indigo-500', 'bg-green-500'];
                            $progress = $es->skillLevel->level_progress ?? 0;
                            $colorClass = $progressColors[$progress] ?? 'bg-gray-200';
                        @endphp
                        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium border border-gray-200">
                            <span class="w-2 h-2 rounded-full {{ $colorClass }}"></span>
                            <span>{{ $es->skill->name ?? 'Unknown' }}</span>
                            @if($es->skillLevel)
                                <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 text-[10px] font-bold">{{ $es->skillLevel->name }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-12 text-center">
            <i class="ph ph-users text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-400 text-sm">No employee skills found.</p>
        </div>
    @endforelse

    {{-- Add Employee Skill Modal --}}
    <div id="addEmployeeSkillModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addEmployeeSkillModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('employee-skills.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Skill to Employee</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Employee</label>
                                <select name="employee_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">Select Employee...</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->user->name }} ({{ $emp->employee_code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Skill</label>
                                <select name="skill_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">Select Skill...</option>
                                    @foreach($skills as $skill)
                                        <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Level</label>
                                <select name="skill_level_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">Select Level...</option>
                                    @foreach($skillLevels as $level)
                                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Assign Skill</button>
                        <button type="button" onclick="document.getElementById('addEmployeeSkillModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>
