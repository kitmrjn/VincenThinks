<x-admin-layout>
    {{-- Hidden input to track polling state --}}
    <input type="hidden" id="latest-event-id" value="{{ $latestEventId }}">
    <input type="hidden" id="poll-url" value="{{ route('admin.analytics.events') }}">

    {{-- SECTION 1: Welcome Banner --}}
    <div class="flex justify-between items-center mb-8 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <div>
            <h1 class="text-2xl text-gray-800 font-light mb-1">
                Welcome back, <span class="font-bold text-maroon-700">{{ Auth::user()->name }}</span>!
            </h1>
            <p class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</p>
        </div>
        <div class="hidden md:block">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800" id="system-status-badge">
                <span class="w-2 h-2 mr-2 bg-green-500 rounded-full animate-pulse"></span> System Operational
            </span>
        </div>
    </div>

    {{-- SECTION 2: Action Items Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-red-50 border border-red-100 p-6 rounded-lg shadow-sm flex items-center justify-between">
            <div>
                <p class="text-red-500 text-xs font-bold uppercase tracking-wider mb-1">Pending Review</p>
                <p class="text-3xl font-bold text-red-700" id="pending-count">{{ $pendingReports }}</p>
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

    {{-- SECTION 3: Main Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Left Column: Quick Actions --}}
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

        {{-- Right Column: Live Feed & Recent Activity --}}
        <div class="flex flex-col gap-6">
            
            {{-- [UPDATED] Live Signals Widget --}}
            <div class="bg-white rounded-lg shadow-sm border border-maroon-100 overflow-hidden relative">
                <div class="px-4 py-3 bg-maroon-50 border-b border-maroon-100 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-maroon-800 flex items-center">
                        <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse mr-2"></span> 
                        Live Signals
                    </h3>
                    <span class="text-[10px] text-maroon-400 uppercase tracking-wide">Real-Time</span>
                </div>
                <ul id="live-feed-list" class="max-h-64 overflow-y-auto p-0 text-sm">
                    @forelse($recentEvents as $event)
                        <li class="border-b border-gray-50 hover:bg-gray-50 transition duration-150 ease-in-out">
                            <a href="{{ $event->action_url }}" class="block p-3">
                                <div class="flex items-start">
                                    @php
                                        $icon = 'bx-info-circle text-gray-500';
                                        if ($event->type === 'ai_flagged') $icon = 'bx-error text-red-600';
                                        if ($event->type === 'report_filed') $icon = 'bx-flag text-orange-500';
                                        if ($event->type === 'new_question') $icon = 'bx-question-mark text-blue-500';
                                    @endphp
                                    <i class='bx {{ $icon }} mt-1 mr-2 text-lg'></i>
                                    <div>
                                        <p class="text-xs font-bold text-gray-800">{{ strtoupper(str_replace('_', ' ', $event->type)) }}</p>
                                        <p class="text-xs text-gray-600">{{ $event->message }}</p>
                                        <p class="text-[10px] text-gray-400 mt-1">{{ $event->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="p-4 text-center text-gray-400 italic text-xs" id="no-signals-msg">
                            Waiting for new system events...
                        </li>
                    @endforelse
                </ul>
            </div>

            {{-- Recent Activity (Audit Logs) --}}
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
    </div>

    {{-- REAL-TIME POLLING SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pollUrl = document.getElementById('poll-url').value;
            const liveList = document.getElementById('live-feed-list');
            const noSignalsMsg = document.getElementById('no-signals-msg');
            const pendingCounter = document.getElementById('pending-count');
            let latestId = document.getElementById('latest-event-id').value;

            function pollEvents() {
                fetch(`${pollUrl}?last_id=${latestId}`)
                    .then(response => response.json())
                    .then(data => {
                        // 1. Update Stats
                        if (data.stats) {
                            pendingCounter.innerText = data.stats.pending_reports + data.stats.pending_reviews;
                        }

                        // 2. Process New Events
                        if (data.events && data.events.length > 0) {
                            if (noSignalsMsg) noSignalsMsg.style.display = 'none';

                            data.events.forEach(event => {
                                // Update ID tracking
                                if (event.id > latestId) latestId = event.id;

                                // Create HTML Element
                                const li = document.createElement('li');
                                li.className = "border-b border-gray-50 animate-pulse bg-yellow-50"; 
                                
                                let icon = 'bx-info-circle text-gray-500';
                                if (event.type === 'ai_flagged') icon = 'bx-error text-red-600';
                                if (event.type === 'report_filed') icon = 'bx-flag text-orange-500';
                                if (event.type === 'new_question') icon = 'bx-question-mark text-blue-500';

                                // [UPDATED] Wrapped in <a> tag with action_url
                                li.innerHTML = `
                                    <a href="${event.action_url}" class="block p-3 hover:bg-gray-50 transition duration-150 ease-in-out">
                                        <div class="flex items-start">
                                            <i class='bx ${icon} mt-1 mr-2 text-lg'></i>
                                            <div>
                                                <p class="text-xs font-bold text-gray-800">${event.type.replace('_', ' ').toUpperCase()}</p>
                                                <p class="text-xs text-gray-600">${event.message}</p>
                                                <p class="text-[10px] text-gray-400 mt-1">Just now</p>
                                            </div>
                                        </div>
                                    </a>
                                `;

                                // Prepend
                                liveList.insertBefore(li, liveList.firstChild);

                                // Remove highlight after 2s
                                setTimeout(() => {
                                    li.classList.remove('animate-pulse', 'bg-yellow-50');
                                }, 2000);
                            });
                        }
                    })
                    .catch(err => console.error('Polling error:', err));
            }

            // Poll every 5 seconds
            setInterval(pollEvents, 5000);
        });
    </script>
</x-admin-layout>