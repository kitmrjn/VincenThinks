<x-admin-layout>
    <div class="mb-10">
        <h1 class="text-3xl font-light text-gray-800 mb-6">Platform Analytics</h1>
        
        {{-- 1. Stat Cards Row --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 flex items-center">
                <div class="p-3 rounded-full bg-blue-50 text-blue-600 mr-4"><i class='bx bx-user text-2xl'></i></div>
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Users</p>
                    <p class="text-xl font-bold text-gray-800">{{ $stats['total_users'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 flex items-center">
                <div class="p-3 rounded-full bg-orange-50 text-orange-600 mr-4"><i class='bx bx-question-mark text-2xl'></i></div>
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Questions</p>
                    <p class="text-xl font-bold text-gray-800">{{ $stats['total_questions'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 flex items-center">
                <div class="p-3 rounded-full bg-green-50 text-green-600 mr-4"><i class='bx bx-check-circle text-2xl'></i></div>
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Solved</p>
                    <p class="text-xl font-bold text-gray-800">{{ $stats['total_solved'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 flex items-center">
                <div class="p-3 rounded-full bg-indigo-50 text-indigo-600 mr-4"><i class='bx bx-book-open text-2xl'></i></div>
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Courses</p>
                    <p class="text-xl font-bold text-gray-800">{{ $stats['total_courses'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 flex items-center">
                <div class="p-3 rounded-full bg-teal-50 text-teal-600 mr-4"><i class='bx bx-category text-2xl'></i></div>
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Categories</p>
                    <p class="text-xl font-bold text-gray-800">{{ $stats['total_categories'] }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200 flex items-center">
                <div class="p-3 rounded-full bg-purple-50 text-purple-600 mr-4"><i class='bx bx-buildings text-2xl'></i></div>
                <div>
                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Departments</p>
                    <p class="text-xl font-bold text-gray-800">{{ $stats['total_departments'] }}</p>
                </div>
            </div>

            <div class="bg-red-50 rounded-lg p-6 shadow-sm border border-red-200 flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4"><i class='bx bx-error text-2xl'></i></div>
                <div>
                    <p class="text-xs text-red-400 font-bold uppercase tracking-wider">Pending Reports</p>
                    <p class="text-xl font-bold text-red-700">{{ $stats['pending_reports'] }}</p>
                </div>
            </div>
        </div>

        {{-- 2. Main Charts Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-gray-800 font-medium mb-4">Question Growth (7 Days)</h3>
                <div class="h-64">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-gray-800 font-medium mb-4">Questions by Category</h3>
                <div class="h-64">
                    <canvas id="distChart"></canvas>
                </div>
            </div>
        </div>

        {{-- 3. Deep Dive Row (Resolution & Contributors) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <h3 class="text-gray-800 font-medium mb-4">Resolution Status</h3>
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

    {{-- Chart Initialization Script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data passed from Controller
            const growthData = @json($charts['growth']);
            const distData = @json($charts['distribution']);
            const resData = @json($charts['resolution']);

            // 1. Growth Line Chart
            new Chart(document.getElementById('growthChart'), {
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
            new Chart(document.getElementById('distChart'), {
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
            new Chart(document.getElementById('resolutionChart'), {
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
        });
    </script>
</x-admin-layout>