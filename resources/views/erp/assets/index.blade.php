<x-layouts.erp :title="'Asset Management'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Asset Management</h1>
            <p class="text-sm text-gray-500 mt-0.5">Register and track company hardware and equipment.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="document.getElementById('addAssetModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> Register Asset
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Assets</p>
            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Asset::count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Assigned</p>
            <p class="text-2xl font-bold text-indigo-600">{{ \App\Models\Asset::where('status', 'assigned')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">In Maintenance</p>
            <p class="text-2xl font-bold text-orange-600">{{ \App\Models\Asset::where('status', 'maintenance')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Available</p>
            <p class="text-2xl font-bold text-green-600">{{ \App\Models\Asset::where('status', 'available')->count() }}</p>
        </div>
    </div>

    {{-- Asset Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <div class="flex gap-2">
                <select class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white outline-none">
                    <option>All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white outline-none">
                    <option>All Status</option>
                    <option>Available</option>
                    <option>Assigned</option>
                    <option>Maintenance</option>
                </select>
            </div>
            <div class="relative w-64">
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Search assets..." class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Asset</th>
                        <th class="px-5 py-4 border-b border-gray-100">Category</th>
                        <th class="px-5 py-4 border-b border-gray-100">Serial No.</th>
                        <th class="px-5 py-4 border-b border-gray-100">Assigned To</th>
                        <th class="px-5 py-4 border-b border-gray-100">Status</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($assets as $asset)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $asset->name }}</p>
                                <p class="text-[10px] text-gray-400 font-medium uppercase tracking-tight">{{ $asset->code }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $asset->category->name }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $asset->serial_number ?? '—' }}</td>
                            <td class="px-5 py-4">
                                @if($asset->employee)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-[10px]">
                                            {{ strtoupper(substr($asset->employee->user->name, 0, 2)) }}
                                        </div>
                                        <span class="text-sm text-gray-700 font-medium">{{ $asset->employee->user->name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($asset->status === 'available') bg-green-100 text-green-700
                                    @elseif($asset->status === 'assigned') bg-indigo-100 text-indigo-700
                                    @elseif($asset->status === 'maintenance') bg-orange-100 text-orange-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ $asset->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('assets.show', $asset) }}" class="text-gray-400 hover:text-indigo-600 transition p-1.5"><i class="ph ph-eye text-lg"></i></a>
                                    @if($asset->status === 'available')
                                        <button onclick="openAssignModal('{{ $asset->id }}', '{{ $asset->name }}')" class="text-gray-400 hover:text-indigo-600 transition p-1.5" title="Assign"><i class="ph ph-user-plus text-lg"></i></button>
                                    @elseif($asset->status === 'assigned')
                                        <button onclick="openReturnModal('{{ $asset->id }}', '{{ $asset->name }}')" class="text-gray-400 hover:text-orange-600 transition p-1.5" title="Return"><i class="ph ph-arrow-counter-clockwise text-lg"></i></button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">No assets registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Asset Modal --}}
    <div id="addAssetModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addAssetModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('assets.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Register New Asset</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Asset Name</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. MacBook Pro 14">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Asset Code</label>
                                <input type="text" name="code" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. IT-LAP-001">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Category</label>
                                <select name="asset_category_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Serial Number</label>
                                <input type="text" name="serial_number" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Location</label>
                                <input type="text" name="location" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Register Asset</button>
                        <button type="button" onclick="document.getElementById('addAssetModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Assign Modal --}}
    <div id="assignModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('assignModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="assignForm" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Assign Asset</h3>
                        <p class="text-sm text-gray-500 mb-4" id="assignAssetName"></p>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Employee</label>
                                <select name="employee_id" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">Select Employee...</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->user->name }} ({{ $emp->employee_code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Notes</label>
                                <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Reason for assignment or condition notes..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Assign Asset</button>
                        <button type="button" onclick="document.getElementById('assignModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Return Modal --}}
    <div id="returnModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('returnModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="returnForm" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Return Asset</h3>
                        <p class="text-sm text-gray-500 mb-4" id="returnAssetName"></p>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Return Notes</label>
                                <textarea name="notes" rows="3" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="Condition upon return (e.g. Good, Needs Repair)..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none sm:w-auto sm:text-sm">Process Return</button>
                        <button type="button" onclick="document.getElementById('returnModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAssignModal(assetId, assetName) {
            document.getElementById('assignAssetName').innerText = assetName;
            document.getElementById('assignForm').action = `/assets/${assetId}/assign`;
            document.getElementById('assignModal').classList.remove('hidden');
        }
        function openReturnModal(assetId, assetName) {
            document.getElementById('returnAssetName').innerText = assetName;
            document.getElementById('returnForm').action = `/assets/${assetId}/return`;
            document.getElementById('returnModal').classList.remove('hidden');
        }
    </script>
</x-layouts.erp>
