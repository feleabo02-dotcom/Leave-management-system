<x-layouts.erp :title="'Opportunity - ' . $opportunity->title">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('crm.pipeline') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $opportunity->title }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Customer: {{ $opportunity->customer->name }}</p>
        </div>
        <div class="flex gap-2">
            @if($opportunity->stage !== 'won')
                <form action="{{ route('crm.convert', $opportunity) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold hover:bg-emerald-700 transition flex items-center gap-2 shadow-lg shadow-emerald-200">
                        <i class="ph-bold ph-receipt"></i>
                        New Quotation
                    </button>
                </form>
            @endif
            <select onchange="window.location.href=this.value" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-medium bg-white outline-none">
                <option>Change Stage...</option>
                @foreach(['new', 'qualified', 'proposition', 'won', 'lost'] as $st)
                    <option value="{{ route('crm.opportunities.stage', [$opportunity, 'stage' => $st]) }}" {{ $opportunity->stage === $st ? 'disabled font-bold' : '' }}>
                        Mark as {{ ucfirst($st) }}
                    </option>
                @endforeach
            </select>
            <form id="stageForm" action="{{ route('crm.opportunities.stage', $opportunity) }}" method="POST" class="hidden">@csrf <input type="hidden" name="stage" id="stageInput"></form>
            <script>
                document.querySelector('select').onchange = function() {
                    const url = new URL(this.value);
                    const stage = url.searchParams.get('stage');
                    document.getElementById('stageInput').value = stage;
                    document.getElementById('stageForm').submit();
                }
            </script>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Info Card --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Opportunity Stats</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Expected Revenue</p>
                        <p class="text-xl font-black text-gray-900">${{ number_format($opportunity->expected_revenue, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Probability</p>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-indigo-600 h-full" style="width: {{ $opportunity->probability }}%"></div>
                            </div>
                            <span class="text-sm font-bold text-indigo-600">{{ $opportunity->probability }}%</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Stage</p>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase bg-indigo-100 text-indigo-700">
                            {{ $opportunity->stage }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">Expected Closing</p>
                        <p class="text-sm font-medium text-gray-900">{{ $opportunity->closing_date ? $opportunity->closing_date->format('M d, Y') : '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-6 shadow-sm">
                <h3 class="text-xs font-bold text-indigo-400 uppercase tracking-widest mb-4">Customer Info</h3>
                <p class="text-sm font-bold text-indigo-900">{{ $opportunity->customer->name }}</p>
                <p class="text-xs text-indigo-600 mb-4">{{ $opportunity->customer->email }}</p>
                <a href="{{ route('crm.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">View Customer Profile</a>
            </div>
        </div>

        {{-- Activity Log --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Log Form --}}
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Log New Activity</h3>
                <form action="{{ route('crm.opportunities.activity', $opportunity) }}" method="POST">
                    @csrf
                    <div class="flex gap-4 mb-4">
                        <select name="type" required class="px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white outline-none">
                            <option value="call">Phone Call</option>
                            <option value="email">Email</option>
                            <option value="meeting">Meeting</option>
                            <option value="task">Task</option>
                        </select>
                        <input type="text" name="notes" placeholder="What happened? Summarize the activity..." required class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 transition">Log</button>
                    </div>
                </form>
            </div>

            {{-- Activity Timeline --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Communication History</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-100 before:to-transparent">
                        @forelse($opportunity->activities as $activity)
                            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                                    <i class="ph {{ match($activity->type) {
                                        'call' => 'ph-phone',
                                        'email' => 'ph-envelope',
                                        'meeting' => 'ph-users',
                                        'task' => 'ph-check-square',
                                        default => 'ph-dots-three',
                                    } }} text-indigo-600"></i>
                                </div>
                                <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded border border-gray-100 bg-gray-50">
                                    <div class="flex items-center justify-between space-x-2 mb-1">
                                        <div class="font-bold text-gray-900 capitalize text-sm">{{ $activity->type }}</div>
                                        <time class="font-medium text-[10px] text-gray-400">{{ $activity->date->diffForHumans() }}</time>
                                    </div>
                                    <div class="text-xs text-gray-500 italic">"{{ $activity->notes }}"</div>
                                    <div class="mt-2 text-[8px] font-black uppercase text-gray-400">By {{ $activity->user->name }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-gray-400 text-sm">No activities logged yet. Start by logging a call or email.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.erp>
