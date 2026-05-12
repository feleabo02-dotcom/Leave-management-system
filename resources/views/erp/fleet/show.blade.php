<x-layouts.erp :title="'Vehicle Detail'">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('fleet.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $vehicle->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $vehicle->license_plate }} &bull; {{ $vehicle->model->name ?? 'Unknown Model' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('fleet.edit', $vehicle) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-pencil-simple"></i> Edit
            </a>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
            <i class="ph ph-check-circle text-green-500 text-lg flex-shrink-0"></i>
            {{ session('success') }}
            <button @click="show = false" class="ml-auto text-green-600"><i class="ph ph-x"></i></button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="flex flex-col gap-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="w-16 h-16 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-700 mx-auto mb-4">
                    <i class="ph ph-truck text-3xl"></i>
                </div>
                <h2 class="font-bold text-gray-900 text-center text-lg">{{ $vehicle->name }}</h2>
                <p class="text-sm text-gray-500 text-center">{{ $vehicle->license_plate }}</p>
                <div class="mt-4 flex justify-center">
                    <span class="px-3 py-1 text-xs font-medium rounded-full uppercase
                        @if($vehicle->status === 'active') bg-green-100 text-green-700
                        @elseif($vehicle->status === 'maintenance') bg-orange-100 text-orange-700
                        @else bg-red-100 text-red-700
                        @endif">
                        {{ $vehicle->status }}
                    </span>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ph ph-info text-indigo-500"></i> Vehicle Details
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Brand</p>
                        <p class="text-sm font-medium text-gray-900">{{ $vehicle->model->brand->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Model</p>
                        <p class="text-sm font-medium text-gray-900">{{ $vehicle->model->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Color</p>
                        <p class="text-sm font-medium text-gray-900">{{ $vehicle->color ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">VIN Number</p>
                        <p class="text-sm font-medium text-gray-900">{{ $vehicle->vin_number ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Seats</p>
                        <p class="text-sm font-medium text-gray-900">{{ $vehicle->seats ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Odometer</p>
                        <p class="text-sm font-medium text-gray-900">{{ number_format($vehicle->current_odometer) }} km</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ph ph-currency-dollar text-indigo-500"></i> Financial Info
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Acquisition Date</p>
                        <p class="text-sm font-medium text-gray-900">{{ $vehicle->acquisition_date?->format('M d, Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Acquisition Cost</p>
                        <p class="text-sm font-medium text-gray-900">${{ number_format($vehicle->acquisition_cost, 2) ?? '—' }}</p>
                    </div>
                </div>
            </div>

            @if($vehicle->driver)
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <i class="ph ph-user text-indigo-500"></i> Driver
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                                {{ strtoupper(substr($vehicle->driver->user->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $vehicle->driver->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $vehicle->driver->employee_code ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="lg:col-span-2 flex flex-col gap-6">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ph ph-file-text text-indigo-500"></i> Contracts
                    </h3>
                    <button onclick="document.getElementById('addContractModal').classList.remove('hidden')" class="text-xs font-medium text-indigo-600 hover:underline">+ Add Contract</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                                <th class="px-5 py-3 border-b border-gray-100">Type</th>
                                <th class="px-5 py-3 border-b border-gray-100">Name</th>
                                <th class="px-5 py-3 border-b border-gray-100">Provider</th>
                                <th class="px-5 py-3 border-b border-gray-100">Dates</th>
                                <th class="px-5 py-3 border-b border-gray-100">Cost</th>
                                <th class="px-5 py-3 border-b border-gray-100">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($vehicle->contracts as $contract)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-3 text-sm capitalize text-gray-900 font-medium">{{ $contract->type }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $contract->name }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $contract->provider }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">
                                        {{ $contract->start_date?->format('M d, Y') }}
                                        @if($contract->end_date) — {{ $contract->end_date->format('M d, Y') }} @endif
                                    </td>
                                    <td class="px-5 py-3 text-sm font-medium text-gray-900">${{ number_format($contract->cost, 2) }}</td>
                                    <td class="px-5 py-3">
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase
                                            @if($contract->status === 'active') bg-green-100 text-green-700
                                            @elseif($contract->status === 'expired') bg-red-100 text-red-700
                                            @else bg-gray-100 text-gray-700
                                            @endif">
                                            {{ $contract->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-6 text-center text-sm text-gray-400">No contracts yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ph ph-wrench text-indigo-500"></i> Service Logs
                    </h3>
                    <button onclick="document.getElementById('addServiceModal').classList.remove('hidden')" class="text-xs font-medium text-indigo-600 hover:underline">+ Add Service</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                                <th class="px-5 py-3 border-b border-gray-100">Type</th>
                                <th class="px-5 py-3 border-b border-gray-100">Description</th>
                                <th class="px-5 py-3 border-b border-gray-100">Date</th>
                                <th class="px-5 py-3 border-b border-gray-100">Cost</th>
                                <th class="px-5 py-3 border-b border-gray-100">Odometer</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($vehicle->serviceLogs as $service)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-3 text-sm capitalize text-gray-900 font-medium">{{ $service->type }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $service->description }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $service->date?->format('M d, Y') }}</td>
                                    <td class="px-5 py-3 text-sm font-medium text-gray-900">${{ number_format($service->cost, 2) }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ number_format($service->odometer) }} km</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-6 text-center text-sm text-gray-400">No service logs yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ph ph-gas-pump text-indigo-500"></i> Fuel Logs
                    </h3>
                    <button onclick="document.getElementById('addFuelModal').classList.remove('hidden')" class="text-xs font-medium text-indigo-600 hover:underline">+ Add Fuel Entry</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                                <th class="px-5 py-3 border-b border-gray-100">Date</th>
                                <th class="px-5 py-3 border-b border-gray-100">Liters</th>
                                <th class="px-5 py-3 border-b border-gray-100">Cost</th>
                                <th class="px-5 py-3 border-b border-gray-100">Odometer</th>
                                <th class="px-5 py-3 border-b border-gray-100">Fuel Type</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($vehicle->fuelLogs as $log)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $log->date?->format('M d, Y') }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-900 font-medium">{{ number_format($log->liters, 2) }} L</td>
                                    <td class="px-5 py-3 text-sm font-medium text-gray-900">${{ number_format($log->cost, 2) }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ number_format($log->odometer) }} km</td>
                                    <td class="px-5 py-3 text-sm capitalize text-gray-600">{{ $log->fuel_type }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-6 text-center text-sm text-gray-400">No fuel logs yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Contract Modal --}}
    <div id="addContractModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addContractModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('fleet.contracts.store', $vehicle) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Contract</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Type</label>
                                <select name="type" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">Select</option>
                                    <option value="insurance">Insurance</option>
                                    <option value="leasing">Leasing</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="rental">Rental</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Name</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Provider</label>
                                <input type="text" name="provider" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cost</label>
                                <input type="number" step="0.01" name="cost" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Start Date</label>
                                <input type="date" name="start_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">End Date</label>
                                <input type="date" name="end_date" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:w-auto sm:text-sm">Save</button>
                        <button type="button" onclick="document.getElementById('addContractModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Service Modal --}}
    <div id="addServiceModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addServiceModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('fleet.services.store', $vehicle) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Service Log</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Type</label>
                                <select name="type" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="">Select</option>
                                    <option value="oil_change">Oil Change</option>
                                    <option value="tire">Tire</option>
                                    <option value="brake">Brake</option>
                                    <option value="inspection">Inspection</option>
                                    <option value="repair">Repair</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Date</label>
                                <input type="date" name="date" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cost</label>
                                <input type="number" step="0.01" name="cost" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Odometer (km)</label>
                                <input type="number" name="odometer" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label>
                                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:w-auto sm:text-sm">Save</button>
                        <button type="button" onclick="document.getElementById('addServiceModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Fuel Modal --}}
    <div id="addFuelModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('addFuelModal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('fleet.fuel.store', $vehicle) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Fuel Entry</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Date</label>
                                <input type="date" name="date" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Liters</label>
                                <input type="number" step="0.01" name="liters" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cost</label>
                                <input type="number" step="0.01" name="cost" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Odometer (km)</label>
                                <input type="number" name="odometer" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Fuel Type</label>
                                <select name="fuel_type" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                    <option value="gasoline">Gasoline</option>
                                    <option value="diesel">Diesel</option>
                                    <option value="ethanol">Ethanol</option>
                                    <option value="electric">Electric</option>
                                    <option value="hybrid">Hybrid</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:w-auto sm:text-sm">Save</button>
                        <button type="button" onclick="document.getElementById('addFuelModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.erp>
