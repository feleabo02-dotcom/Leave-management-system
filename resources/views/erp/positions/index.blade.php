<x-layouts.erp :title="'Positions'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Positions</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage job positions and titles across departments.</p>
        </div>
        <a href="{{ route('positions.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
            <i class="ph ph-plus"></i> Add Position
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wider">
                        <th class="px-6 py-3 font-medium">Title</th>
                        <th class="px-6 py-3 font-medium">Department</th>
                        <th class="px-6 py-3 font-medium">Level</th>
                        <th class="px-6 py-3 font-medium">Employees</th>
                        <th class="px-6 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($positions as $position)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $position->title }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-700">{{ $position->department?->name ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-700">Level {{ $position->level }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-700">{{ $position->employees_count ?? $position->employees()->count() }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition">
                                    <a href="{{ route('positions.show', $position) }}" class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded">
                                        <i class="ph ph-eye text-lg"></i>
                                    </a>
                                    <a href="{{ route('positions.edit', $position) }}" class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded">
                                        <i class="ph ph-pencil-simple text-lg"></i>
                                    </a>
                                    <form action="{{ route('positions.destroy', $position) }}" method="POST" onsubmit="return confirm('Delete this position?')">
                                        @csrf @method('DELETE')
                                        <button class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded"><i class="ph ph-trash text-lg"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">No positions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $positions->links() }}
        </div>
    </div>
</x-layouts.erp>
