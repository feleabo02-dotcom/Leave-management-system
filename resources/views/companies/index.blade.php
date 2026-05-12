<x-layouts.erp :title="'Companies'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Companies</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $companies->total() }} total</p>
        </div>
        <a href="{{ route('companies.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm inline-flex items-center gap-2">
            <i class="ph ph-plus-circle text-lg"></i>
            <span>New Company</span>
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wider">
                        <th class="px-4 py-3 font-medium">Name</th>
                        <th class="px-4 py-3 font-medium">Slug</th>
                        <th class="px-4 py-3 font-medium">Branches</th>
                        <th class="px-4 py-3 font-medium">Users</th>
                        <th class="px-4 py-3 font-medium">Currency</th>
                        <th class="px-4 py-3 font-medium">Timezone</th>
                        <th class="px-4 py-3 font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($companies as $company)
                        <tr class="hover:bg-gray-50 transition text-sm">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $company->name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $company->slug }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $company->branches_count }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $company->users_count }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $company->currency }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $company->timezone }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('companies.show', $company) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm p-1.5">
                                        <i class="ph ph-eye text-lg"></i>
                                    </a>
                                    <a href="{{ route('companies.edit', $company) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm p-1.5">
                                        <i class="ph ph-pencil-simple text-lg"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">No companies found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($companies->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $companies->links() }}
            </div>
        @endif
    </div>
</x-layouts.erp>
