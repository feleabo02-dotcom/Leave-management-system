<x-layouts.erp :title="$company->name">
    <div class="mb-6">
        <a href="{{ route('companies.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">&larr; Back to Companies</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $company->name }}</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Details</h2>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">Slug</dt><dd class="font-medium">{{ $company->slug }}</dd></div>
                    <div><dt class="text-gray-500">Currency</dt><dd class="font-medium">{{ $company->currency }}</dd></div>
                    <div><dt class="text-gray-500">Timezone</dt><dd class="font-medium">{{ $company->timezone }}</dd></div>
                    <div><dt class="text-gray-500">Branches</dt><dd class="font-medium">{{ $company->branches_count }}</dd></div>
                    <div><dt class="text-gray-500">Users</dt><dd class="font-medium">{{ $company->users_count }}</dd></div>
                    @if ($company->phone)<div><dt class="text-gray-500">Phone</dt><dd class="font-medium">{{ $company->phone }}</dd></div>@endif
                    @if ($company->email)<div><dt class="text-gray-500">Email</dt><dd class="font-medium">{{ $company->email }}</dd></div>@endif
                    @if ($company->website)<div><dt class="text-gray-500">Website</dt><dd class="font-medium"><a href="{{ $company->website }}" target="_blank" class="text-indigo-600 hover:underline">{{ $company->website }}</a></dd></div>@endif
                    @if ($company->address)<div class="col-span-2"><dt class="text-gray-500">Address</dt><dd class="font-medium">{{ $company->address }}</dd></div>@endif
                </dl>
            </div>
        </div>
        <div class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Actions</h2>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('companies.edit', $company) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm text-center">Edit Company</a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.erp>
