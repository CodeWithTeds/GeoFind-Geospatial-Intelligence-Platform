@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Stat Card 1 -->
    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border border-gray-700">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-500 bg-opacity-20 text-yellow-500 flex items-center justify-center">
                <span class="material-icons text-3xl">place</span>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-400">Total Locations</p>
                <p class="text-2xl font-semibold text-white">1,234</p>
            </div>
        </div>
    </div>

    <!-- Stat Card 2 -->
    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border border-gray-700">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-500 bg-opacity-20 text-blue-500 flex items-center justify-center">
                <span class="material-icons text-3xl">people</span>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-400">Active Users</p>
                <p class="text-2xl font-semibold text-white">567</p>
            </div>
        </div>
    </div>

    <!-- Stat Card 3 -->
    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 border border-gray-700">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-500 bg-opacity-20 text-green-500 flex items-center justify-center">
                <span class="material-icons text-3xl">check_circle</span>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-400">System Status</p>
                <p class="text-2xl font-semibold text-green-400">Optimal</p>
            </div>
        </div>
    </div>
</div>

<div class="mt-8">
    <div class="bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border border-gray-700">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg leading-6 font-medium text-white">
                Recent Activity
            </h3>
        </div>
        <div class="p-6">
            <p class="text-gray-400">Welcome to the admin dashboard. Here you can manage locations, users, and system settings.</p>
        </div>
    </div>
</div>
@endsection
