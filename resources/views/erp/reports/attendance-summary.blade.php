<x-layouts.erp :title="'Attendance Summary'">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('reports.index') }}" class="p-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
            <i class="ph ph-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Attendance Summary</h1>
            <p class="text-sm text-gray-500 mt-0.5">Monthly attendance analytics.</p>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-5 py-4 border-b border-gray-100">Month / Year</th>
                        <th class="px-5 py-4 border-b border-gray-100">Total Present</th>
                        <th class="px-5 py-4 border-b border-gray-100">Total Absent</th>
                        <th class="px-5 py-4 border-b border-gray-100">Total Late</th>
                        <th class="px-5 py-4 border-b border-gray-100">Total Overtime (hrs)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($attendanceData as $data)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4 text-sm font-bold text-gray-900">{{ $data->month }}/{{ $data->year }}</td>
                            <td class="px-5 py-4 text-sm text-green-600 font-medium">{{ $data->total_present }}</td>
                            <td class="px-5 py-4 text-sm text-red-600 font-medium">{{ $data->total_absent }}</td>
                            <td class="px-5 py-4 text-sm text-orange-600 font-medium">{{ $data->total_late }}</td>
                            <td class="px-5 py-4 text-sm text-indigo-600 font-medium">{{ number_format($data->total_overtime_hours, 1) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">No attendance data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.erp>
