<x-layouts.erp :title="'Recruitment'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Job Positions</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage open positions and track applicants.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('recruitment.applications') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-users"></i> View Applications
            </a>
            <button onclick="document.getElementById('addPositionModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> New Position
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Positions</p>
            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\JobPosition::count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Open</p>
            <p class="text-2xl font-bold text-green-600">{{ \App\Models\JobPosition::where('status', 'open')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Draft</p>
            <p class="text-2xl font-bold text-gray-600">{{ \App\Models\JobPosition::where('status', 'draft')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Filled</p>
            <p class="text-2xl font-bold text-indigo-600">{{ \App\Models\JobPosition::where('status', 'filled')->count() }}</p>
        </div>
    </div>

    {{-- Positions Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <div class="flex gap-2">
                <select id="filterStatus" class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white outline-none">
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="open">Open</option>
                    <option value="closed">Closed</option>
                    <option value="filled">Filled</option>
                </select>
            </div>
            <div class="relative w-64">
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Search positions..." class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Position</th>
                        <th class="px-5 py-4 border-b border-gray-100">Department</th>
                        <th class="px-5 py-4 border-b border-gray-100">Status</th>
                        <th class="px-5 py-4 border-b border-gray-100">Applicants</th>
                        <th class="px-5 py-4 border-b border-gray-100">Hiring Manager</th>
                        <th class="px-5 py-4 border-b border-gray-100">Created</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($positions as $position)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                <a href="{{ route('recruitment.show', $position) }}" class="text-sm font-bold text-gray-900 hover:text-indigo-600">{{ $position->name }}</a>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $position->department ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($position->status === 'open') bg-green-100 text-green-700
                                    @elseif($position->status === 'draft') bg-gray-100 text-gray-600
                                    @elseif($position->status === 'filled') bg-indigo-100 text-indigo-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ $position->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $position->applications_count }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $position->hiringManager?->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-xs text-gray-400">{{ $position->created_at->format('M d, Y') }}</td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('recruitment.show', $position) }}" class="text-gray-400 hover:text-indigo-600 transition p-1.5"><i class="ph ph-eye text-lg"></i></a>
                                    <button onclick="openEditModal({{ $position->id }})" class="text-gray-400 hover:text-indigo-600 transition p-1.5"><i class="ph ph-pencil text-lg"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">No positions created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($positions->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $positions->links() }}
            </div>
        @endif
    </div>

    {{-- Create Position Modal --}}
    <div id="addPositionModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addPositionModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('recruitment.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Create Job Position</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Position Title</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Senior Software Engineer">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Department</label>
                                <input type="text" name="department" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Engineering">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                                <select name="status" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="draft">Draft</option>
                                    <option value="open" selected>Open</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Salary Min</label>
                                <input type="number" name="salary_min" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Salary Max</label>
                                <input type="number" name="salary_max" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Hiring Manager</label>
                                <select name="hiring_manager_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">Select Manager...</option>
                                    @foreach($hiringManagers as $manager)
                                        <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label>
                                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Job description..."></textarea>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Requirements</label>
                                <textarea name="requirements" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Requirements..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Create Position</button>
                        <button type="button" onclick="document.getElementById('addPositionModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Position Modal --}}
    <div id="editPositionModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('editPositionModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="editPositionForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Position</h3>
                        <div class="grid grid-cols-2 gap-4" id="editPositionFields">
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Update Position</button>
                        <button type="button" onclick="document.getElementById('editPositionModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(id) {
            fetch('/recruitment/' + id)
                .then(r => r.json())
                .then(pos => {
                    const fields = document.getElementById('editPositionFields');
                    fields.innerHTML = `
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Position Title</label>
                            <input type="text" name="name" value="${pos.name}" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Department</label>
                            <input type="text" name="department" value="${pos.department || ''}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                            <select name="status" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                <option value="draft" ${pos.status === 'draft' ? 'selected' : ''}>Draft</option>
                                <option value="open" ${pos.status === 'open' ? 'selected' : ''}>Open</option>
                                <option value="closed" ${pos.status === 'closed' ? 'selected' : ''}>Closed</option>
                                <option value="filled" ${pos.status === 'filled' ? 'selected' : ''}>Filled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Salary Min</label>
                            <input type="number" name="salary_min" step="0.01" value="${pos.salary_min || ''}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Salary Max</label>
                            <input type="number" name="salary_max" step="0.01" value="${pos.salary_max || ''}" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Hiring Manager</label>
                            <select name="hiring_manager_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                <option value="">Select Manager...</option>
                                @foreach($hiringManagers as $manager)
                                    <option value="{{ $manager->id }}" ${pos.hiring_manager_id === {{ $manager->id }} ? 'selected' : ''}>{{ $manager->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label>
                            <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">${pos.description || ''}</textarea>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Requirements</label>
                            <textarea name="requirements" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">${pos.requirements || ''}</textarea>
                        </div>
                    `;
                    document.getElementById('editPositionForm').action = '/recruitment/' + id;
                    document.getElementById('editPositionModal').classList.remove('hidden');
                });
        }
    </script>
</x-layouts.erp>