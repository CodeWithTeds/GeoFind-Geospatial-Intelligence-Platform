<div class="w-full max-w-sm mx-auto p-4">
    <!-- Title -->
    <div class="text-center mb-6">
        <h3 class="text-amber-500 font-black uppercase tracking-widest text-lg drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)]">
            Campaign Path
        </h3>
        <p class="text-xs text-amber-700 font-mono uppercase tracking-widest opacity-80">
            {{ $completed_levels }} / {{ $total_levels }} Missions Complete
        </p>
    </div>

    <!-- Winding Path Container -->
    <div class="relative flex flex-col items-center gap-6">
        
        <!-- Path Line (Vertical Dashed) -->
        <div class="absolute top-4 bottom-4 left-1/2 -translate-x-1/2 w-1 h-full bg-gradient-to-b from-amber-500/20 via-amber-500/50 to-amber-500/20 border-l border-r border-dashed border-amber-500/30 -z-10"></div>

        <!-- Nodes (Visual Representation of Progress) -->
        @php
            // Create a simplified visual path (e.g., previous, current, next 2)
            $current = $completed_levels + 1;
            // Define a range to show, centered on current if possible
            $start = max(1, $current - 1);
            $end = min($total_levels, $start + 3); // Show 4 nodes max
            $range = range($start, $end);
        @endphp

        @foreach($range as $i)
            @php
                $isCompleted = $i <= $completed_levels;
                $isCurrent = $i == $current;
                $isLocked = $i > $current;
            @endphp

            <div class="relative z-10 flex items-center justify-center w-full">
                <!-- Node Circle -->
                <div class="relative w-14 h-14 flex items-center justify-center rounded-full border-4 shadow-xl transition-all duration-500
                    {{ $isCompleted ? 'bg-green-600 border-green-400 shadow-[0_0_15px_rgba(22,163,74,0.5)]' : 
                       ($isCurrent ? 'bg-amber-500 border-amber-300 shadow-[0_0_20px_rgba(245,158,11,0.6)] scale-110' : 
                        'bg-zinc-800 border-zinc-700 shadow-none opacity-80') }}">
                    
                    @if($isCompleted)
                        <span class="material-icons text-white text-2xl font-bold">check</span>
                    @elseif($isCurrent)
                         <!-- Current Level Indicator -->
                         <span class="font-black text-black text-xl">{{ $i }}</span>
                         <!-- Pulse Effect -->
                         <div class="absolute inset-0 rounded-full border-2 border-amber-200 opacity-0 animate-ping"></div>
                    @else
                        <span class="material-icons text-zinc-600 text-xl">lock</span>
                    @endif
                </div>

                <!-- Label (Alternating Sides) -->
                <div class="absolute {{ $loop->odd ? 'right-[60%]' : 'left-[60%]' }} top-1/2 -translate-y-1/2 w-32 {{ $loop->odd ? 'text-right pr-4' : 'text-left pl-4' }}">
                    @if($isCurrent)
                        <span class="block text-amber-400 font-bold text-sm uppercase tracking-wider drop-shadow-md animate-pulse">Current</span>
                    @endif
                    <span class="block text-[10px] font-mono uppercase tracking-widest {{ $isLocked ? 'text-zinc-600' : 'text-zinc-400' }}">
                        Level {{ $i }}
                    </span>
                </div>
            </div>
        @endforeach

        <!-- Next/More Indicator -->
        @if($end < $total_levels)
            <div class="relative z-10 flex flex-col items-center opacity-50">
                <div class="w-2 h-2 rounded-full bg-amber-500/50 mb-1"></div>
                <div class="w-2 h-2 rounded-full bg-amber-500/50 mb-1"></div>
                <div class="w-2 h-2 rounded-full bg-amber-500/50"></div>
            </div>
        @endif
    </div>
</div>