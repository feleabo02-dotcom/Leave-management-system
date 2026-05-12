<x-layouts.erp :title="'CRM - Pipeline'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Opportunity Pipeline</h1>
            <p class="text-sm text-gray-500 mt-0.5">Track your sales progress through the pipeline stages.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('crm.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition shadow-sm flex items-center gap-2">
                <i class="ph ph-users"></i> All Customers
            </a>
        </div>
    </div>

    <div class="flex gap-4 overflow-x-auto pb-8 min-h-[600px] items-start">
        @foreach($stages as $stage)
            <div class="flex-shrink-0 w-80 bg-gray-50 border border-gray-100 rounded-2xl p-4">
                <div class="flex items-center justify-between mb-4 px-2">
                    <h3 class="text-xs font-black uppercase tracking-widest text-gray-400">{{ $stage }}</h3>
                    <span class="bg-white px-2 py-0.5 rounded-full border border-gray-200 text-[10px] font-bold text-gray-500">
                        {{ $opportunities->get($stage)?->count() ?? 0 }}
                    </span>
                </div>
                
                <div class="space-y-3">
                    @forelse($opportunities->get($stage) ?? [] as $opp)
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md hover:border-indigo-200 transition group relative">
                            <div class="mb-3">
                                <h4 class="text-sm font-bold text-gray-900 leading-tight group-hover:text-indigo-600 transition">{{ $opp->title }}</h4>
                                <p class="text-[10px] text-gray-400 font-medium">{{ $opp->customer->name }}</p>
                            </div>
                            <div class="flex items-center justify-between mt-auto">
                                <div class="text-[10px] font-bold text-indigo-600">
                                    ${{ number_format($opp->expected_revenue, 0) }}
                                </div>
                                <div class="flex -space-x-2">
                                    <div class="w-5 h-5 rounded-full bg-indigo-100 border-2 border-white flex items-center justify-center text-[8px] font-bold text-indigo-700" title="{{ $opp->assignee->name }}">
                                        {{ strtoupper(substr($opp->assignee->name, 0, 1)) }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3 pt-3 border-t border-gray-50 flex items-center justify-between">
                                <div class="w-full bg-gray-100 h-1 rounded-full overflow-hidden">
                                    <div class="bg-indigo-600 h-full" style="width: {{ $opp->probability }}%"></div>
                                </div>
                                <span class="ml-2 text-[8px] font-black text-gray-400">{{ $opp->probability }}%</span>
                            </div>

                            <a href="{{ route('crm.opportunities.show', $opp) }}" class="absolute inset-0"></a>
                        </div>
                    @empty
                        <div class="py-12 border-2 border-dashed border-gray-100 rounded-xl flex items-center justify-center">
                            <p class="text-[10px] font-bold text-gray-300 uppercase">No Items</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</x-layouts.erp>
