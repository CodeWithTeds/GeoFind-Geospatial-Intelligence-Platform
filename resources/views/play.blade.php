<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JerksHead - Play</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- CesiumJS -->
    <script src="https://cesium.com/downloads/cesiumjs/releases/1.119/Build/Cesium/Cesium.js"></script>
    <link href="https://cesium.com/downloads/cesiumjs/releases/1.119/Build/Cesium/Widgets/widgets.css" rel="stylesheet">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/landing.css', 'resources/js/play.js'])
</head>
<body class="h-screen w-screen overflow-hidden relative font-sans bg-black">

    <!-- Fullscreen Map Container -->
    <div id="game-map-container" class="fixed top-0 left-0 w-full h-full z-[1] bg-black">
        <div id="cesiumContainer" class="w-full h-full"></div>
        <a href="/" id="exit-map-btn" aria-label="Close Map" class="absolute top-4 left-4 z-50 bg-black/50 text-white p-2 rounded-full backdrop-blur-sm border border-white/20 hover:bg-black/80 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
    </div>

    <!-- Question Panel (Bottom Right) -->
    <div id="question-panel" class="fixed right-0 bottom-0 md:right-6 md:bottom-6 w-full md:w-[450px] max-h-[60vh] md:max-h-[50vh] text-white z-50 transform transition-transform duration-500 translate-y-0 flex flex-col items-end p-4 md:p-0">
        <div class="bg-zinc-900/95 md:bg-zinc-900/90 border border-yellow-500/50 p-4 md:p-6 shadow-2xl backdrop-blur-md rounded-t-xl md:rounded-lg w-full overflow-y-auto">
            <div class="flex justify-between items-center mb-3 md:mb-4">
                <div class="text-yellow-500 font-['Chakra_Petch'] text-[10px] md:text-xs tracking-[0.2em] uppercase opacity-70">Incoming Transmission</div>
                <div class="flex gap-2">
                    <div class="w-1.5 h-1.5 md:w-2 md:h-2 bg-yellow-500 rounded-full animate-pulse"></div>
                    <div class="w-1.5 h-1.5 md:w-2 md:h-2 bg-yellow-500/50 rounded-full"></div>
                    <div class="w-1.5 h-1.5 md:w-2 md:h-2 bg-yellow-500/20 rounded-full"></div>
                </div>
            </div>

            <h2 id="question-title" class="text-xl md:text-2xl font-bold text-white mb-3 md:mb-4 font-['Chakra_Petch'] uppercase tracking-wide leading-tight drop-shadow-[0_0_10px_rgba(255,255,255,0.3)]">Initializing...</h2>
            
            <p id="question-description" class="text-gray-300 text-sm md:text-base leading-relaxed mb-4 md:mb-6 font-mono border-l-2 border-yellow-500/30 pl-3 md:pl-4">
                Establishing secure connection to mission control...
            </p>
            
            <div class="grid grid-cols-2 gap-2 md:gap-3 mb-4 md:mb-6">
                <div class="bg-black/40 p-2 border border-white/10 rounded">
                    <div class="text-[9px] md:text-[10px] text-gray-500 font-mono mb-0.5">DIFFICULTY</div>
                    <div id="question-difficulty" class="text-yellow-400 font-['Chakra_Petch'] font-bold text-xs md:text-sm">---</div>
                </div>
                <div class="bg-black/40 p-2 border border-white/10 rounded">
                    <div class="text-[9px] md:text-[10px] text-gray-500 font-mono mb-0.5">TOLERANCE</div>
                    <div id="question-tolerance" class="text-yellow-400 font-['Chakra_Petch'] font-bold text-xs md:text-sm">---</div>
                </div>
            </div>
            
            <button id="confirm-question-btn" class="group relative w-full overflow-hidden bg-yellow-500 hover:bg-yellow-400 text-black font-bold py-3 md:py-3 px-4 transition-all duration-300 font-['Chakra_Petch'] text-base md:text-lg uppercase tracking-[0.15em] hover:shadow-[0_0_20px_rgba(234,179,8,0.6)] rounded active:scale-95">
                <span class="relative z-10">ACCEPT MISSION</span>
                <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
            </button>
        </div>
    </div>

    <!-- Submit Button (Hidden Initially) -->
    <div id="submit-container" class="fixed bottom-8 left-1/2 transform -translate-x-1/2 z-50 transition-all duration-300 translate-y-24 opacity-0">
        <button id="submit-answer-btn" class="bg-green-600 hover:bg-green-500 text-white font-bold py-3 px-8 rounded-full shadow-[0_0_20px_rgba(22,163,74,0.6)] font-['Chakra_Petch'] text-lg uppercase tracking-widest transition-all hover:scale-105 active:scale-95 border-2 border-green-400">
            LOCK IN COORDINATES
        </button>
    </div>

    <!-- Mission Failed Modal -->
    @include('client.partials.mission-failed-modal')
    
    <!-- Mission Success Modal -->
    @include('client.partials.mission-success-modal')

    <!-- Global App Config -->
    <script>
        window.AppConfig = {
            targetLevel: Number(@json(request('level'))) || null,
            csrfToken: "{{ csrf_token() }}",
        }
    </script>
</body>
</html>