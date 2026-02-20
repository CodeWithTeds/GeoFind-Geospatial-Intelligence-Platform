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
        /* Custom Scrollbar for Levels Container Only */
        #levels-scroll-container::-webkit-scrollbar {
            width: 8px;
            height: 6px;
        }
        #levels-scroll-container::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05); 
            border-radius: 10px;
            margin: 0px 16px;
        }
        #levels-scroll-container::-webkit-scrollbar-thumb {
            background: linear-gradient(90deg, #f59e0b, #d97706); 
            border-radius: 10px;
        }
        #levels-scroll-container::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(90deg, #fbbf24, #b45309); 
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

            <div id="levels-scroll-container" class="flex-1 w-full overflow-x-auto overflow-y-visible md:overflow-y-hidden p-4 pt-16 pb-3">
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

                                    <!-- Play Button / Status -->
                                    <div class="w-full mt-2">
                                        @if($level['status'] === 'completed')
                                            <div class="flex flex-col items-center space-y-2">
                                                <!-- Stars Display -->
                                                <div class="flex items-center justify-center space-x-1 mb-1">
                                                    @for($i = 0; $i < 3; $i++)
                                                        @if($i < ($level['stars'] ?? 0))
                                                            <svg class="w-6 h-6 text-yellow-400 drop-shadow-[0_0_5px_rgba(250,204,21,0.5)]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                        @else
                                                            <svg class="w-6 h-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                        @endif
                                                    @endfor
                                                </div>
                                                
                                                <button disabled class="block w-full py-2 rounded-lg font-black uppercase tracking-wider text-sm text-center bg-green-100 text-green-700 border-b-4 border-green-200 cursor-default opacity-80">
                                                    Completed
                                                </button>
                                            </div>
                                        @else
                                            <a href="{{ route('play', ['level' => $level['level']]) }}" 
                                               class="block w-full py-2 rounded-lg font-black uppercase tracking-wider text-sm text-center shadow-lg transition-all transform hover:translate-y-px active:translate-y-1 bg-red-500 text-white shadow-red-500/30 hover:bg-red-600 border-b-4 border-red-700">
                                                Play
                                            </a>
                                        @endif
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

        // Horizontal Scroll Logic
        function enableHorizontalScroll(id) {
            const container = document.getElementById(id);
            if (container) {
                container.addEventListener("wheel", (evt) => {
                    // Only intercept vertical scrolling if content overflows horizontally
                    if (container.scrollWidth > container.clientWidth) {
                        evt.preventDefault();
                        container.scrollLeft += evt.deltaY;
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            enableHorizontalScroll('levels-scroll-container');
            enableHorizontalScroll('progress-scroll-container');
        });
    </script>
</body>
</html>
