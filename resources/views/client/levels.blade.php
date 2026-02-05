<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Mission - JerksHead</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            font-family: 'Chakra Petch', sans-serif;
            background-color: #000000;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="h-screen w-screen md:overflow-hidden overflow-y-auto relative text-white bg-black">
    <!-- Finisher Header Background -->
    <div class="header finisher-header absolute inset-0 z-0" style="width: 100%; height: 100vh;"></div>

    <!-- Tech Borders (Decorative) -->
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none z-40 p-6 hidden md:block">
        <div class="absolute top-6 left-6 w-32 h-32 border-t-2 border-l-2 border-yellow-500/30 rounded-tl-3xl"></div>
        <div class="absolute top-6 right-6 w-32 h-32 border-t-2 border-r-2 border-yellow-500/30 rounded-tr-3xl"></div>
        <div class="absolute bottom-6 left-6 w-32 h-32 border-b-2 border-l-2 border-yellow-500/30 rounded-bl-3xl"></div>
        <div class="absolute bottom-6 right-6 w-32 h-32 border-b-2 border-r-2 border-yellow-500/30 rounded-br-3xl"></div>
    </div>

    <!-- Navigation Bar -->
    @include('client.partials.nav')

    <!-- Main Content -->
    <main class="relative z-10 w-full min-h-screen md:h-full flex flex-col md:flex-row items-center justify-start md:justify-center p-4 pt-24 pb-12 gap-8">
        
        <!-- Left Side: Character Only -->
        <div class="w-full md:w-1/3 flex flex-col items-center justify-center space-y-6 shrink-0">
            <!-- Character Image -->
            <div class="relative group flex items-center justify-center">
                <!-- Glow effect -->
                <div class="absolute inset-0 bg-yellow-500/20 blur-3xl rounded-full opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
                
                <img 
                    src="{{ asset('images/p1.avif') }}" 
                    alt="Character" 
                    class="relative max-w-[60%] md:max-w-[80%] max-h-[25vh] md:max-h-[30vh] w-auto h-auto object-contain drop-shadow-2xl transition-transform duration-500 hover:scale-105"
                >
            </div>
            
            <!-- Progress Text (Moved Here) -->
            <div class="text-center">
                <h3 class="text-amber-500 font-black uppercase tracking-widest text-lg drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)]">
                    Progress
                </h3>
                <p class="text-sm text-amber-700 font-mono uppercase tracking-widest opacity-80 font-bold">
                    {{ Auth::user()->completed_levels }} / {{ count($levels) }} Missions
                </p>
            </div>
        </div>

        <!-- Right Side: Levels Grid + Progress -->
        <div class="w-full md:w-2/3 h-auto md:h-full md:max-h-[80vh] flex flex-col">
            
            <!-- Horizontal Zigzag Progress Map -->
            @include('client.partials.progress-map-horizontal', [
                'completed_levels' => Auth::user()->completed_levels,
                'total_levels' => count($levels)
            ])

            <h1 class="text-3xl md:text-4xl font-black uppercase tracking-tighter mb-2 text-transparent bg-clip-text bg-gradient-to-r from-white via-gray-200 to-gray-400 drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)] text-center md:text-left">
                Mission Selection
            </h1>

            <div class="flex-1 w-full overflow-x-auto overflow-y-visible md:overflow-y-hidden scrollbar-hide p-4 pt-16 pb-8 touch-pan-x">
                <div class="flex flex-nowrap gap-6 md:gap-8 px-4 min-w-max">
                    @forelse($levels as $level)
                        <!-- Hanging Level Card -->
                        <div class="relative group flex-shrink-0">
                            <!-- Ropes -->
                            <div class="absolute -top-12 left-6 w-1 h-14 bg-amber-800/60 z-0"></div>
                            <div class="absolute -top-12 right-6 w-1 h-14 bg-amber-800/60 z-0"></div>

                            <!-- Card Body -->
                            <div class="relative w-48 h-64 rounded-xl shadow-2xl z-10 flex flex-col items-center justify-between p-4 transition-transform duration-300 hover:scale-105 hover:-rotate-1
                                {{ $level['status'] === 'locked' ? 'bg-zinc-800 border-b-8 border-zinc-900' : 'bg-[#f4e4bc] border-b-8 border-[#d4c49c]' }}">
                                
                                @if($level['status'] === 'locked')
                                    <!-- Locked Content: User Picture + Lock -->
                                    <div class="flex-1 flex flex-col items-center justify-center w-full relative">
                                        <!-- User Picture (Character) -->
                                        <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-zinc-600 bg-zinc-900 relative mb-2">
                                            <img src="{{ asset('images/p1.avif') }}" alt="Locked" class="w-full h-full object-cover opacity-50 grayscale">
                                            
                                            <!-- Lock Icon Overlay -->
                                            <div class="absolute inset-0 flex items-center justify-center bg-black/40">
                                                <span class="material-icons text-4xl text-white/80 drop-shadow-lg">lock</span>
                                            </div>
                                        </div>
                                        <span class="text-zinc-500 font-bold uppercase tracking-widest text-sm mt-2">Locked</span>
                                        <div class="mt-2 px-3 py-1 bg-zinc-900/50 rounded text-xs text-zinc-500 border border-zinc-700">
                                            Level {{ $level['level'] }}
                                        </div>
                                    </div>
                                @else
                                    <!-- Unlocked/Completed Content -->
                                    <div class="w-full text-center">
                                        <h3 class="font-black text-amber-900 text-lg uppercase leading-tight mb-1">{{ $level['title'] }}</h3>
                                        <div class="text-[10px] font-bold text-amber-700/60 uppercase tracking-widest mb-2">Level {{ $level['level'] }}</div>
                                    </div>

                                    <!-- Central Graphic/Icon -->
                                    <div class="flex-1 flex items-center justify-center">
                                        @if($level['status'] === 'completed')
                                            <div class="text-green-600">
                                                <span class="material-icons text-6xl drop-shadow-sm">check_circle</span>
                                            </div>
                                        @else
                                            <div class="text-amber-600">
                                                <span class="material-icons text-6xl drop-shadow-sm">map</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Play Button -->
                                    <div class="w-full mt-2">
                                        <a href="{{ route('play', ['level' => $level['level']]) }}" 
                                           class="block w-full py-2 rounded-lg font-black uppercase tracking-wider text-sm text-center shadow-lg transition-all transform hover:translate-y-px active:translate-y-1
                                           {{ $level['status'] === 'completed' 
                                              ? 'bg-green-500 text-white shadow-green-500/30 hover:bg-green-600 border-b-4 border-green-700' 
                                              : 'bg-red-500 text-white shadow-red-500/30 hover:bg-red-600 border-b-4 border-red-700' }}">
                                            {{ $level['status'] === 'completed' ? 'Replay' : 'Play' }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-gray-500 w-full">
                            <p class="uppercase tracking-widest">No missions available yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    @include('client.partials.footer')

    <!-- Finisher Header Scripts -->
    <script src="{{ asset('js/libs/finisher-header.es5.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/finisher-init.js') }}" type="text/javascript"></script>
    
    <!-- Mobile Menu Script -->
    <script>
        const menuBtn = document.getElementById('mobile-menu-btn');
        const navLinks = document.getElementById('nav-links');
        
        if(menuBtn && navLinks) {
            menuBtn.addEventListener('click', () => {
                navLinks.classList.toggle('hidden');
                navLinks.classList.toggle('flex');
                navLinks.classList.toggle('flex-col');
                navLinks.classList.toggle('absolute');
                navLinks.classList.toggle('top-20');
                navLinks.classList.toggle('left-0');
                navLinks.classList.toggle('w-full');
                navLinks.classList.toggle('bg-black/95');
                navLinks.classList.toggle('p-8');
                navLinks.classList.toggle('border-b');
                navLinks.classList.toggle('border-yellow-500/30');
            });
        }
    </script>
</body>
</html>
