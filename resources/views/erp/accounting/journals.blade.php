<x-layouts.erp :title="'Journals'">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Journals</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage accounting journals and entries.</p>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Name</th>
                        <th class="px-5 py-4 border-b border-gray-100">Code</th>
                        <th class="px-5 py-4 border-b border-gray-100">Type</th>
                        <th class="px-5 py-4 border-b border-gray-100 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($journals as $journal)
                        <tr class="hover:bg-gray-50 transition cursor-pointer" data-href="{{ route('accounting.journals.entries', $journal) }}" onclick="window.location=this.dataset.href">
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $journal->name }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $journal->code }}</td>
                            <td class="px-5 py-4 text-sm capitalize text-gray-600">{{ $journal->type }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('accounting.journals.entries', $journal) }}" class="text-gray-400 hover:text-indigo-600 transition p-1.5 inline-block"><i class="ph ph-eye text-lg"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-gray-400 text-sm">No journals found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Journal Entries Section --}}
    @if(isset($selectedJournal))
        <div class="mt-6 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="ph ph-list-numbers text-indigo-500"></i> Entries: {{ $selectedJournal->name }}
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                            <th class="px-5 py-3 border-b border-gray-100">Date</th>
                            <th class="px-5 py-3 border-b border-gray-100">Number</th>
                            <th class="px-5 py-3 border-b border-gray-100">Reference</th>
                            <th class="px-5 py-3 border-b border-gray-100">Debit</th>
                            <th class="px-5 py-3 border-b border-gray-100">Credit</th>
                            <th class="px-5 py-3 border-b border-gray-100">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($selectedJournal->entries as $entry)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3 text-sm text-gray-600">{{ $entry->date?->format('M d, Y') }}</td>
                                <td class="px-5 py-3 text-sm font-bold text-gray-900">{{ $entry->code }}</td>
                                <td class="px-5 py-3 text-sm text-gray-600">{{ $entry->reference ?? '—' }}</td>
                                <td class="px-5 py-3 text-sm text-gray-900">${{ number_format($entry->debit, 2) }}</td>
                                <td class="px-5 py-3 text-sm text-gray-900">${{ number_format($entry->credit, 2) }}</td>
                                <td class="px-5 py-3">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full uppercase {{ $entry->state === 'posted' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $entry->state }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-6 text-center text-sm text-gray-400">No entries for this journal.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-layouts.erp>
