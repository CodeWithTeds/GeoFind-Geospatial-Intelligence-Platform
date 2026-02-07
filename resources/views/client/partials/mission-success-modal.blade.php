<div id="mission-success-modal" class="fixed inset-0 z-[100] flex items-center justify-center hidden opacity-0 transition-opacity duration-500">
    <!-- Overlay with blur -->
    <div class="absolute inset-0 bg-black/80 backdrop-blur-[2px]"></div>

    <!-- Main Container -->
    <div class="relative z-10 w-full max-w-2xl flex flex-col items-center justify-center p-4">
        
        <!-- Header Title -->
        <div class="text-center mb-8 relative">
            <h2 class="text-yellow-500 font-['Chakra_Petch'] font-bold text-xl tracking-[0.2em] uppercase mb-1 animate-pulse transform -skew-x-12">
                Mission Status
            </h2>
            <h1 class="text-6xl md:text-8xl font-black text-transparent bg-clip-text bg-gradient-to-b from-yellow-300 via-yellow-500 to-yellow-700 font-['Chakra_Petch'] uppercase tracking-tighter drop-shadow-[0_0_25px_rgba(234,179,8,0.6)] transform -skew-x-6">
                COMPLETED
            </h1>
        </div>

        <!-- Bitter Compliment (Random Message) -->
        <div class="w-full max-w-lg transform -skew-x-12 bg-zinc-900 border-l-8 border-yellow-500 p-1 mb-6 shadow-[0_0_20px_rgba(0,0,0,0.5)]">
            <div class="bg-zinc-800/80 p-4 text-center border border-white/5">
                <p class="font-mono text-gray-500 text-[10px] uppercase tracking-widest mb-1">Evaluation</p>
                <p id="success-compliment-text" class="text-white font-['Chakra_Petch'] text-lg md:text-xl font-medium leading-tight min-h-[1.5em] transform skew-x-12 md:skew-x-0">
                    <!-- Text injected by JS -->
                </p>
            </div>
        </div>

        <!-- Stats Bars (Like the reference) -->
        <div class="w-full max-w-md space-y-3 mb-10">
            
            <!-- Distance Bar -->
            <div class="w-full h-12 bg-zinc-900 transform -skew-x-12 flex items-center justify-between px-6 border border-white/10 shadow-lg relative overflow-hidden group">
                <div class="absolute inset-0 bg-blue-900/20 group-hover:bg-blue-900/30 transition-colors"></div>
                <span class="text-white font-['Chakra_Petch'] font-bold uppercase tracking-wider z-10 text-sm transform skew-x-12">Distance Error</span>
                <span id="success-distance" class="text-yellow-400 font-mono font-bold text-xl z-10 drop-shadow-[0_0_5px_rgba(234,179,8,0.8)] transform skew-x-12">0m</span>
            </div>

            <!-- Stars Bar -->
            <div class="w-full h-12 bg-zinc-900 transform -skew-x-12 flex items-center justify-between px-6 border border-white/10 shadow-lg relative overflow-hidden group">
                <div class="absolute inset-0 bg-purple-900/20 group-hover:bg-purple-900/30 transition-colors"></div>
                <span class="text-white font-['Chakra_Petch'] font-bold uppercase tracking-wider z-10 text-sm transform skew-x-12">Performance</span>
                <div id="success-stars-container" class="flex gap-1 z-10 transform skew-x-12">
                    <!-- Stars injected here -->
                </div>
            </div>

            <!-- Total Reward (Optional placeholder for future use or purely decorative for now) -->
             <div class="w-full h-14 bg-zinc-900 transform -skew-x-12 flex items-center justify-center px-6 border-2 border-yellow-500/50 shadow-[0_0_15px_rgba(234,179,8,0.2)] mt-2">
                <span class="text-green-400 font-['Chakra_Petch'] font-black uppercase tracking-[0.2em] text-xl transform skew-x-12 drop-shadow-[0_0_8px_rgba(74,222,128,0.6)]">
                    TARGET SECURED
                </span>
            </div>

        </div>

        <!-- Action Buttons -->
        <div class="flex gap-6 w-full max-w-md justify-center">
            
            <!-- Review/Retry Button -->
            <button id="success-map-btn" class="flex-1 bg-red-600 hover:bg-red-500 text-white font-black py-3 px-2 transform -skew-x-12 border-b-4 border-red-800 active:border-b-0 active:translate-y-1 transition-all shadow-lg group relative overflow-hidden">
                <span class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform"></span>
                <span class="inline-block transform skew-x-12 uppercase tracking-widest text-sm md:text-base">Review Map</span>
            </button>
            
            <!-- Next Mission Button -->
            <a href="/levels" class="flex-1 bg-yellow-500 hover:bg-yellow-400 text-black font-black py-3 px-2 transform -skew-x-12 border-b-4 border-yellow-700 active:border-b-0 active:translate-y-1 transition-all shadow-[0_0_20px_rgba(234,179,8,0.4)] group relative overflow-hidden text-center flex items-center justify-center">
                <span class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform"></span>
                <span class="inline-block transform skew-x-12 uppercase tracking-widest text-sm md:text-base">Next Mission</span>
            </a>

        </div>

    </div>
</div>
