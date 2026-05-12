<x-layouts.erp :title="'Skills Management'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Skills Management</h1>
            <p class="text-sm text-gray-500 mt-0.5">Define skill types and skills for your organization.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('addSkillTypeModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> Add Skill Type
            </button>
            <button onclick="document.getElementById('addSkillModal').classList.remove('hidden')" class="px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> Add Skill
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Skill Types</p>
            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\SkillType::count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Skills</p>
            <p class="text-2xl font-bold text-indigo-600">{{ \App\Models\Skill::count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Active Types</p>
            <p class="text-2xl font-bold text-green-600">{{ \App\Models\SkillType::where('active', true)->count() }}</p>
        </div>
    </div>

    {{-- Skill Types Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Skill Types</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Name</th>
                        <th class="px-5 py-4 border-b border-gray-100">Status</th>
                        <th class="px-5 py-4 border-b border-gray-100">Color</th>
                        <th class="px-5 py-4 border-b border-gray-100">Skills</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($skillTypes as $skillType)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $skillType->name }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($skillType->active) bg-green-100 text-green-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ $skillType->active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-4 h-4 rounded-full inline-block" style="background-color: {{ $skillType->color ?? '#6366f1' }}"></span>
                                    <span class="text-sm text-gray-500">{{ $skillType->color ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $skillType->skills->count() }}</td>
                            <td class="px-5 py-4 text-right">
                                <form action="{{ route('skill-types.destroy', $skillType) }}" method="POST" onsubmit="return confirm('Delete this skill type?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition p-1.5"><i class="ph ph-trash text-lg"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">No skill types defined yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Skills Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Skills</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Name</th>
                        <th class="px-5 py-4 border-b border-gray-100">Skill Type</th>
                        <th class="px-5 py-4 border-b border-gray-100">Sequence</th>
                        <th class="px-5 py-4 border-b border-gray-100">Color</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($skills as $skill)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $skill->name }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $skill->skillType->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $skill->sequence ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="w-4 h-4 rounded-full inline-block" style="background-color: {{ $skill->color ?? '#6366f1' }}"></span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <form action="{{ route('skills.destroy', $skill) }}" method="POST" onsubmit="return confirm('Delete this skill?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition p-1.5"><i class="ph ph-trash text-lg"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">No skills defined yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Skill Type Modal --}}
    <div id="addSkillTypeModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addSkillTypeModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('skill-types.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Skill Type</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Name</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Technical Skills">
                            </div>
                            <div>
                                <label class="flex items-center gap-2 text-xs font-bold text-gray-500 uppercase">
                                    <input type="checkbox" name="active" value="1" checked class="rounded border-gray-300 text-indigo-600">
                                    Active
                                </label>
                            </div>
                            <div>
                                <label class="flex items-center gap-2 text-xs font-bold text-gray-500 uppercase">
                                    <input type="checkbox" name="is_certification" value="1" class="rounded border-gray-300 text-indigo-600">
                                    Is Certification
                                </label>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Color</label>
                                <input type="color" name="color" value="#6366f1" class="w-full h-9 px-1 py-1 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sequence</label>
                                <input type="number" name="sequence" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Save Skill Type</button>
                        <button type="button" onclick="document.getElementById('addSkillTypeModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Skill Modal --}}
    <div id="addSkillModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addSkillModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('skills.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Skill</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Name</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. PHP">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Skill Type</label>
                                <select name="skill_type_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">Select Type...</option>
                                    @foreach($skillTypes as $st)
                                        <option value="{{ $st->id }}">{{ $st->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sequence</label>
                                <input type="number" name="sequence" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" value="0">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Color</label>
                                <input type="color" name="color" value="#6366f1" class="w-full h-9 px-1 py-1 border border-gray-200 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Save Skill</button>
                        <button type="button" onclick="document.getElementById('addSkillModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>
