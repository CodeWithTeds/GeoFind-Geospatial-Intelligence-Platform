<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JerksHead</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- CesiumJS -->
    <script src="https://cesium.com/downloads/cesiumjs/releases/1.119/Build/Cesium/Cesium.js"></script>
    <link href="https://cesium.com/downloads/cesiumjs/releases/1.119/Build/Cesium/Widgets/widgets.css" rel="stylesheet">
    
    <!-- Vite Assets -->
    @vite(['resources/css/landing.css', 'resources/js/landing.js'])
</head>
<body class="h-screen w-screen overflow-hidden relative font-sans">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/findjerks_opt.jpg') }}?v={{ filemtime(public_path('images/findjerks_opt.jpg')) }}" alt="Background" class="w-full h-full object-cover" fetchpriority="high">
        <!-- Dark gradient overlay for better text readability if needed -->
        <div class="absolute inset-0 bg-black/10"></div>
    </div>

    <!-- Main Content Container -->
    <div class="relative z-10 w-full h-full flex flex-col p-6 md:p-12">
        
        <!-- Header / Top Left Button -->
        <header class="flex justify-start">
            <button id="play-now-btn" aria-label="Play Now" class="group relative bg-black/80 text-white px-8 py-3 rounded-full font-bold border-2 border-yellow-500/50 hover:bg-black hover:border-yellow-400 transition-all duration-300 shadow-lg flex items-center gap-3 overflow-hidden">
                <span class="relative z-10 tracking-wider text-sm md:text-base">PLAY NOW!</span>
                <span class="relative z-10 w-2.5 h-2.5 bg-yellow-400 rounded-full animate-pulse shadow-[0_0_10px_rgba(250,204,21,0.8)]"></span>
                
                <!-- Hover effect glow -->
                <div class="absolute inset-0 bg-yellow-400/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
            </button>
        </header>

        <!-- Main Body -->
        <main class="flex-grow relative">
            <!-- 3D-style right-side paragraph, slightly skewed left --> 
            <div class="absolute right-6 top-1/2 -translate-y-1/2 max-w-md hidden md:block"> 
                <div 
                    id="perspective-text"
                    class="relative select-none text-white transition-transform duration-700 ease-out will-change-transform" 
                > 
                    <p class="text-5xl md:text-6xl font-extrabold uppercase tracking-wide leading-tight text-[#f2dc2f] [text-shadow:0_1px_0_#c3a91f,0_2px_0_#b1971c,0_3px_0_#9e8619,0_4px_0_#8c7616,0_5px_0_#7a6614,0_6px_0_#6a5712,0_14px_28px_rgba(0,0,0,.65)]"> 
                        The jerks won’t hide forever. 
                    </p> 
                    <p class="mt-3 text-white/90 text-lg font-medium drop-shadow-md"> 
                        Scan the area, trust your gut, and pin the location fast. 
                    </p> 
                </div> 
            </div> 
        </main>

        <!-- Footer / Logo -->
        <footer class="flex justify-center pb-4 md:pb-8">
            <div class="transform hover:scale-105 transition-transform duration-300 cursor-pointer">
                <img src="{{ asset('images/title_opt.png') }}?v={{ filemtime(public_path('images/title_opt.png')) }}" alt="JERKSHEAD" class="h-20 md:h-28 lg:h-32 object-contain drop-shadow-2xl" loading="lazy">
            </div>
        </footer>

    </div>

    <!-- Instruction Modal -->
    <div id="instruction-modal" class="hidden fixed top-0 left-0 w-full h-full z-50 bg-black/85 items-center justify-center backdrop-blur-[5px]" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="modal-content bg-gradient-to-br from-[#1a1a1a] to-[#0d0d0d] border border-[#333] p-8 rounded-2xl max-w-[90%] w-[400px] text-center text-white shadow-[0_20px_50px_rgba(0,0,0,0.5)] modal-pop">
            <h2 id="modal-title" class="text-2xl font-bold mb-4 text-yellow-400">Welcome!</h2>
            <p class="mb-6 text-gray-300">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                Welcome! Click Start to explore the 3D map. 
                Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            </p>
            <button id="start-game-btn" aria-label="Start Game" class="bg-yellow-500 hover:bg-yellow-400 text-black font-bold py-3 px-8 rounded-full transition-colors duration-300 w-full">
                START
            </button>
            <button id="close-modal-btn" aria-label="Cancel" class="mt-4 text-gray-500 hover:text-white text-sm underline">
                Cancel
            </button>
        </div>
    </div>

    <!-- Global App Config -->
    <script>
        window.AppConfig = {
            // cesium token removed for security
        }
    </script>
</body>
</html>
