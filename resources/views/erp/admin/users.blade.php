<x-layouts.erp :title="'Users & Roles'">
    <div class="grid gap-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Directory</p>
                    <h2 class="mt-2 text-xl font-semibold">User management</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <input class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Search users" />
                    <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">Add user</button>
                </div>
            </div>
            <div class="overflow-x-auto mt-6">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 tracking-wider">
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Department</th>
                            <th class="px-4 py-3 font-medium">Role</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Access</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50 transition text-sm">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $user->department?->name ?? 'Unassigned' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $user->roles->first()?->name ?? 'None' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ ucfirst($user->status ?? 'active') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('admin.users.assign-role', $user) }}" class="flex gap-2">
                                        @csrf
                                        <select name="role_id" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}" {{ $user->roles->first()?->id === $role->id ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm" type="submit">Update</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Role & permission UI</p>
            <h3 class="mt-2 text-lg font-semibold">Access control editor</h3>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <p class="font-semibold text-gray-900">Manager</p>
                    <p class="text-xs text-gray-500">Approve requests, view team coverage.</p>
                    <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm mt-4 w-full">Edit permissions</button>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <p class="font-semibold text-gray-900">Employee</p>
                    <p class="text-xs text-gray-500">Submit requests, track balances.</p>
                    <button class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm mt-4 w-full">Edit permissions</button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.erp>
