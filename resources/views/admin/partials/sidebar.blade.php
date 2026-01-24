<aside class="fixed left-0 top-16 w-64 h-full bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out z-40" id="sidebar">
    <nav class="mt-6 px-3 space-y-1">
        <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-3 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }} transition ease-in-out duration-150">
            <span class="material-icons mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}">dashboard</span>
            Dashboard
        </a>
        
        <a href="{{ route('locations.index') }}" class="group flex items-center px-3 py-3 text-sm font-medium rounded-md {{ request()->routeIs('locations.index') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }} transition ease-in-out duration-150">
            <span class="material-icons mr-3 {{ request()->routeIs('locations.index') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}">place</span>
            Locations
        </a>

        <a href="{{ route('admin.questions.index') }}" class="group flex items-center px-3 py-3 text-sm font-medium rounded-md {{ request()->routeIs('admin.questions.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }} transition ease-in-out duration-150">
            <span class="material-icons mr-3 {{ request()->routeIs('admin.questions.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500' }}">help_outline</span>
            Questions
        </a>

        <!-- Add more sidebar items here -->
    </nav>
</aside>
