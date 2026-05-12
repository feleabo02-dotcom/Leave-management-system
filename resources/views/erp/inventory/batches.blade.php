<x-layouts.erp :title="'Picking Batches'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Picking Batches</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage picking batches for warehouse operations.</p>
        </div>
        <button onclick="document.getElementById('createBatchModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
            <i class="ph ph-plus"></i> Create Batch
        </button>
    </div>

    {{-- Batches Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Name</th>
                        <th class="px-5 py-4 border-b border-gray-100">Assigned User</th>
                        <th class="px-5 py-4 border-b border-gray-100">State</th>
                        <th class="px-5 py-4 border-b border-gray-100">Scheduled Date</th>
                        <th class="px-5 py-4 border-b border-gray-100">Wave</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($batches as $batch)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $batch->name }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $batch->user->name ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($batch->state === 'draft') bg-gray-100 text-gray-600
                                    @elseif($batch->state === 'in_progress') bg-indigo-100 text-indigo-700
                                    @elseif($batch->state === 'done') bg-green-100 text-green-700
                                    @else bg-gray-100 text-gray-600
                                    @endif">
                                    {{ str_replace('_', ' ', $batch->state) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $batch->scheduled_date ? $batch->scheduled_date->format('M d, Y') : '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($batch->is_wave) bg-indigo-100 text-indigo-700
                                    @else bg-gray-100 text-gray-600
                                    @endif">
                                    {{ $batch->is_wave ? 'Wave' : '—' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                @if($batch->state !== 'done')
                                    <form action="{{ route('picking-batches.complete', $batch) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-gray-400 hover:text-green-600 transition p-1.5" title="Complete Batch"><i class="ph ph-check text-lg"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">No picking batches found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create Batch Modal --}}
    <div id="createBatchModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('createBatchModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('picking-batches.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Create Picking Batch</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Batch Name</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" placeholder="e.g. BATCH-001">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Scheduled Date</label>
                                <input type="date" name="scheduled_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="flex items-center gap-2 text-xs font-bold text-gray-500 uppercase pt-6">
                                    <input type="checkbox" name="is_wave" value="1" class="rounded border-gray-300 text-indigo-600">
                                    Is Wave
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:w-auto sm:text-sm">Create Batch</button>
                        <button type="button" onclick="document.getElementById('createBatchModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>
