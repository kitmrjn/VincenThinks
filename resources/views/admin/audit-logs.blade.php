<x-admin-layout>
    <div class="space-y-6">
        {{-- Header Section --}}
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Admin Audit Logs</h2>
                <p class="text-gray-500 text-sm">Review all administrative actions and security-related events.</p>
            </div>
            <div class="bg-maroon-50 px-4 py-2 rounded-lg border border-maroon-100">
                <span class="text-maroon-700 font-bold">{{ $logs->total() }}</span>
                <span class="text-maroon-600 text-sm">Total Actions Logged</span>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Admin</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Target User</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Details</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Timestamp & IP</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            {{-- Admin Column --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-maroon-700 flex items-center justify-center text-white text-xs font-bold mr-3 shadow-sm">
                                        {{ substr($log->admin->name, 0, 1) }}
                                    </div>
                                    <div class="text-sm font-bold text-gray-900">{{ $log->admin->name }}</div>
                                </div>
                            </td>

                            {{-- Action Badge Column --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-bold rounded-full 
                                    {{ Str::contains($log->action, ['Delete', 'Ban']) ? 'bg-red-100 text-red-800' : 
                                       (Str::contains($log->action, ['Create', 'Promote', 'Verify']) ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $log->action }}
                                </span>
                            </td>

                            {{-- Target User Column --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700 font-medium">
                                    {{ $log->target_user_name }}
                                </div>
                            </td>

                            {{-- Details Column --}}
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 italic max-w-xs truncate" title="{{ $log->details }}">
                                    {{ $log->details ?? 'No additional info' }}
                                </div>
                            </td>

                            {{-- Date & IP Column --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                {{-- Date and Time --}}
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $log->created_at->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-gray-500 mb-1">
                                    {{ $log->created_at->format('h:i A') }}
                                </div>

                                {{-- IP Badge --}}
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 font-mono border border-gray-200">
                                    {{ $log->ip_address }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class='bx bx-history text-5xl mb-3 text-gray-300'></i>
                                    <p class="text-lg font-medium text-gray-400">The audit log is currently empty.</p>
                                    <p class="text-sm text-gray-300">Actions will appear here as admins manage the system.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $logs->links('partials.pagination') }}
        </div>
    </div>
</x-admin-layout>