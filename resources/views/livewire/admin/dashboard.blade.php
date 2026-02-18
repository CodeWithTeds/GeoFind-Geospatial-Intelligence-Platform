<div>
    @section('title', 'Dashboard')

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-600 mt-2">Welcome back, here's what's happening today.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Stat Card 2 -->
        <div class="bg-white overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 rounded-xl border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                    <span class="material-icons text-3xl">help_outline</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Questions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_questions'] }}</p>
                </div>
            </div>
        </div>
    </div>

@if(isset($analyticsData))
<div class="mt-8">
    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg leading-6 font-semibold text-gray-800 flex items-center">
                <span class="material-icons text-blue-500 mr-2 text-base">analytics</span>
                Google Analytics (Last 7 Days)
            </h3>
            <span class="text-xs text-gray-400">Property: {{ config('analytics.property_id') }}</span>
        </div>
        <div class="p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visitors</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Page Views</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($analyticsData as $data)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['date']->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($data['activeUsers'] ?? $data['visitors'] ?? 0) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($data['screenPageViews'] ?? $data['pageViews'] ?? 0) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No analytics data available for this period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="mt-8">
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <span class="material-icons text-yellow-400">warning</span>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    Google Analytics data is currently unavailable. Please ensure the service account credentials file is uploaded to:
                    <br>
                    <code class="bg-yellow-100 px-1 py-0.5 rounded text-xs font-mono mt-1 block w-fit">storage/app/analytics/service-account-credentials.json</code>
                </p>
            </div>
        </div>
    </div>
</div>
@endif
</div>
