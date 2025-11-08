<x-admin-layout>
    <script src="https://unpkg.com/lucide@latest"></script> 
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
   <x-slot name="header">
    <div class="flex items-center space-x-2">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Overview
        </h2>
        <span class="text-sm text-gray-500" x-data="{ 
            currentTime: ''
        }" x-init="
            const updateTime = () => {
                const now = new Date();
                const options = { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true
                };
                currentTime = now.toLocaleString('en-US', options);
            };
            updateTime();
            setInterval(updateTime, 1000);
        ">
            | Last Update: <span x-text="currentTime"></span>
        </span>
    </div>
</x-slot>

    <div class="space-y-8 pb-6">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            @php
                $userChange = $dashboardData['totalUsersChange'] ?? 0;
                $userColor = $userChange >= 0 ? 'text-green-500' : 'text-red-500';
                $userArrow = $userChange >= 0 ? '<i data-lucide="arrow-up" class="w-4 h-4 mr-0.5"></i>' : '<i data-lucide="arrow-down" class="w-4 h-4 mr-0.5"></i>';
            @endphp
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-3">
                        <div class="p-2 bg-gray-100 rounded-full text-gray-600 border border-gray-200">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-extrabold text-gray-900">{{ number_format($dashboardData['totalUsers'] ?? 0) }}</p>
                            <p class="text-sm text-gray-500">Total Users</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold flex items-center mt-1 {{ $userColor }}">
                        {!! $userArrow !!} {{ abs($userChange) }}%
                    </span>
                </div>
            </div>

            @php
                $newUsersChange = $dashboardData['newUsersChange'] ?? 0;
                $newUsersColor = $newUsersChange >= 0 ? 'text-green-500' : 'text-red-500';
                $newUsersArrow = $newUsersChange >= 0 ? '<i data-lucide="arrow-up" class="w-4 h-4 mr-0.5"></i>' : '<i data-lucide="arrow-down" class="w-4 h-4 mr-0.5"></i>';
            @endphp
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-3">
                        <div class="p-2 bg-gray-100 rounded-full text-gray-600 border border-gray-200">
                            <i data-lucide="user-plus" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-extrabold text-gray-900">{{ $dashboardData['newUsersThisWeek'] ?? 0 }}</p>
                            <p class="text-sm text-gray-500">New Users (Week)</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold flex items-center mt-1 {{ $newUsersColor }}">
                        {!! $newUsersArrow !!} {{ abs($newUsersChange) }}%
                    </span>
                </div>
            </div>

            @php
                $activeReportsChange = $dashboardData['activeReportsChange'] ?? 0;
                $activeReportsColor = $activeReportsChange >= 0 ? 'text-red-500' : 'text-green-500';
                $activeReportsArrow = $activeReportsChange >= 0 ? '<i data-lucide="arrow-up" class="w-4 h-4 mr-0.5"></i>' : '<i data-lucide="arrow-down" class="w-4 h-4 mr-0.5"></i>';
            @endphp
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-3">
                        <div class="p-2 bg-gray-100 rounded-full text-gray-600 border border-gray-200">
                            <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-extrabold text-gray-900">{{ $dashboardData['activeReports'] ?? 0 }}</p>
                            <p class="text-sm text-gray-500">Active Reports (Pending)</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold flex items-center mt-1 {{ $activeReportsColor }}">
                        {!! $activeReportsArrow !!} {{ abs($activeReportsChange) }}%
                    </span>
                </div>
            </div>

            @php
                $resolvedChange = $dashboardData['resolvedReportsChange'] ?? 0;
                $resolvedColor = $resolvedChange >= 0 ? 'text-green-500' : 'text-red-500';
                $resolvedArrow = $resolvedChange >= 0 ? '<i data-lucide="arrow-up" class="w-4 h-4 mr-0.5"></i>' : '<i data-lucide="arrow-down" class="w-4 h-4 mr-0.5"></i>';
            @endphp
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-3">
                        <div class="p-2 bg-gray-100 rounded-full text-gray-600 border border-gray-200">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-extrabold text-gray-900">{{ $dashboardData['resolvedReports'] ?? 0 }}</p>
                            <p class="text-sm text-gray-500">Resolved Reports</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold flex items-center mt-1 {{ $resolvedColor }}">
                        {!! $resolvedArrow !!} {{ abs($resolvedChange) }}%
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-md border border-gray-100 space-y-4" 
                x-data="{ 
                    filter: 'week', 
                    chartLabels: @js($dashboardData['growthData']['labels'] ?? []),
                    chartUsers: @js($dashboardData['growthData']['users'] ?? []),
                    chartPosts: @js($dashboardData['growthData']['posts'] ?? []),
                    chartInstance: null,
                    loading: false,
                    async updateChart(period) {
                        this.filter = period;
                        this.loading = true;
                        const url = '/admin/api/growth-data?period=' + period;
                        console.log('Fetching from:', url);
                        try {
                            const response = await fetch(url);
                            console.log('Response status:', response.status);
                            if (!response.ok) {
                                const errorText = await response.text();
                                console.error('Response error:', errorText);
                                throw new Error('Network response was not ok: ' + response.status);
                            }
                            const data = await response.json();
                            console.log('Received data:', data);
                            this.chartLabels = data.labels;
                            this.chartUsers = data.users;
                            this.chartPosts = data.posts;
                            initChart(this.$refs.growthChart, this.chartLabels, this.chartUsers, this.chartPosts, this);
                        } catch (error) {
                            console.error('Full error:', error);
                            alert('Failed to load chart data: ' + error.message);
                        } finally {
                            this.loading = false;
                        }
                    }
                }"
                x-init="$nextTick(() => { 
                    lucide.createIcons(); 
                    initChart($refs.growthChart, chartLabels, chartUsers, chartPosts, $data);
                })"
            >
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-800">Platform Growth (Users & Posts)</h3>
                    
                    <div class="inline-flex rounded-md shadow-sm bg-gray-100 p-1">
                        <button @click="updateChart('week')" :disabled="loading" :class="filter === 'week' ? 'bg-white text-indigo-700 shadow' : 'text-gray-600 hover:text-indigo-700'" class="py-1 px-3 text-sm font-medium rounded-md transition-colors disabled:opacity-50">
                            Week
                        </button>
                        <button @click="updateChart('month')" :disabled="loading" :class="filter === 'month' ? 'bg-white text-indigo-700 shadow' : 'text-gray-600 hover:text-indigo-700'" class="py-1 px-3 text-sm font-medium rounded-md transition-colors disabled:opacity-50">
                            Month
                        </button>
                        <button @click="updateChart('year')" :disabled="loading" :class="filter === 'year' ? 'bg-white text-indigo-700 shadow' : 'text-gray-600 hover:text-indigo-700'" class="py-1 px-3 text-sm font-medium rounded-md transition-colors disabled:opacity-50">
                            Year
                        </button>
                    </div>
                </div>

                <div class="relative h-96 w-full p-4 overflow-hidden">
                    <canvas x-ref="growthChart" class="w-full h-full"></canvas>
                </div>
            </div>

            <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-md border border-gray-100">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Recent Censored Words</h3>
                <ul class="divide-y divide-gray-100">
                    @forelse ($dashboardData['recentActivities'] ?? [] as $activity)
                        <li class="py-3 flex items-start">
                            <i data-lucide="shield-alert" class="w-4 h-4 mr-3 mt-1 text-red-500 flex-shrink-0"></i>

                            <div>
                                <p class="text-sm text-gray-800 leading-snug">{!! $activity['description'] !!}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $activity['time'] }}</p>
                            </div>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-gray-500">No recent censored words found.</li>
                    @endforelse
                </ul>
                <a href="../admin/reports" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    View All Censored Words &rarr;
                </a>
            </div>
        </div>
    </div>

    <script>
        function initChart(canvas, labels, users, posts, $data) {
            if ($data.chartInstance) {
                $data.chartInstance.destroy();
            }

            const ctx = canvas.getContext('2d');
            
            $data.chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'New Users',
                            data: users,
                            borderColor: '#8b5cf6',
                            backgroundColor: '#8b5cf615',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }, 
                        {
                            label: 'New Posts',
                            data: posts,
                            borderColor: '#60a5fa',
                            backgroundColor: 'transparent',
                            tension: 0.4,
                            fill: false,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: { 
                        y: { 
                            beginAtZero: true,
                            grid: { color: '#f3f4f6' },
                            ticks: {
                                precision: 0
                            }
                        },
                        x: { 
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</x-admin-layout>