<div id="mission-failed-modal" class="fixed inset-0 z-[100] flex flex-col items-center justify-center hidden opacity-0 transition-opacity duration-500 pointer-events-none">
    <!-- Overlay (Transparent/Subtle) -->
    <div class="absolute inset-0 bg-black/80 backdrop-blur-[2px] pointer-events-auto"></div>

    <div class="relative z-10 flex flex-col items-center text-center p-4 w-full max-w-4xl pointer-events-auto">
        
        <!-- Title -->
        <h1 class="text-5xl md:text-8xl font-black text-transparent bg-clip-text bg-gradient-to-b from-red-500 to-red-900 font-['Chakra_Petch'] tracking-tighter uppercase mb-6 drop-shadow-[0_0_25px_rgba(220,38,38,0.6)] animate-pulse">
            MISSION FAILED
        </h1>

        <!-- Teasing Text Container -->
        <div class="min-h-[120px] md:min-h-[160px] flex items-center justify-center w-full max-w-2xl bg-black/40 border-l-4 border-red-600 p-6 backdrop-blur-sm transform skew-x-[-5deg]">
            <p id="failed-teasing-text" class="text-yellow-400 font-['Chakra_Petch'] text-xl md:text-3xl font-medium tracking-wide leading-relaxed transform skew-x-[5deg]">
                <!-- Text injected by JS -->
            </p>
            <span class="w-3 h-8 bg-yellow-500 ml-2 animate-pulse inline-block transform skew-x-[5deg]"></span>
        </div>

        <!-- Action Buttons -->
        <div class="mt-10 flex flex-col gap-4 w-full max-w-xs z-20">
            <button onclick="window.location.reload()" class="w-full bg-transparent hover:bg-red-600/20 text-red-500 hover:text-white font-bold py-4 px-8 border-2 border-red-600 shadow-[0_0_20px_rgba(220,38,38,0.2)] hover:shadow-[0_0_30px_rgba(220,38,38,0.6)] transition-all duration-300 transform hover:scale-105 group clip-path-polygon">
                <span class="font-['Chakra_Petch'] text-xl uppercase tracking-[0.2em]">RETRY MISSION</span>
            </button>
            
            <a href="/dashboard" class="text-gray-500 hover:text-gray-300 font-['Chakra_Petch'] text-xs uppercase tracking-[0.3em] mt-2 transition-colors text-center">
                Return to Base
            </a>
        </div>
    </div>
</div>

<style>
    /* Optional glitch animation could go here */
    .clip-path-polygon {
        clip-path: polygon(10% 0, 100% 0, 100% 80%, 90% 100%, 0 100%, 0 20%);
    }
</style>