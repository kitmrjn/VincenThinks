<x-admin-layout>
    <div class="space-y-6">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Admin Audit Logs</h2>
                <p class="text-gray-500 text-sm">Review all administrative actions and security-related events.</p>
            </div>
            <div class="bg-maroon-50 px-4 py-2 rounded-lg border border-maroon-100 self-start md:self-auto">
                <span class="text-maroon-700 font-bold">{{ $logs->total() }}</span>
                <span class="text-maroon-600 text-sm">Total Actions Logged</span>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Added table-fixed for mobile to prevent overflow --}}
            <table class="w-full divide-y divide-gray-200 table-fixed md:table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        {{-- Admin: 40% width on mobile --}}
                        <th class="px-4 py-3 md:px-6 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-5/12 md:w-auto">Admin & Action</th>
                        
                        {{-- Action: Hidden on mobile (merged into Admin) --}}
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Action</th>
                        
                        {{-- Target: 35% width on mobile --}}
                        <th class="px-2 py-3 md:px-6 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-4/12 md:w-auto">Target</th>
                        
                        {{-- Details: Hidden on mobile --}}
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Details</th>
                        
                        {{-- Time: 25% width on mobile --}}
                        <th class="px-4 py-3 md:px-6 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-3/12 md:w-auto">Time</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            
                            {{-- Admin Column (Merged Action on Mobile) --}}
                            <td class="px-4 py-4 md:px-6 align-top">
                                <div class="flex items-start">
                                    {{-- Avatar: Smaller on mobile --}}
                                    <div class="h-8 w-8 rounded-full bg-maroon-700 flex-shrink-0 flex items-center justify-center text-white text-xs font-bold mr-2 md:mr-3 shadow-sm hidden xs:flex">
                                        {{ substr($log->admin->name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-sm font-bold text-gray-900 truncate">{{ $log->admin->name }}</div>
                                        
                                        {{-- Mobile-Only Action Badge --}}
                                        <div class="md:hidden mt-1">
                                            <span class="px-2 py-0.5 inline-flex text-[10px] leading-4 font-bold rounded-full border 
                                                {{ Str::contains($log->action, ['Delete', 'Ban']) ? 'bg-red-50 text-red-700 border-red-100' : 
                                                   (Str::contains($log->action, ['Create', 'Promote', 'Verify']) ? 'bg-green-50 text-green-700 border-green-100' : 'bg-blue-50 text-blue-700 border-blue-100') }}">
                                                {{ $log->action }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Action Badge Column (Desktop Only) --}}
                            <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell align-top">
                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-bold rounded-full 
                                    {{ Str::contains($log->action, ['Delete', 'Ban']) ? 'bg-red-100 text-red-800' : 
                                       (Str::contains($log->action, ['Create', 'Promote', 'Verify']) ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $log->action }}
                                </span>
                            </td>

                            {{-- Target User Column --}}
                            <td class="px-2 py-4 md:px-6 whitespace-nowrap align-top">
                                <div class="text-sm text-gray-700 font-medium truncate">
                                    {{ $log->target_user_name }}
                                </div>
                                {{-- Optional: Show very short details on mobile if needed --}}
                                <div class="text-[10px] text-gray-400 md:hidden truncate max-w-[100px]">
                                    {{ $log->details }}
                                </div>
                            </td>

                            {{-- Details Column (Desktop Only) --}}
                            <td class="px-6 py-4 hidden md:table-cell align-top">
                                <div class="text-sm text-gray-500 italic max-w-xs truncate" title="{{ $log->details }}">
                                    {{ $log->details ?? 'No additional info' }}
                                </div>
                            </td>

                            {{-- Date & IP Column --}}
                            <td class="px-4 py-4 md:px-6 whitespace-nowrap text-right align-top">
                                <div class="flex flex-col items-end">
                                    {{-- Date --}}
                                    <div class="text-xs md:text-sm font-medium text-gray-900">
                                        {{ $log->created_at->format('M d') }}
                                    </div>
                                    {{-- Time --}}
                                    <div class="text-[10px] md:text-xs text-gray-500 mb-1">
                                        {{ $log->created_at->format('h:i A') }}
                                    </div>

                                    {{-- IP Badge (Desktop Only) --}}
                                    <span class="hidden md:inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 font-mono border border-gray-200">
                                        {{ $log->ip_address }}
                                    </span>
                                </div>
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