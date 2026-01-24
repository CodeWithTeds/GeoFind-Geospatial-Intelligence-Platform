<header class="bg-white shadow-sm border-b border-gray-200 h-16 fixed w-full top-0 z-50 flex items-center justify-between px-6">
    <div class="flex items-center">
        <h1 class="text-xl font-bold text-blue-600 tracking-tight">GIS Admin</h1>
    </div>
    <div class="flex items-center space-x-6">
        <span class="text-gray-600 text-sm font-medium">Welcome, {{ Auth::user()->name ?? 'Admin' }}</span>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-700 transition duration-150 flex items-center">
                <span class="material-icons text-sm mr-1">logout</span>
                Logout
            </button>
        </form>
    </div>
</header>
