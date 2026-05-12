<x-layouts.erp :title="'Leaderboard'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Leaderboard</h1>
            <p class="text-sm text-gray-500 mt-0.5">Users ranked by karma score.</p>
        </div>
    </div>

    {{-- Leaderboard Table --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Rank</th>
                        <th class="px-5 py-4 border-b border-gray-100">User</th>
                        <th class="px-5 py-4 border-b border-gray-100">Karma Score</th>
                        <th class="px-5 py-4 border-b border-gray-100">Current Rank Badge</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($leaderboard as $index => $entry)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    @if($index === 0)
                                        <span class="w-7 h-7 rounded-full bg-yellow-100 text-yellow-700 flex items-center justify-center font-bold text-xs">
                                            <i class="ph ph-crown text-sm"></i>
                                        </span>
                                    @elseif($index === 1)
                                        <span class="w-7 h-7 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center font-bold text-xs">2</span>
                                    @elseif($index === 2)
                                        <span class="w-7 h-7 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xs">3</span>
                                    @else
                                        <span class="w-7 h-7 rounded-full bg-gray-50 text-gray-400 flex items-center justify-center font-bold text-xs">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-[10px]">
                                        {{ strtoupper(substr($entry->user->name ?? 'NA', 0, 2)) }}
                                    </div>
                                    <span class="text-sm font-bold text-gray-900">{{ $entry->user->name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-sm font-bold text-indigo-600">{{ number_format($entry->new_value ?? $entry->gain ?? 0) }}</span>
                            </td>
                            <td class="px-5 py-4">
                                @php
                                    $karmaScore = $entry->new_value ?? $entry->gain ?? 0;
                                    $rankBadge = \App\Models\GamificationKarmaRank::where('karma_min', '<=', $karmaScore)->orderBy('karma_min', 'desc')->first();
                                @endphp
                                @if($rankBadge)
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-100 text-indigo-700 uppercase">{{ $rankBadge->name }}</span>
                                @else
                                    <span class="text-xs text-gray-400 italic">Unranked</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-gray-400 text-sm">No karma tracking data yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.erp>
