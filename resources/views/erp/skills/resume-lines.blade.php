<x-layouts.erp :title="'Resume Lines'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Resume Lines</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage employee resume lines and line types.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('addResumeLineModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> Add Resume Line
            </button>
            <button onclick="document.getElementById('addLineTypeModal').classList.remove('hidden')" class="px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> Add Line Type
            </button>
        </div>
    </div>

    {{-- Resume Lines Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Resume Lines</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Employee</th>
                        <th class="px-5 py-4 border-b border-gray-100">Line Name</th>
                        <th class="px-5 py-4 border-b border-gray-100">Date Start</th>
                        <th class="px-5 py-4 border-b border-gray-100">Date End</th>
                        <th class="px-5 py-4 border-b border-gray-100">Line Type</th>
                        <th class="px-5 py-4 border-b border-gray-100">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($resumeLines as $line)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-[10px]">
                                        {{ strtoupper(substr($line->employee->user->name ?? 'NA', 0, 2)) }}
                                    </div>
                                    <span class="text-sm text-gray-700 font-medium">{{ $line->employee->user->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $line->name }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $line->date_start ? $line->date_start->format('M d, Y') : '—' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $line->date_end ? $line->date_end->format('M d, Y') : '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-gray-100 text-gray-600 uppercase">
                                    {{ $line->lineType->name ?? '—' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $line->description ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">No resume lines found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Line Types Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50">
            <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Line Types</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Name</th>
                        <th class="px-5 py-4 border-b border-gray-100">Sequence</th>
                        <th class="px-5 py-4 border-b border-gray-100">Is Course</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($lineTypes as $type)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $type->name }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $type->sequence ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($type->is_course) bg-green-100 text-green-700
                                    @else bg-gray-100 text-gray-600
                                    @endif">
                                    {{ $type->is_course ? 'Yes' : 'No' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-12 text-center text-gray-400 text-sm">No line types defined.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Resume Line Modal --}}
    <div id="addResumeLineModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addResumeLineModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('resume-lines.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Resume Line</h3>
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
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Line Name</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Software Engineer at Corp">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Date Start</label>
                                <input type="date" name="date_start" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Date End</label>
                                <input type="date" name="date_end" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Line Type</label>
                                <select name="line_type_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">Select Type...</option>
                                    @foreach($lineTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label>
                                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Brief description..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Save Resume Line</button>
                        <button type="button" onclick="document.getElementById('addResumeLineModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Line Type Modal --}}
    <div id="addLineTypeModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addLineTypeModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('resume-line-types.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Line Type</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Name</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. Work Experience">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sequence</label>
                                <input type="number" name="sequence" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" value="0">
                            </div>
                            <div>
                                <label class="flex items-center gap-2 text-xs font-bold text-gray-500 uppercase">
                                    <input type="checkbox" name="is_course" value="1" class="rounded border-gray-300 text-indigo-600">
                                    Is Course
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Save Line Type</button>
                        <button type="button" onclick="document.getElementById('addLineTypeModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>
