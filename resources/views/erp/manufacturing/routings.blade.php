<x-layouts.erp :title="'Routings'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Routings</h1>
            <p class="text-sm text-gray-500 mt-0.5">Define manufacturing routing operations.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('addRoutingModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> New Routing
            </button>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Name</th>
                        <th class="px-5 py-4 border-b border-gray-100">Code</th>
                        <th class="px-5 py-4 border-b border-gray-100">BOM</th>
                        <th class="px-5 py-4 border-b border-gray-100">Lead Time</th>
                        <th class="px-5 py-4 border-b border-gray-100">Steps</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($routings as $routing)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $routing->name }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $routing->code }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $routing->bom->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $routing->lead_time ?? '—' }} hrs</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $routing->steps->count() }} step(s)</td>
                            <td class="px-5 py-4 text-right">
                                <button onclick="document.getElementById('routingSteps{{ $routing->id }}').classList.toggle('hidden')" class="text-gray-400 hover:text-indigo-600 transition p-1.5">
                                    <i class="ph ph-eye text-lg"></i>
                                </button>
                            </td>
                        </tr>
                        <tr id="routingSteps{{ $routing->id }}" class="hidden bg-gray-50">
                            <td colspan="6" class="px-5 py-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold uppercase text-gray-500">Steps</span>
                                    <button onclick="document.getElementById('addStepForm{{ $routing->id }}').classList.toggle('hidden')" class="text-xs font-medium text-indigo-600 hover:underline">+ Add Step</button>
                                </div>

                                <div id="addStepForm{{ $routing->id }}" class="hidden mb-4 p-4 bg-white border border-gray-200 rounded-lg">
                                    <form action="{{ route('manufacturing.routings.steps.store', $routing) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        @csrf
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Name *</label>
                                            <input type="text" name="name" required class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Work Center</label>
                                            <select name="work_center_id" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg">
                                                @foreach($workCenters as $wc)
                                                    <option value="{{ $wc->id }}">{{ $wc->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Duration (hrs)</label>
                                            <input type="number" step="0.01" name="duration" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg">
                                        </div>
                                        <div class="md:col-span-3 flex justify-end gap-2">
                                            <button type="button" onclick="document.getElementById('addStepForm{{ $routing->id }}').classList.add('hidden')" class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg">Cancel</button>
                                            <button type="submit" class="px-3 py-1.5 text-xs bg-indigo-600 text-white rounded-lg">Add</button>
                                        </div>
                                    </form>
                                </div>

                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">
                                            <th class="pb-2 pr-4">Step</th>
                                            <th class="pb-2 pr-4">Work Center</th>
                                            <th class="pb-2 pr-4">Duration</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse($routing->steps as $step)
                                            <tr>
                                                <td class="py-1.5 pr-4 text-sm text-gray-700">{{ $step->name }}</td>
                                                <td class="py-1.5 pr-4 text-sm text-gray-700">{{ $step->workCenter->name ?? '—' }}</td>
                                                <td class="py-1.5 pr-4 text-sm text-gray-700">{{ $step->duration }} hrs</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="py-2 text-sm text-gray-400 italic">No steps defined.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">No routings defined.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Routing Modal --}}
    <div id="addRoutingModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addRoutingModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('manufacturing.routings.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">New Routing</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Name *</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Code *</label>
                                <input type="text" name="code" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">BOM</label>
                                <select name="bom_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    @foreach($boms as $bom)
                                        <option value="{{ $bom->id }}">{{ $bom->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Lead Time (hours)</label>
                                <input type="number" step="0.01" name="lead_time" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:w-auto sm:text-sm">Save</button>
                        <button type="button" onclick="document.getElementById('addRoutingModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>
