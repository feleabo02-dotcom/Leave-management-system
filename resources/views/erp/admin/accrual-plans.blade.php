<x-layouts.erp :title="'Accrual Plans'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Accrual Plans</h1>
            <p class="text-sm text-gray-500 mt-0.5">Configure automated leave accrual schedules.</p>
        </div>
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('admin.accrual-plans.run') }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                    <i class="ph ph-play"></i> Run Accruals
                </button>
            </form>
            <button onclick="document.getElementById('create-plan-modal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-plus"></i> New Plan
            </button>
        </div>
    </div>

    @if(session('status') === 'accrual-plan-created')
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 flex items-center gap-2">
            <i class="ph ph-check-circle text-lg"></i> Accrual plan created.
        </div>
    @elseif(session('status') === 'accrual-level-created')
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 flex items-center gap-2">
            <i class="ph ph-check-circle text-lg"></i> Accrual level added.
        </div>
    @elseif(str_starts_with(session('status') ?? '', 'accruals-processed-'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 flex items-center gap-2">
            <i class="ph ph-check-circle text-lg"></i> {{ str_replace('accruals-processed-', '', session('status')) }} accruals processed.
        </div>
    @endif

    <div class="space-y-6">
        @forelse($plans as $plan)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">{{ $plan->name }}</h2>
                            <p class="text-sm text-gray-500">{{ $plan->leaveType->name }} · {{ ucfirst($plan->transition_mode) }} transition · {{ ucfirst($plan->accrued_gain_time) }} of period</p>
                        </div>
                        <form method="POST" action="{{ route('admin.accrual-plans.destroy', $plan) }}" class="inline" onsubmit="return confirm('Delete this plan?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition"><i class="ph ph-trash text-lg"></i></button>
                        </form>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Levels</p>
                        <button onclick="document.getElementById('add-level-modal-{{ $plan->id }}').classList.remove('hidden')" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">+ Add Level</button>
                    </div>
                    @if($plan->levels->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($plan->levels as $level)
                                <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $level->name ?? "Level {$level->sequence}" }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ number_format($level->added_value, 2) }} {{ $level->added_value_type }} every {{ $level->frequency }}
                                            @if($level->cap_accrued_time_amount) · Cap: {{ $level->cap_accrued_time_amount }} @endif
                                            @if($level->action_with_unused_accruals === 'carry_over') · Carry over ({{ $level->carryover_options }}) @endif
                                        </p>
                                    </div>
                                    <form method="POST" action="{{ route('admin.accrual-levels.destroy', [$plan, $level]) }}" class="inline" onsubmit="return confirm('Delete this level?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1 text-red-500 hover:bg-red-50 rounded transition"><i class="ph ph-x text-lg"></i></button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400 text-center py-4">No levels defined yet. Add at least one level.</p>
                    @endif
                </div>
            </div>

            {{-- Add Level Modal --}}
            <div id="add-level-modal-{{ $plan->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40" onclick="if(event.target===this)this.classList.add('hidden')">
                <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Add Level — {{ $plan->name }}</h3>
                    <form method="POST" action="{{ route('admin.accrual-levels.store', $plan) }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name (optional)</label>
                                <input type="text" name="name" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Year 1 accrual rate">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Added Value *</label>
                                    <input type="number" name="added_value" step="0.01" required class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                    <select name="added_value_type" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="days">Days</option>
                                        <option value="hours">Hours</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Frequency *</label>
                                <select name="frequency" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="biweekly">Biweekly</option>
                                    <option value="monthly" selected>Monthly</option>
                                    <option value="bimonthly">Bimonthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="biyearly">Biyearly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cap Total</label>
                                    <input type="number" name="cap_accrued_time_amount" step="0.01" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Max total">
                                    <label class="flex items-center gap-2 mt-1"><input type="checkbox" name="cap_accrued_time" value="1" class="h-4 w-4 rounded border-gray-300 text-indigo-600"> Enable cap</label>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Yearly Cap</label>
                                    <input type="number" name="cap_accrued_time_yearly_amount" step="0.01" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Max per year">
                                    <label class="flex items-center gap-2 mt-1"><input type="checkbox" name="cap_accrued_time_yearly" value="1" class="h-4 w-4 rounded border-gray-300 text-indigo-600"> Enable cap</label>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Unused Accruals</label>
                                    <select name="action_with_unused_accruals" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="lost">Lost</option>
                                        <option value="carry_over">Carry Over</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Carry Over</label>
                                    <select name="carryover_options" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="unlimited">Unlimited</option>
                                        <option value="limited">Limited</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Carry Over Limit (days)</label>
                                    <input type="number" name="carryover_limit_days" min="0" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Accrual Validity (days)</label>
                                    <input type="number" name="accrual_validity_days" min="1" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Expiry after carry-over">
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 mt-6">
                            <button type="button" onclick="document.getElementById('add-level-modal-{{ $plan->id }}').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Add Level</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-indigo-100 flex items-center justify-center">
                    <i class="ph ph-clock-counter-clockwise text-3xl text-indigo-600"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900 mb-1">No accrual plans</h2>
                <p class="text-sm text-gray-500 mb-4">Create your first accrual plan to automate leave accrual for your team.</p>
                <button onclick="document.getElementById('create-plan-modal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">Create Plan</button>
            </div>
        @endforelse
    </div>

    {{-- Create Plan Modal --}}
    <div id="create-plan-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40" onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-lg mx-4" onclick="event.stopPropagation()">
            <h3 class="text-lg font-bold text-gray-900 mb-4">New Accrual Plan</h3>
            <form method="POST" action="{{ route('admin.accrual-plans.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plan Name *</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Standard Annual Accrual">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type *</label>
                        <select name="leave_type_id" required class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Transition Mode</label>
                            <select name="transition_mode" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="immediately">Immediately</option>
                                <option value="end_of_accrual">End of Accrual</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Accrued Gain Time</label>
                            <select name="accrued_gain_time" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="start">Start of Period</option>
                                <option value="end">End of Period</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Carryover Date</label>
                        <select name="carryover_date" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="year_start">Year Start</option>
                            <option value="allocation">Allocation Date</option>
                            <option value="other">Custom Date</option>
                        </select>
                    </div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_based_on_worked_time" value="1" class="h-4 w-4 rounded border-gray-300 text-indigo-600">
                        <span class="text-sm text-gray-700">Based on worked time</span>
                    </label>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="document.getElementById('create-plan-modal').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Create Plan</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.erp>
