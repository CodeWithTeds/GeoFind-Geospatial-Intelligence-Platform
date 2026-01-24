<aside class="fixed left-0 top-16 w-64 h-full bg-gray-900 border-r border-gray-800 transform transition-transform duration-300 ease-in-out z-40" id="sidebar">
    <nav class="mt-5 px-2">
        <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-2 py-3 text-base leading-6 font-medium rounded-md text-white bg-gray-800 focus:outline-none focus:bg-gray-700 transition ease-in-out duration-150">
            <span class="material-icons mr-4 text-yellow-500">dashboard</span>
            Dashboard
        </a>
        
        <a href="{{ route('locations.index') }}" class="mt-1 group flex items-center px-2 py-3 text-base leading-6 font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150">
            <span class="material-icons mr-4 text-gray-400 group-hover:text-gray-300 group-focus:text-gray-300 transition ease-in-out duration-150">place</span>
            Locations
        </a>

        <!-- Add more sidebar items here -->
    </nav>
</aside>
