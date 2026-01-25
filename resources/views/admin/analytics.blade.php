<x-admin-layout>
    <div class="mb-10">
        {{-- Header & Range Filters --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
            <h1 class="text-3xl font-light text-gray-800">Platform Analytics</h1>
            
            <div class="mt-4 md:mt-0 bg-white p-1 rounded-lg border border-gray-200 inline-flex shadow-sm">
                <button onclick="setRange('day')" id="btn-day" class="px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors">
                    Day
                </button>
                <button onclick="setRange('week')" id="btn-week" class="px-4 py-2 text-sm font-medium rounded-md bg-maroon-50 text-maroon-700 shadow-sm focus:outline-none transition-colors">
                    Week
                </button>
                <button onclick="setRange('month')" id="btn-month" class="px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors">
                    Month
                </button>
                <button onclick="setRange('year')" id="btn-year" class="px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-800 hover:bg-gray-50 focus:outline-none transition-colors">
                    Year
                </button>
            </div>
        </div>
        
        {{-- 1. Stat Cards Row --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 flex items-center">
                <div class="p-3 rounded-full bg-blue-50 text-blue-600 mr-4"><i class='bx bx-user text-2xl'></i></div>
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wider" id="label-users">Users</p>
                    <p class="text-xl font-bold text-gray-800" id="stat-total-users">{{ $stats['total_users'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 flex items-center">
                <div class="p-3 rounded-full bg-orange-50 text-orange-600 mr-4"><i class='bx bx-question-mark text-2xl'></i></div>
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wider" id="label-questions">Questions</p>
                    <p class="text-xl font-bold text-gray-800" id="stat-total-questions">{{ $stats['total_questions'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 flex items-center">
                <div class="p-3 rounded-full bg-green-50 text-green-600 mr-4"><i class='bx bx-check-circle text-2xl'></i></div>
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wider" id="label-solved">Solved</p>
                    <p class="text-xl font-bold text-gray-800" id="stat-total-solved">{{ $stats['total_solved'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 flex items-center">
                <div class="p-3 rounded-full bg-indigo-50 text-indigo-600 mr-4"><i class='bx bx-book-open text-2xl'></i></div>
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Courses</p>
                    <p class="text-xl font-bold text-gray-800" id="stat-total-courses">{{ $stats['total_courses'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 flex items-center">
                <div class="p-3 rounded-full bg-teal-50 text-teal-600 mr-4"><i class='bx bx-category text-2xl'></i></div>
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Categories</p>
                    <p class="text-xl font-bold text-gray-800" id="stat-total-categories">{{ $stats['total_categories'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 flex items-center">
                <div class="p-3 rounded-full bg-purple-50 text-purple-600 mr-4"><i class='bx bx-buildings text-2xl'></i></div>
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Departments</p>
                    <p class="text-xl font-bold text-gray-800" id="stat-total-departments">{{ $stats['total_departments'] }}</p>
                </div>
            </div>

            <div class="bg-red-50 rounded-lg p-6 shadow-sm border border-red-200 flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4"><i class='bx bx-error text-2xl'></i></div>
                <div>
                    <p class="text-xs text-red-400 font-bold uppercase tracking-wider">Pending Reports</p>
                    <p class="text-xl font-bold text-red-700" id="stat-pending-reports">{{ $stats['pending_reports'] }}</p>
                </div>
            </div>
        </div>

        {{-- 2. Main Charts Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-gray-800 font-medium">Question Growth</h3>
                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded" id="label-growth-range">Last 7 Days</span>
                </div>
                <div class="h-64">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-gray-800 font-medium">Questions by Category</h3>
                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded" id="label-dist-range">Last 7 Days</span>
                </div>
                <div class="h-64">
                    <canvas id="distChart"></canvas>
                </div>
            </div>
        </div>

        {{-- 3. Deep Dive Row (Resolution & Contributors) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-gray-800 font-medium">Resolution Status</h3>
                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded" id="label-res-range">Last 7 Days</span>
                </div>
                <div class="h-64">
                    <canvas id="resolutionChart"></canvas>
                </div>
            </div>

            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-gray-800 font-medium mb-4">Top Contributors (Most Answers)</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="text-xs text-gray-400 uppercase border-b border-gray-100">
                            <tr>
                                <th class="pb-3 font-normal">User</th>
                                <th class="pb-3 font-normal">Role</th>
                                <th class="pb-3 font-normal text-right">Answers Posted</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($topContributors as $contributor)
                            <tr class="group hover:bg-gray-50">
                                <td class="py-3 flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-maroon-700 text-white flex items-center justify-center text-xs mr-3">
                                        {{ substr($contributor->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">{{ $contributor->name }}</span>
                                </td>
                                <td class="py-3 text-sm text-gray-500">
                                    <span class="px-2 py-1 rounded text-xs {{ $contributor->is_admin ? 'bg-purple-100 text-purple-600' : ($contributor->member_type == 'teacher' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-500') }}">
                                        {{ $contributor->is_admin ? 'Admin' : ucfirst($contributor->member_type) }}
                                    </span>
                                </td>
                                <td class="py-3 text-right text-sm font-bold text-gray-700">{{ $contributor->answers_count }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 4. Trending Content Table --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-gray-800 font-medium">Trending Content (Most Viewed)</h3>
            </div>
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3 font-normal">Question Title</th>
                        <th class="px-6 py-3 font-normal">Category</th>
                        <th class="px-6 py-3 font-normal">Views</th>
                        <th class="px-6 py-3 font-normal text-right">Date Posted</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($trendingQuestions as $q)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('question.show', $q->id) }}" target="_blank" class="text-maroon-700 hover:underline font-medium text-sm">
                                {{ Str::limit($q->title, 50) }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                {{ $q->category->name ?? 'Uncategorized' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 flex items-center">
                            <i class='bx bx-show mr-2 text-gray-400'></i> {{ number_format($q->views) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 text-right">
                            {{ $q->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    {{-- Chart Initialization & Polling Script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let currentRange = 'week'; // Default

        // Exposed function to change range
        window.setRange = function(range) {
            currentRange = range;
            
            // Update UI Buttons
            ['day', 'week', 'month', 'year'].forEach(r => {
                const btn = document.getElementById('btn-' + r);
                if (r === range) {
                    btn.classList.add('bg-maroon-50', 'text-maroon-700', 'shadow-sm');
                    btn.classList.remove('text-gray-500', 'hover:bg-gray-50');
                } else {
                    btn.classList.remove('bg-maroon-50', 'text-maroon-700', 'shadow-sm');
                    btn.classList.add('text-gray-500', 'hover:bg-gray-50');
                }
            });

            // Update Labels
            const labelText = range === 'day' ? 'Last 24 Hours' : 
                              range === 'week' ? 'Last 7 Days' : 
                              range === 'month' ? 'Last 30 Days' : 'Last 12 Months';
            
            // Chart Labels
            if(document.getElementById('label-growth-range')) document.getElementById('label-growth-range').innerText = labelText;
            if(document.getElementById('label-dist-range')) document.getElementById('label-dist-range').innerText = labelText;
            if(document.getElementById('label-res-range')) document.getElementById('label-res-range').innerText = labelText;

            // Stat Card Labels (To indicate context)
            if(document.getElementById('label-users')) document.getElementById('label-users').innerText = 'New Users (' + (range === 'day' ? 'Today' : range) + ')';
            if(document.getElementById('label-questions')) document.getElementById('label-questions').innerText = 'New Questions (' + (range === 'day' ? 'Today' : range) + ')';
            if(document.getElementById('label-solved')) document.getElementById('label-solved').innerText = 'Solved (' + (range === 'day' ? 'Today' : range) + ')';

            // Trigger immediate update
            updateCharts(); 
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Data passed from Controller for initial render (Default: Week)
            const growthData = @json($charts['growth']);
            const distData = @json($charts['distribution']);
            const resData = @json($charts['resolution']);

            // 1. Growth Line Chart
            window.growthChart = new Chart(document.getElementById('growthChart'), {
                type: 'line',
                data: {
                    labels: growthData.labels,
                    datasets: [{
                        label: 'New Questions',
                        data: growthData.data,
                        borderColor: '#800000', 
                        backgroundColor: 'rgba(128, 0, 0, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 2. Distribution Doughnut Chart
            window.distChart = new Chart(document.getElementById('distChart'), {
                type: 'doughnut',
                data: {
                    labels: distData.labels,
                    datasets: [{
                        data: distData.data,
                        backgroundColor: [
                            '#800000', '#F59E0B', '#10B981', '#3B82F6', '#6366F1', '#8B5CF6'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'right', labels: { boxWidth: 10 } } }
                }
            });

            // 3. Resolution Doughnut Chart
            window.resolutionChart = new Chart(document.getElementById('resolutionChart'), {
                type: 'doughnut',
                data: {
                    labels: resData.labels,
                    datasets: [{
                        data: resData.data,
                        backgroundColor: [
                            '#10B981', // Green for Solved
                            '#E5E7EB'  // Gray for Unsolved
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%', // Thinner ring
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 10 } } }
                }
            });

            // --- Real-Time Polling Logic ---
            const updateInterval = 3000; // 3 seconds
            window.updateCharts = function() { // Expose to window for button clicks
                fetch("{{ route('admin.analytics.data') }}?range=" + currentRange)
                    .then(response => response.json())
                    .then(data => {
                        // 1. Update Text Stats (Filtered)
                        if (document.getElementById('stat-total-users')) document.getElementById('stat-total-users').innerText = data.stats.total_users;
                        if (document.getElementById('stat-total-questions')) document.getElementById('stat-total-questions').innerText = data.stats.total_questions;
                        if (document.getElementById('stat-total-solved')) document.getElementById('stat-total-solved').innerText = data.stats.total_solved;
                        if (document.getElementById('stat-total-courses')) document.getElementById('stat-total-courses').innerText = data.stats.total_courses;
                        if (document.getElementById('stat-total-categories')) document.getElementById('stat-total-categories').innerText = data.stats.total_categories;
                        if (document.getElementById('stat-total-departments')) document.getElementById('stat-total-departments').innerText = data.stats.total_departments;
                        if (document.getElementById('stat-pending-reports')) document.getElementById('stat-pending-reports').innerText = data.stats.pending_reports;

                        // 2. Update Charts (Filtered)
                        if (window.growthChart) {
                            window.growthChart.data.labels = data.charts.growth.labels;
                            window.growthChart.data.datasets[0].data = data.charts.growth.data;
                            window.growthChart.update();
                        }

                        if (window.distChart) {
                            window.distChart.data.labels = data.charts.distribution.labels;
                            window.distChart.data.datasets[0].data = data.charts.distribution.data;
                            window.distChart.update();
                        }

                        if (window.resolutionChart) {
                            window.resolutionChart.data.labels = data.charts.resolution.labels;
                            window.resolutionChart.data.datasets[0].data = data.charts.resolution.data;
                            window.resolutionChart.update();
                        }
                    })
                    .catch(error => console.error('Error fetching analytics:', error));
            }

            // Start polling
            setInterval(updateCharts, updateInterval);
        });
    </script>
</x-admin-layout>