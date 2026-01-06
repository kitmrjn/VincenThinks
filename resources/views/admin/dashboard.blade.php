<x-admin-layout>
    {{-- SECTION 1: Welcome Banner --}}
    <div class="flex justify-between items-center mb-8 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <div>
            <h1 class="text-2xl text-gray-800 font-light mb-1">
                Welcome back, <span class="font-bold text-maroon-700">{{ Auth::user()->name }}</span>!
            </h1>
            <p class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</p>
        </div>
        <div class="hidden md:block">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <span class="w-2 h-2 mr-2 bg-green-500 rounded-full"></span> System Operational
            </span>
        </div>
    </div>

    {{-- SECTION 2: Action Items Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-red-50 border border-red-100 p-6 rounded-lg shadow-sm flex items-center justify-between">
            <div>
                <p class="text-red-500 text-xs font-bold uppercase tracking-wider mb-1">Pending Review</p>
                <p class="text-3xl font-bold text-red-700">{{ $pendingReports }}</p>
                <p class="text-xs text-red-400 mt-1">Reported items</p>
            </div>
            <div class="p-3 bg-red-100 rounded-full text-red-600">
                <i class='bx bx-error-circle text-2xl'></i>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-100 p-6 rounded-lg shadow-sm flex items-center justify-between">
            <div>
                <p class="text-blue-500 text-xs font-bold uppercase tracking-wider mb-1">Growth Today</p>
                <p class="text-3xl font-bold text-blue-700">{{ $newUsersToday }}</p>
                <p class="text-xs text-blue-400 mt-1">New registrations</p>
            </div>
            <div class="p-3 bg-blue-100 rounded-full text-blue-600">
                <i class='bx bx-user-plus text-2xl'></i>
            </div>
        </div>

        <div class="bg-green-50 border border-green-100 p-6 rounded-lg shadow-sm flex items-center justify-between">
            <div>
                <p class="text-green-600 text-xs font-bold uppercase tracking-wider mb-1">Total Content</p>
                <p class="text-3xl font-bold text-green-700">{{ $totalQuestions }}</p>
                <p class="text-xs text-green-500 mt-1">Active questions</p>
            </div>
            <div class="p-3 bg-green-100 rounded-full text-green-600">
                <i class='bx bx-message-square-dots text-2xl'></i>
            </div>
        </div>
    </div>

    {{-- SECTION 3: Main Layout (2 Columns) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Left Column: Quick Actions (Span 2) --}}
        <div class="lg:col-span-2">
            <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                <i class='bx bx-bolt-circle mr-2 text-maroon-700'></i> Quick Actions
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('admin.users') }}" class="group p-6 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:border-maroon-700 transition flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 group-hover:bg-maroon-50 group-hover:text-maroon-700 transition mb-3">
                        <i class='bx bxs-user-account text-2xl'></i>
                    </div>
                    <h4 class="font-medium text-gray-800 group-hover:text-maroon-700">Manage Users</h4>
                    <p class="text-xs text-gray-500 mt-1">Verify, Ban, Edit</p>
                </a>

                <a href="{{ route('admin.departments') }}" class="group p-6 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:border-maroon-700 transition flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 group-hover:bg-maroon-50 group-hover:text-maroon-700 transition mb-3">
                        <i class='bx bx-building-house text-2xl'></i>
                    </div>
                    <h4 class="font-medium text-gray-800 group-hover:text-maroon-700">Add Department</h4>
                    <p class="text-xs text-gray-500 mt-1">Create new units</p>
                </a>

                <a href="{{ route('admin.analytics') }}" class="group p-6 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:border-maroon-700 transition flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 group-hover:bg-maroon-50 group-hover:text-maroon-700 transition mb-3">
                        <i class='bx bx-bar-chart-alt-2 text-2xl'></i>
                    </div>
                    <h4 class="font-medium text-gray-800 group-hover:text-maroon-700">View Analytics</h4>
                    <p class="text-xs text-gray-500 mt-1">Stats & Trends</p>
                </a>

                <a href="{{ route('admin.settings.general') }}" class="group p-6 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:border-maroon-700 transition flex flex-col items-center text-center">
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 group-hover:bg-maroon-50 group-hover:text-maroon-700 transition mb-3">
                        <i class='bx bx-cog text-2xl'></i>
                    </div>
                    <h4 class="font-medium text-gray-800 group-hover:text-maroon-700">System Settings</h4>
                    <p class="text-xs text-gray-500 mt-1">Config & Rules</p>
                </a>
            </div>
        </div>

        {{-- Right Column: Recent Activity (Span 1) --}}
        <div>
            <h3 class="text-lg font-medium text-gray-800 mb-4 flex items-center">
                <i class='bx bx-history mr-2 text-gray-400'></i> Recent Activity
            </h3>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <ul class="divide-y divide-gray-100">
                    @forelse($recentLogs as $log)
                        <li class="p-4 hover:bg-gray-50 transition">
                            <div class="flex items-start">
                                <div class="mt-1 mr-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 text-xs font-bold">
                                        {{ substr($log->admin->name ?? 'Sys', 0, 1) }}
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">
                                        {{ $log->action }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">
                                        {{ $log->details ?? 'No details provided' }}
                                    </p>
                                    <div class="mt-1 flex items-center text-[10px] text-gray-400">
                                        <i class='bx bx-time-five mr-1'></i>
                                        {{ $log->created_at->diffForHumans() }}
                                        <span class="mx-1">•</span>
                                        {{ $log->admin->name ?? 'System' }}
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="p-8 text-center text-gray-400 text-sm">
                            No recent activity logged.
                        </li>
                    @endforelse
                </ul>
                <div class="p-3 border-t border-gray-100 text-center bg-gray-50">
                    <a href="{{ route('admin.audit_logs') }}" class="text-xs font-medium text-maroon-700 hover:underline">View All Logs &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>