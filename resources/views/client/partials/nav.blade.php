<nav class="fixed top-0 left-0 w-full z-50 px-8 py-6 flex items-center justify-between bg-gradient-to-b from-black/80 to-transparent">
    <!-- Mobile Menu Button -->
    <button id="mobile-menu-btn" class="md:hidden text-white hover:text-yellow-500 transition-colors p-2" aria-label="Menu">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <!-- Brand -->
    <div class="text-2xl font-bold text-white tracking-[0.2em] uppercase drop-shadow-[0_0_10px_rgba(234,179,8,0.5)]">
        <span class="text-yellow-500">Jerks</span>Head
    </div>

    <!-- Navigation Items -->
    <div id="nav-links" class="hidden md:flex gap-12 items-center">
        <a href="{{ route('play') }}" class="text-sm font-bold text-gray-300 uppercase tracking-[0.2em] hover:text-yellow-500 hover:drop-shadow-[0_0_8px_rgba(234,179,8,0.8)] transition-all duration-300 relative group">
            Single Player
            <span class="absolute -bottom-2 left-0 w-0 h-0.5 bg-yellow-500 transition-all duration-300 group-hover:w-full box-shadow-[0_0_5px_rgba(234,179,8,0.8)]"></span>
        </a>
        <a href="#" class="text-sm font-bold text-gray-300 uppercase tracking-[0.2em] hover:text-yellow-500 hover:drop-shadow-[0_0_8px_rgba(234,179,8,0.8)] transition-all duration-300 relative group">
            Multi Player
            <span class="absolute -bottom-2 left-0 w-0 h-0.5 bg-yellow-500 transition-all duration-300 group-hover:w-full box-shadow-[0_0_5px_rgba(234,179,8,0.8)]"></span>
        </a>
        <a href="#" class="text-sm font-bold text-gray-300 uppercase tracking-[0.2em] hover:text-yellow-500 hover:drop-shadow-[0_0_8px_rgba(234,179,8,0.8)] transition-all duration-300 relative group">
            Leaderboard
            <span class="absolute -bottom-2 left-0 w-0 h-0.5 bg-yellow-500 transition-all duration-300 group-hover:w-full box-shadow-[0_0_5px_rgba(234,179,8,0.8)]"></span>
        </a>
        <a href="#" class="text-sm font-bold text-gray-300 uppercase tracking-[0.2em] hover:text-yellow-500 hover:drop-shadow-[0_0_8px_rgba(234,179,8,0.8)] transition-all duration-300 relative group">
            Party
            <span class="absolute -bottom-2 left-0 w-0 h-0.5 bg-yellow-500 transition-all duration-300 group-hover:w-full box-shadow-[0_0_5px_rgba(234,179,8,0.8)]"></span>
        </a>
    </div>

    <!-- Profile Section -->
    <div class="flex items-center gap-4">
        <div class="hidden md:block text-right">
            <div class="text-sm font-bold text-white tracking-wider uppercase">{{ Auth::user()->name }}</div>
            <div class="text-xs text-yellow-500 tracking-widest uppercase">Level 1</div>
        </div>
        <div class="relative group cursor-pointer">
            <div class="w-12 h-12 rounded border-2 border-yellow-500/50 group-hover:border-yellow-500 transition-all duration-300 p-0.5 bg-black/50 overflow-hidden relative">
                <!-- Tech corner accents -->
                <div class="absolute top-0 right-0 w-2 h-2 border-t-2 border-r-2 border-yellow-500/80"></div>
                <div class="absolute bottom-0 left-0 w-2 h-2 border-b-2 border-l-2 border-yellow-500/80"></div>
                
                <img 
                    src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=f2dc2f&color=000000" 
                    alt="Profile" 
                    class="w-full h-full object-cover"
                >
            </div>
            
            <!-- Dropdown -->
            <div class="absolute right-0 mt-4 w-48 bg-zinc-900 border border-zinc-700 rounded shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transform translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-50">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-3 text-red-400 hover:text-red-300 hover:bg-white/5 transition-colors uppercase tracking-widest text-xs font-bold">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
