<x-layouts.erp :title="'Repair Order Detail'">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('repair.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $order->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $order->product->name ?? '—' }} &bull; {{ $order->customer->name ?? '—' }}</p>
        </div>
        <div class="flex gap-2">
            @if($order->status === 'draft')
                <form action="{{ route('repair.status', $order) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="in_progress">
                    <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm font-medium hover:bg-orange-700 transition shadow-sm flex items-center gap-2">
                        <i class="ph ph-play"></i> Start Repair
                    </button>
                </form>
            @endif
            @if($order->status === 'in_progress')
                <form action="{{ route('repair.status', $order) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="done">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition shadow-sm flex items-center gap-2">
                        <i class="ph ph-check-circle"></i> Mark Done
                    </button>
                </form>
            @endif
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
                    <i class="ph ph-wrench text-3xl"></i>
                </div>
                <h2 class="font-bold text-gray-900 text-center text-lg">{{ $order->name }}</h2>
                <div class="mt-4 flex justify-center gap-2">
                    <span class="px-3 py-1 text-xs font-medium rounded-full uppercase
                        @if($order->status === 'done') bg-green-100 text-green-700
                        @elseif($order->status === 'in_progress') bg-orange-100 text-orange-700
                        @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                        @else bg-gray-100 text-gray-700
                        @endif">
                        {{ str_replace('_', ' ', $order->status) }}
                    </span>
                    <span class="px-3 py-1 text-xs font-medium rounded-full uppercase
                        @if($order->priority === 'urgent') bg-red-100 text-red-700
                        @elseif($order->priority === 'high') bg-orange-100 text-orange-700
                        @else bg-blue-100 text-blue-700
                        @endif">
                        {{ $order->priority }}
                    </span>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ph ph-info text-indigo-500"></i> Order Info
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Product</p>
                        <p class="text-sm font-medium text-gray-900">{{ $order->product->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Customer</p>
                        <p class="text-sm font-medium text-gray-900">{{ $order->customer->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Assignee</p>
                        <p class="text-sm font-medium text-gray-900">{{ $order->assignedTo->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Date Requested</p>
                        <p class="text-sm font-medium text-gray-900">{{ $order->date_requested?->format('M d, Y') ?? '—' }}</p>
                    </div>
                </div>
            </div>

            @if($order->internal_notes)
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <i class="ph ph-lock-key text-indigo-500"></i> Internal Notes
                        </h3>
                    </div>
                    <div class="p-5 text-sm text-gray-600 whitespace-pre-wrap">{{ $order->internal_notes }}</div>
                </div>
            @endif

            @if($order->customer_notes)
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                            <i class="ph ph-chat-text text-indigo-500"></i> Customer Notes
                        </h3>
                    </div>
                    <div class="p-5 text-sm text-gray-600 whitespace-pre-wrap">{{ $order->customer_notes }}</div>
                </div>
            @endif
        </div>

        <div class="lg:col-span-2 flex flex-col gap-6">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ph ph-stethoscope text-indigo-500"></i> Diagnosis
                    </h3>
                </div>
                <div class="p-5 text-sm text-gray-600 whitespace-pre-wrap">
                    {{ $order->diagnosis ?? 'No diagnosis recorded.' }}
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        <i class="ph ph-list-bullets text-indigo-500"></i> Repair Lines
                    </h3>
                    <button onclick="document.getElementById('addLineForm').classList.toggle('hidden')" class="text-xs font-medium text-indigo-600 hover:underline">+ Add Line</button>
                </div>

                <div id="addLineForm" class="hidden p-5 border-b border-gray-100 bg-indigo-50">
                    <form action="{{ route('repair.lines.store', $order) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Description *</label>
                            <input type="text" name="description" required class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Product</label>
                            <select name="product_id" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg">
                                <option value="">—</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Qty</label>
                            <input type="number" name="quantity" value="1" min="1" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Cost</label>
                            <input type="number" step="0.01" name="cost" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Price</label>
                            <input type="number" step="0.01" name="price" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                            <select name="type" class="w-full px-3 py-1.5 text-sm border border-gray-300 rounded-lg">
                                <option value="part">Part</option>
                                <option value="labor">Labor</option>
                                <option value="service">Service</option>
                            </select>
                        </div>
                        <div class="md:col-span-3 flex justify-end gap-2">
                            <button type="button" onclick="document.getElementById('addLineForm').classList.add('hidden')" class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg">Cancel</button>
                            <button type="submit" class="px-3 py-1.5 text-xs bg-indigo-600 text-white rounded-lg">Add Line</button>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                                <th class="px-5 py-3 border-b border-gray-100">Description</th>
                                <th class="px-5 py-3 border-b border-gray-100">Product</th>
                                <th class="px-5 py-3 border-b border-gray-100">Qty</th>
                                <th class="px-5 py-3 border-b border-gray-100">Cost</th>
                                <th class="px-5 py-3 border-b border-gray-100">Price</th>
                                <th class="px-5 py-3 border-b border-gray-100">Type</th>
                                <th class="px-5 py-3 border-b border-gray-100 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($order->lines as $line)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-3 text-sm text-gray-900 font-medium">{{ $line->description }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $line->product->name ?? '—' }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $line->quantity }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">${{ number_format($line->cost, 2) }}</td>
                                    <td class="px-5 py-3 text-sm font-medium text-gray-900">${{ number_format($line->price, 2) }}</td>
                                    <td class="px-5 py-3 text-sm capitalize text-gray-600">{{ $line->type }}</td>
                                    <td class="px-5 py-3 text-right">
                                        <form action="{{ route('repair.lines.destroy', [$order, $line]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this line?')">
                                            @csrf @method('DELETE')
                                            <button class="text-gray-400 hover:text-red-600 transition p-1.5"><i class="ph ph-trash text-lg"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-6 text-center text-sm text-gray-400">No repair lines added yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.erp>
