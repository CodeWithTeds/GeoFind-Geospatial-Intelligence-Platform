<div>
    @section('title', 'Dashboard')

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-600 mt-2">Welcome back, here's what's happening today.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Stat Card 1 -->
        <div class="bg-white overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 rounded-xl border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center">
                    <span class="material-icons text-3xl">place</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Locations</p>
                    <p class="text-2xl font-bold text-gray-900">1,234</p>
                </div>
            </div>
        </div>

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

        <!-- Stat Card 3 -->
        <div class="bg-white overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 rounded-xl border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-50 text-green-600 flex items-center justify-center">
                    <span class="material-icons text-3xl">check_circle</span>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">System Status</p>
                    <p class="text-2xl font-bold text-green-600">Optimal</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg leading-6 font-semibold text-gray-800 flex items-center">
                    <span class="material-icons text-gray-400 mr-2 text-base">history</span>
                    Recent Activity
                </h3>
            </div>
            <div class="p-6">
                <p class="text-gray-600">Welcome to the admin dashboard. Here you can manage locations, users, and system settings.</p>
            </div>
        </div>
    </div>
</div>
