<x-layouts.erp :title="'New Leave Request'">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('employee.dashboard') }}" class="p-2 rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition">
                <i class="ph ph-caret-left text-lg"></i>
            </a>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Leave request</p>
                <h1 class="mt-1 text-2xl font-bold text-gray-900">New Request</h1>
            </div>
        </div>

        <form method="POST" action="{{ route('employee.requests.store') }}" class="bg-white border border-gray-200 rounded-xl shadow-sm p-8">
            @csrf

            @if (session('status') === 'request-submitted')
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    Request submitted successfully.
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            @php
                $firstLeaveTypeId = $leaveTypes->first()?->id;
                $balanceMap = $balances->keyBy(fn($b) => $b->type->id);
            @endphp

            <div
                class="space-y-6"
                x-data="{
                    types: @js($leaveTypes->mapWithKeys(fn($t) => [$t->id => ['unit' => $t->request_unit, 'allow_half_day' => (bool) $t->allow_half_day, 'allow_hour' => (bool) $t->allow_hour]])),
                    balances: @js($balances->mapWithKeys(fn($b) => [$b->type->id => ['remaining' => $b->remaining, 'allocated' => $b->allocated]])),
                    selectedId: @js($firstLeaveTypeId),
                    unit: 'day',
                    allowHalfDay: false,
                    allowHour: false,
                    startDate: '',
                    endDate: '',
                    requestedHours: '',
                    halfDayPeriod: 'am',
                    previewDays: 0,
                    previewError: '',
                    previewTimer: null,
                    init() {
                        this.setUnit();
                        this.$watch('selectedId', () => { this.setUnit(); this.queuePreview(); });
                        this.$watch('unit', () => this.queuePreview());
                        this.$watch('startDate', () => this.queuePreview());
                        this.$watch('endDate', () => this.queuePreview());
                        this.$watch('requestedHours', () => this.queuePreview());
                        this.$watch('halfDayPeriod', () => this.queuePreview());
                    },
                    setUnit() {
                        const t = this.types[this.selectedId];
                        if (t) { this.unit = t.unit; this.allowHalfDay = !!t.allow_half_day; this.allowHour = !!t.allow_hour; }
                        if (this.unit === 'day') this.requestedHours = '';
                    },
                    businessDays() {
                        if (!this.startDate || !this.endDate) return 0;
                        const s = new Date(this.startDate), e = new Date(this.endDate);
                        if (s > e) return 0;
                        let c = 0; const d = new Date(s);
                        while (d <= e) { const day = d.getDay(); if (day !== 0 && day !== 6) c++; d.setDate(d.getDate() + 1); }
                        return c;
                    },
                    requestedDays() {
                        if (this.unit === 'half_day') return 0.5;
                        if (this.unit === 'hour') { const h = parseFloat(this.requestedHours || 0); return h > 0 ? (h / {{ $hoursPerDay ?? 8 }}).toFixed(2) : 0; }
                        return this.businessDays();
                    },
                    queuePreview() { clearTimeout(this.previewTimer); this.previewTimer = setTimeout(() => this.fetchPreview(), 400); },
                    async fetchPreview() {
                        this.previewError = '';
                        if (!this.startDate || !this.endDate || !this.selectedId) { this.previewDays = 0; return; }
                        const p = new URLSearchParams({ leave_type_id: this.selectedId, start_date: this.startDate, end_date: this.endDate, request_unit: this.unit, requested_hours: this.requestedHours || '', half_day_period: this.halfDayPeriod || '' });
                        try {
                            const r = await fetch(`{{ route('employee.requests.preview') }}?${p}`);
                            if (!r.ok) { const j = await r.json(); this.previewError = j.message || 'Unable to calculate.'; this.previewDays = 0; return; }
                            const j = await r.json(); this.previewDays = j.days;
                        } catch (e) { this.previewError = 'Preview unavailable.'; this.previewDays = 0; }
                    },
                }"
            >
                {{-- Leave Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type *</label>
                    <select name="leave_type_id" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required x-model="selectedId" @change="setUnit()">
                        @foreach ($leaveTypes as $type)
                            @php $bal = $balanceMap->get($type->id); @endphp
                            <option value="{{ $type->id }}">
                                {{ $type->name }} — {{ $bal ? number_format($bal->remaining, 1) : '0.0' }} days remaining
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1.5 text-xs text-gray-500" x-text="'Balance: ' + (balances[selectedId]?.remaining ?? 0) + ' / ' + (balances[selectedId]?.allocated ?? 0) + ' days'"></p>
                    @error('leave_type_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="reason" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 h-24" placeholder="Reason for leave...">{{ old('reason') }}</textarea>
                    @error('reason') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Request Unit --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Request Unit</label>
                    <select name="request_unit" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" x-model="unit">
                        <option value="day">Full Day</option>
                        <option value="half_day" x-show="allowHalfDay">Half Day</option>
                        <option value="hour" x-show="allowHour">Hours</option>
                    </select>
                    @error('request_unit') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Date From / To --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From *</label>
                        <input type="date" name="start_date" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required x-model="startDate" />
                        @error('start_date') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date To *</label>
                        <input type="date" name="end_date" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required x-model="endDate" />
                        @error('end_date') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Half Day Period (conditional) --}}
                <div x-show="unit === 'half_day'" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Half-Day Period</label>
                    <select name="half_day_period" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" x-model="halfDayPeriod">
                        <option value="am">Morning</option>
                        <option value="pm">Afternoon</option>
                    </select>
                    @error('half_day_period') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Hours (conditional) --}}
                <div x-show="unit === 'hour'" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Requested Hours</label>
                    <input type="number" name="requested_hours" step="0.5" min="0.5" class="w-full px-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. 2" x-model="requestedHours" />
                    @error('requested_hours') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Duration Preview --}}
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Duration</span>
                        <span class="text-lg font-semibold text-gray-900" x-text="previewDays + ' days'"></span>
                    </div>
                    <p x-show="previewError" class="mt-1 text-xs text-red-600" x-text="previewError"></p>
                    <p class="mt-1 text-xs text-gray-400" x-show="!previewError && previewDays > 0">Business days calculated automatically.</p>
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('employee.dashboard') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm">Submit Request</button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.erp>
