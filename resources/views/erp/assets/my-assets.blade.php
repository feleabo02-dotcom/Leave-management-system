<x-layouts.erp :title="'My Assets'">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Assigned Assets</h1>
        <p class="text-sm text-gray-500 mt-0.5">List of company equipment currently assigned to you.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($assets as $asset)
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition group">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                        <i class="ph {{ match($asset->category->code) {
                            'LAPTOP' => 'ph-laptop',
                            'MOBILE' => 'ph-phone',
                            'DESKTOP' => 'ph-desktop',
                            'FURNITURE' => 'ph-armchair',
                            default => 'ph-cube',
                        } }} text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $asset->name }}</h3>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $asset->code }}</p>
                    </div>
                </div>
                <div class="space-y-2 mb-6">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Category</span>
                        <span class="font-medium text-gray-900">{{ $asset->category->name }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Serial No.</span>
                        <span class="font-medium text-gray-900">{{ $asset->serial_number ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Assigned On</span>
                        <span class="font-medium text-gray-900">{{ $asset->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-50 flex gap-2">
                    <a href="{{ route('assets.show', $asset) }}" class="flex-1 py-2 text-center text-xs font-bold text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition">
                        View Details
                    </a>
                    <button class="px-3 py-2 text-gray-400 hover:text-red-600 transition" title="Report Issue">
                        <i class="ph ph-warning-circle text-lg"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center bg-white border border-dashed border-gray-300 rounded-2xl">
                <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ph ph-cube text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">No assets assigned</h3>
                <p class="text-sm text-gray-400">You don't have any company equipment currently assigned to your profile.</p>
            </div>
        @endforelse
    </div>
</x-layouts.erp>
