<x-layouts.erp :title="'Equipment Detail'">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('maintenance.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $equipment->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $equipment->code }} &bull; {{ $equipment->category->name ?? 'No Category' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('maintenance.edit', $equipment) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-pencil-simple"></i> Edit
            </a>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            <i class="ph ph-check-circle text-green-500 text-lg flex-shrink-0"></i>
            {{ session('success') }}
            <button @click="show = false" class="ml-auto text-green-600"><i class="ph ph-x"></i></button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="flex flex-col gap-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="w-16 h-16 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-700 mx-auto mb-4">
                    <i class="ph ph-toolbox text-3xl"></i>
                </div>
                <h2 class="font-bold text-gray-900 text-center text-lg">{{ $equipment->name }}</h2>
                <p class="text-sm text-gray-500 text-center">{{ $equipment->code }}</p>
                <div class="mt-4 flex justify-center">
                    <span class="px-3 py-1 text-xs font-medium rounded-full uppercase
                        @if($equipment->status === 'operating') bg-green-100 text-green-700
                        @elseif($equipment->status === 'under_maintenance') bg-orange-100 text-orange-700
                        @else bg-red-100 text-red-700
                        @endif">
                        {{ str_replace('_', ' ', $equipment->status) }}
                    </span>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ph ph-info text-indigo-500"></i> Equipment Info
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Category</p>
                        <p class="text-sm font-medium text-gray-900">{{ $equipment->category->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Location</p>
                        <p class="text-sm font-medium text-gray-900">{{ $equipment->location ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Purchase Date</p>
                        <p class="text-sm font-medium text-gray-900">{{ $equipment->purchase_date?->format('M d, Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Purchase Cost</p>
                        <p class="text-sm font-medium text-gray-900">${{ number_format($equipment->purchase_cost, 2) ?? '—' }}</p>
                    </div>
                </div>
            </div>

            @if($equipment->notes)
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-semibold text-gray-800">Notes</h3>
                    </div>
                    <div class="p-5 text-sm text-gray-600">
                        {{ $equipment->notes }}
                    </div>
                </div>
            @endif
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ph ph-clipboard-text text-indigo-500"></i> Request History
                    </h3>
                    <button onclick="document.getElementById('addRequestModal').classList.remove('hidden')" class="text-xs font-medium text-indigo-600 hover:underline">+ Add Request</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                                <th class="px-5 py-3 border-b border-gray-100">Name</th>
                                <th class="px-5 py-3 border-b border-gray-100">Priority</th>
                                <th class="px-5 py-3 border-b border-gray-100">Status</th>
                                <th class="px-5 py-3 border-b border-gray-100">Assigned To</th>
                                <th class="px-5 py-3 border-b border-gray-100">Scheduled</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($equipment->requests as $req)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-3 text-sm font-bold text-gray-900">{{ $req->name }}</td>
                                    <td class="px-5 py-3">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                            @if($req->priority === 'urgent') bg-red-100 text-red-700
                                            @elseif($req->priority === 'high') bg-orange-100 text-orange-700
                                            @else bg-blue-100 text-blue-700
                                            @endif">
                                            {{ $req->priority }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                            @if($req->status === 'done') bg-green-100 text-green-700
                                            @elseif($req->status === 'in_progress') bg-orange-100 text-orange-700
                                            @else bg-gray-100 text-gray-700
                                            @endif">
                                            {{ str_replace('_', ' ', $req->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $req->assignedTo->name ?? '—' }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $req->scheduled_date?->format('M d, Y') ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-6 text-center text-sm text-gray-400">No requests for this equipment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Request Modal --}}
    <div id="addRequestModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addRequestModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('maintenance.requests.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">New Maintenance Request</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Name *</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Priority</label>
                                <select name="priority" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Scheduled Date</label>
                                <input type="date" name="scheduled_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label>
                                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:w-auto sm:text-sm">Save</button>
                        <button type="button" onclick="document.getElementById('addRequestModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>
