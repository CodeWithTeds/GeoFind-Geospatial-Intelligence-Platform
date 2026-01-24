<header class="bg-gray-800 shadow-lg h-16 fixed w-full top-0 z-50 flex items-center justify-between px-6">
    <div class="flex items-center">
        <h1 class="text-xl font-bold text-yellow-500">GIS Admin</h1>
    </div>
    <div class="flex items-center space-x-4">
        <span class="text-gray-300 text-sm">Welcome, {{ Auth::user()->name ?? 'Admin' }}</span>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="text-sm text-red-400 hover:text-red-300 transition duration-150">Logout</button>
        </form>
    </div>
</header>
