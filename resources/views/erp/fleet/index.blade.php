<x-layouts.erp :title="'Fleet Management'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Fleet Management</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage vehicles, drivers, and fleet operations.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('fleet.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> Register Vehicle
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Vehicles</p>
            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\FleetVehicle::count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Active Vehicles</p>
            <p class="text-2xl font-bold text-green-600">{{ \App\Models\FleetVehicle::where('status', 'active')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">In Maintenance</p>
            <p class="text-2xl font-bold text-orange-600">{{ \App\Models\FleetVehicle::where('status', 'maintenance')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Total Costs</p>
            <p class="text-2xl font-bold text-indigo-600">${{ number_format(\App\Models\FleetVehicle::sum('acquisition_cost'), 2) }}</p>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <div class="flex gap-2">
                <select class="px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white outline-none">
                    <option>All Status</option>
                    <option>Active</option>
                    <option>Maintenance</option>
                    <option>Retired</option>
                </select>
            </div>
            <div class="relative w-64">
                <i class="ph ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Search vehicles..." class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Name</th>
                        <th class="px-5 py-4 border-b border-gray-100">License Plate</th>
                        <th class="px-5 py-4 border-b border-gray-100">Model / Brand</th>
                        <th class="px-5 py-4 border-b border-gray-100">Driver</th>
                        <th class="px-5 py-4 border-b border-gray-100">Status</th>
                        <th class="px-5 py-4 border-b border-gray-100">Odometer</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($vehicles as $vehicle)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $vehicle->name }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $vehicle->license_plate }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $vehicle->model->name ?? '—' }} / {{ $vehicle->model->brand->name ?? '—' }}</td>
                            <td class="px-5 py-4">
                                @if($vehicle->driver)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-[10px]">
                                            {{ strtoupper(substr($vehicle->driver->name, 0, 2)) }}
                                        </div>
                                        <span class="text-sm text-gray-700 font-medium">{{ $vehicle->driver->name }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                    @if($vehicle->status === 'active') bg-green-100 text-green-700
                                    @elseif($vehicle->status === 'maintenance') bg-orange-100 text-orange-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ $vehicle->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ number_format($vehicle->current_odometer) }} km</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('fleet.show', $vehicle) }}" class="text-gray-400 hover:text-indigo-600 transition p-1.5 inline-block"><i class="ph ph-eye text-lg"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">No vehicles registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($vehicles, 'links'))
            <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
                {{ $vehicles->links() }}
            </div>
        @endif
    </div>
</x-layouts.erp>
