<div>
    @if($isOpen)
    <!-- Instruction Overlay -->
    <div class="fixed inset-0 z-50 overflow-y-auto bg-black/90 backdrop-blur-sm font-['Chakra_Petch'] text-white animate-fade-in-up">
        <div class="max-w-6xl mx-auto p-6 md:p-12 flex flex-col gap-16 md:gap-24">
            
            <!-- Header / Close -->
            <div class="flex justify-end pt-4">
                <button wire:click="close" class="text-gray-400 hover:text-white transition-colors p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mission Briefing -->
            <section class="space-y-6">
                <h1 class="text-5xl md:text-7xl font-bold text-yellow-400 tracking-widest uppercase drop-shadow-[0_0_10px_rgba(250,204,21,0.5)]">
                    Mission Briefing
                </h1>
                <div class="text-xl md:text-2xl leading-relaxed text-gray-200 max-w-3xl border-l-4 border-yellow-500 pl-6">
                    <p class="mb-4">Somewhere in the Philippines, Jerkshead is hiding — and only the best hunters will find them.</p>
                    <p class="mb-4">Use your skills, logic, and map knowledge to track down their exact location.</p>
                    <p class="text-yellow-400 font-bold tracking-wide">There is no room for mistakes.!!</p>
                </div>
            </section>

            <!-- Step 1 -->
            <section class="grid grid-cols-1 md:grid-cols-2 gap-12 items-end">
                <div class="order-1">
                    <h2 class="text-3xl md:text-4xl font-bold text-yellow-400 mb-6 flex items-center gap-3">
                        <span class="text-4xl"></span>STEP 1 — ANALYZE THE CLUE
                    </h2>
                    <div class="space-y-4 text-lg md:text-xl text-gray-300">
                        <p>Read the question carefully.</p>
                        <p>Every word matters. One small detail could lead you straight to Jerkshead’s hiding place.</p>
                    </div>
                </div>
                <div class="order-2 flex justify-end">
                    <img src="{{ asset('images/danvs.avif') }}" class="w-full md:w-3/5 rounded-xl shadow-[0_0_20px_rgba(0,0,0,0.5)]" alt="Analyze The Clue">
                </div>
            </section>

            <!-- Step 2 -->
            <section class="grid grid-cols-1 md:grid-cols-2 gap-12 items-end">
                <div class="order-1 md:order-2">
                    <h2 class="text-3xl md:text-4xl font-bold text-yellow-400 mb-6 flex items-center gap-3">
                        <span class="text-4xl"></span>STEP 2 — SEARCH THE MAP
                    </h2>
                    <div class="space-y-4 text-lg md:text-xl text-gray-300">
                        <p>Scroll to the 3D map and begin your search.</p>
                        <p>Match the clue to real-world locations, landmarks, and terrain.</p>
                        <div class="mt-6 p-4 bg-yellow-900/20 border border-yellow-500/30 rounded-lg">
                            <p class="text-yellow-200 text-base">⚠️ <span class="font-bold">Tip:</span> If your map does not show labels, switch to Bing Maps Aerial with Labels to gain a tactical advantage.</p>
                        </div>
                    </div>
                </div>
                <div class="order-2 md:order-1 flex justify-end md:justify-start">
                    <img src="{{ asset('images/p1.avif') }}" class="w-full md:w-3/5 rounded-xl shadow-[0_0_20px_rgba(0,0,0,0.5)]" alt="Search The Map">
                </div>
            </section>

            <!-- Step 3 -->
            <section class="grid grid-cols-1 md:grid-cols-2 gap-12 items-end">
                <div class="order-1">
                    <h2 class="text-3xl md:text-4xl font-bold text-yellow-400 mb-6 flex items-center gap-3">
                        <span class="text-4xl">📍</span> STEP 3 — PIN TO WIN
                    </h2>
                    <div class="space-y-4 text-lg md:text-xl text-gray-300">
                        <p>Found the location? Now prove it.</p>
                        <p>Pin the exact coordinates on the map. Accuracy is everything.</p>
                        <div class="mt-8 space-y-2">
                            <p class="text-green-400 font-bold text-2xl drop-shadow-md">Get it right — YOU WIN.</p>
                            <p class="text-red-500 font-bold text-2xl drop-shadow-md">Get it wrong — Jerkshead escapes.</p>
                        </div>
                    </div>
                </div>
                <div class="order-2 flex justify-end">
                    <img src="{{ asset('images/p2.avif') }}" class="w-full md:w-3/5 rounded-xl shadow-[0_0_20px_rgba(0,0,0,0.5)]" alt="Pin To Win">
                </div>
            </section>  

            <!-- CTA -->
            <section class="py-16 flex flex-col items-center justify-center text-center">
                <button id="start-game-btn" @click="window.location.href='/play'" class="group relative px-16 py-6 bg-yellow-500 hover:bg-yellow-400 text-black font-black text-4xl tracking-widest clip-path-polygon transition-all duration-300 transform hover:scale-105 shadow-[0_0_40px_rgba(234,179,8,0.6)] uppercase">
                    PLAY NOW
                    <div class="absolute inset-0 bg-white/30 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                </button>
                <p class="mt-6 text-gray-500 text-sm tracking-widest uppercase">Are you ready to hunt?</p>
            </section>
            
            <div class="h-12"></div> <!-- Spacer -->
        </div>
    </div>
    @endif
</div>
