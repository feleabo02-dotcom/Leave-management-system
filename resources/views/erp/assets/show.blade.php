<x-layouts.erp :title="'Asset - ' . $asset->name">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('assets.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $asset->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Asset Code: {{ $asset->code }}</p>
        </div>
        <div class="flex gap-2">
            @if($asset->status === 'available')
                <button onclick="openAssignModal('{{ $asset->id }}', '{{ $asset->name }}')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                    <i class="ph ph-user-plus"></i> Assign Asset
                </button>
            @elseif($asset->status === 'assigned')
                <button onclick="openReturnModal('{{ $asset->id }}', '{{ $asset->name }}')" class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm font-medium hover:bg-orange-700 transition shadow-sm flex items-center gap-2">
                    <i class="ph ph-arrow-counter-clockwise"></i> Return Asset
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Details --}}
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Asset Information</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Category</p>
                        <p class="text-sm font-medium text-gray-900">{{ $asset->category->name }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Serial Number</p>
                        <p class="text-sm font-medium text-gray-900">{{ $asset->serial_number ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Purchase Date</p>
                        <p class="text-sm font-medium text-gray-900">{{ $asset->purchase_date ? $asset->purchase_date->format('M d, Y') : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Purchase Cost</p>
                        <p class="text-sm font-medium text-gray-900">{{ $asset->purchase_cost ? '$' . number_format($asset->purchase_cost, 2) : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Location</p>
                        <p class="text-sm font-medium text-gray-900">{{ $asset->location ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Status</p>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                            @if($asset->status === 'available') bg-green-100 text-green-700
                            @elseif($asset->status === 'assigned') bg-indigo-100 text-indigo-700
                            @elseif($asset->status === 'maintenance') bg-orange-100 text-orange-700
                            @else bg-red-100 text-red-700
                            @endif">
                            {{ $asset->status }}
                        </span>
                    </div>
                </div>
            </div>

            @if($asset->employee)
                <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-6 shadow-sm">
                    <h3 class="text-xs font-bold text-indigo-400 uppercase tracking-widest mb-4">Current User</h3>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">
                            {{ strtoupper(substr($asset->employee->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-indigo-900">{{ $asset->employee->user->name }}</p>
                            <p class="text-[10px] text-indigo-500 font-medium">{{ $asset->employee->employee_code }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- History --}}
        <div class="md:col-span-2">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Assignment & Maintenance History</h3>
                </div>
                <div class="p-0">
                    <ul class="divide-y divide-gray-50">
                        @forelse($asset->histories as $history)
                            <li class="px-5 py-4 hover:bg-gray-50 transition">
                                <div class="flex items-start gap-4">
                                    <div class="mt-1 w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                        @if($history->action === 'assigned') bg-indigo-100 text-indigo-600
                                        @elseif($history->action === 'returned') bg-orange-100 text-orange-600
                                        @else bg-gray-100 text-gray-600
                                        @endif">
                                        <i class="ph {{ $history->action === 'assigned' ? 'ph-user-plus' : 'ph-arrow-counter-clockwise' }}"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <p class="text-sm font-bold text-gray-900 capitalize">{{ $history->action }}</p>
                                            <p class="text-xs text-gray-400">{{ $history->action_date->format('M d, Y') }}</p>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2">
                                            @if($history->employee)
                                                Employee: <span class="font-medium text-gray-800">{{ $history->employee->user->name }}</span>
                                            @else
                                                System/Maintenance
                                            @endif
                                        </p>
                                        @if($history->notes)
                                            <div class="p-2 bg-gray-50 rounded border border-gray-100 text-xs text-gray-500 italic">
                                                "{{ $history->notes }}"
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="px-5 py-12 text-center text-gray-400 text-sm">No history records found for this asset.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-layouts.erp>
