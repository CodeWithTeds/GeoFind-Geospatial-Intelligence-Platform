<div class="w-full mb-6">
    <!-- Horizontal Road Container -->
    <div class="relative w-full overflow-x-auto scrollbar-hide py-4 touch-pan-x" id="progress-scroll-container">
        
        @php
            // Configuration for the Road
            $stepX = 120; // Horizontal distance between nodes
            $centerY = 80; // Vertical center of the container
            $amplitude = 40; // How far up/down the road goes
            
            // Total width based on levels
            $totalWidth = ($total_levels + 1) * $stepX;
            $containerHeight = 160;
        @endphp

        <div class="relative" style="height: {{ $containerHeight }}px; min-width: {{ $totalWidth }}px;">
            
            <!-- The Road (SVG Path) -->
            <svg class="absolute top-0 left-0 w-full h-full pointer-events-none" style="min-width: {{ $totalWidth }}px;">
                <defs>
                    <filter id="roadShadow" x="-20%" y="-20%" width="140%" height="140%">
                        <feGaussianBlur in="SourceAlpha" stdDeviation="4"/>
                        <feOffset dx="0" dy="4" result="offsetblur"/>
                        <feComponentTransfer>
                            <feFuncA type="linear" slope="0.6"/>
                        </feComponentTransfer>
                        <feMerge> 
                            <feMergeNode/>
                            <feMergeNode in="SourceGraphic"/>
                        </feMerge>
                    </filter>
                </defs>

                <!-- Road Border (Outline) -->
                <path d="M 0,{{ $centerY }} 
                   @foreach(range(1, $total_levels) as $i)
                       @php
                           $currX = $i * $stepX;
                           $currY = $i % 2 != 0 ? $centerY - $amplitude : $centerY + $amplitude;
                           $prevX = ($i - 1) * $stepX;
                           $prevY = ($i - 1) == 0 ? $centerY : (($i - 1) % 2 != 0 ? $centerY - $amplitude : $centerY + $amplitude);
                           $cp1x = $prevX + ($stepX / 2); $cp1y = $prevY;
                           $cp2x = $currX - ($stepX / 2); $cp2y = $currY;
                       @endphp
                       C {{ $cp1x }},{{ $cp1y }} {{ $cp2x }},{{ $cp2y }} {{ $currX }},{{ $currY }}
                   @endforeach
                   " 
                   fill="none" 
                   stroke="#18181b" 
                   stroke-width="52" 
                   stroke-linecap="round"
                   filter="url(#roadShadow)"
                   class="opacity-90" />

                <!-- Road Pavement -->
                <path d="M 0,{{ $centerY }} 
                   @foreach(range(1, $total_levels) as $i)
                       @php
                           $currX = $i * $stepX;
                           $currY = $i % 2 != 0 ? $centerY - $amplitude : $centerY + $amplitude;
                           $prevX = ($i - 1) * $stepX;
                           $prevY = ($i - 1) == 0 ? $centerY : (($i - 1) % 2 != 0 ? $centerY - $amplitude : $centerY + $amplitude);
                           $cp1x = $prevX + ($stepX / 2); $cp1y = $prevY;
                           $cp2x = $currX - ($stepX / 2); $cp2y = $currY;
                       @endphp
                       C {{ $cp1x }},{{ $cp1y }} {{ $cp2x }},{{ $cp2y }} {{ $currX }},{{ $currY }}
                   @endforeach
                   " 
                   fill="none" 
                   stroke="#3f3f46" 
                   stroke-width="40" 
                   stroke-linecap="round"
                   class="opacity-100" />

                <!-- Road Center Line (Dashed) -->
                <path d="M 0,{{ $centerY }} 
                   @foreach(range(1, $total_levels) as $i)
                       @php
                           $currX = $i * $stepX;
                           $currY = $i % 2 != 0 ? $centerY - $amplitude : $centerY + $amplitude;
                           $prevX = ($i - 1) * $stepX;
                           $prevY = ($i - 1) == 0 ? $centerY : (($i - 1) % 2 != 0 ? $centerY - $amplitude : $centerY + $amplitude);
                           $cp1x = $prevX + ($stepX / 2); $cp1y = $prevY;
                           $cp2x = $currX - ($stepX / 2); $cp2y = $currY;
                       @endphp
                       C {{ $cp1x }},{{ $cp1y }} {{ $cp2x }},{{ $cp2y }} {{ $currX }},{{ $currY }}
                   @endforeach
                   " 
                   fill="none" 
                   stroke="#fbbf24" 
                   stroke-width="2" 
                   stroke-dasharray="10,15"
                   stroke-linecap="round"
                   class="opacity-80" />
            </svg>

            <!-- Nodes -->
            @foreach(range(1, $total_levels) as $i)
                @php
                    $isCompleted = $i <= $completed_levels;
                    $isCurrent = $i == $completed_levels + 1;
                    $isLocked = $i > $completed_levels + 1;
                    
                    $posX = $i * $stepX;
                    $posY = $i % 2 != 0 ? $centerY - $amplitude : $centerY + $amplitude;
                @endphp

                <div class="absolute flex flex-col items-center justify-center transform -translate-x-1/2 -translate-y-1/2 transition-all duration-500 hover:scale-110 cursor-pointer" 
                     style="left: {{ $posX }}px; top: {{ $posY }}px;">
                    
                    <!-- Node Circle -->
                    <div class="relative flex items-center justify-center rounded-full border-4 shadow-lg z-10 transition-all duration-300
                        {{ $isCompleted ? 'w-10 h-10 bg-green-600 border-green-400 shadow-green-900/50' : 
                           ($isCurrent ? 'w-14 h-14 bg-amber-500 border-amber-300 shadow-amber-500/50 scale-110 ring-4 ring-amber-500/20 animate-pulse' : 
                            'w-10 h-10 bg-zinc-800 border-zinc-600 shadow-black/50 grayscale opacity-80') }}">
                        
                        @if($isCompleted)
                            <span class="material-icons text-white text-lg">check</span>
                        @elseif($isCurrent)
                            <span class="font-black text-white text-2xl drop-shadow-md">{{ $i }}</span>
                        @else
                            <span class="material-icons text-zinc-500 text-lg">lock</span>
                        @endif
                    </div>

                    <!-- Level Label (Below) -->
                    @if($isCurrent)
                    <div class="absolute top-full mt-2 px-3 py-1 bg-black/80 text-amber-400 text-xs font-bold rounded-full border border-amber-500/30 whitespace-nowrap shadow-xl backdrop-blur-sm">
                        Level {{ $i }}
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.getElementById('progress-scroll-container');
        if(!container) return;
        
        // Scroll to current level centered
        const currentLevel = {{ $completed_levels + 1 }};
        const stepX = {{ $stepX }};
        const containerWidth = container.clientWidth;
        
        // Calculate position: (Level * Step) - (Screen / 2)
        const scrollPos = (currentLevel * stepX) - (containerWidth / 2);
        
        // Slight delay to ensure layout is ready
        setTimeout(() => {
            container.scrollTo({
                left: Math.max(0, scrollPos),
                behavior: 'smooth'
            });
        }, 100);
    });
</script>
