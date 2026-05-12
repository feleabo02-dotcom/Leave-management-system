<x-layouts.erp :title="'Position Details'">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('positions.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $position->title }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $position->department?->name ?? 'No Department' }} &bull; Level {{ $position->level }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('positions.edit', $position) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-pencil-simple"></i> Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="flex flex-col gap-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2 mb-4">
                    <i class="ph ph-briefcase text-indigo-500"></i> Position Info
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Title</p>
                        <p class="text-sm font-medium text-gray-900">{{ $position->title }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Department</p>
                        <p class="text-sm font-medium text-gray-900">{{ $position->department?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Level</p>
                        <p class="text-sm font-medium text-gray-900">{{ $position->level }}</p>
                    </div>
                    @if($position->description)
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Description</p>
                            <p class="text-sm text-gray-700">{{ $position->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ph ph-users text-indigo-500"></i> Employees in this Position
                    </h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($position->employees as $emp)
                        <div class="flex items-center justify-between px-5 py-3.5 hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                    {{ strtoupper(substr($emp->user?->name ?? 'U', 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $emp->user?->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-500">{{ $emp->employee_code }}</p>
                                </div>
                            </div>
                            <a href="{{ route('employees.show', $emp) }}" class="text-xs text-indigo-600 hover:underline">View</a>
                        </div>
                    @empty
                        <div class="px-5 py-6 text-center text-sm text-gray-400">No employees in this position.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts.erp>
