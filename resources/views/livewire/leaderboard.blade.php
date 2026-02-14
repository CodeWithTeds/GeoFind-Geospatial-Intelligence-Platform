<div wire:poll.5s class="min-h-screen bg-[#121212] font-['Chakra_Petch'] text-white p-4 md:p-8 relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-purple-900/20 blur-[100px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-900/20 blur-[100px] rounded-full"></div>
        
        <!-- Stars/Dust -->
        <div class="absolute top-10 left-20 w-1 h-1 bg-white/50 rounded-full animate-pulse"></div>
        <div class="absolute top-40 right-40 w-2 h-2 bg-yellow-400/30 rounded-full animate-pulse delay-700"></div>
        <div class="absolute bottom-20 left-1/3 w-1.5 h-1.5 bg-blue-400/30 rounded-full animate-pulse delay-300"></div>
    </div>

    <!-- Navigation Bar (Included directly since we are extending a layout that might not have it) -->
    @include('client.partials.nav')

    <!-- Header -->
    <div class="relative z-10 text-center mb-12 mt-20">
        <div class="inline-block relative">

            
            <h1 class="text-3xl md:text-5xl font-black uppercase tracking-widest bg-gradient-to-b from-purple-400 to-purple-700 text-transparent bg-clip-text drop-shadow-[0_4px_0_rgba(88,28,135,0.8)]"
                style="-webkit-text-stroke: 1px rgba(255,255,255,0.3);">
                Leaderboard
            </h1>
            <div class="h-1 w-full bg-gradient-to-r from-transparent via-purple-500 to-transparent mt-2"></div>
        </div>
        <p class="text-gray-400 text-xs md:text-sm mt-2 uppercase tracking-widest font-mono">
            Top Agents • Global Ranking
        </p>
    </div>

    <div class="max-w-4xl mx-auto relative z-10">
        
        <!-- PODIUM SECTION (Top 3) -->
        @if($top3->count() > 0)
        <div class="flex justify-center items-end gap-2 md:gap-6 mb-16 h-[350px] md:h-[400px]">
            
            <!-- 2nd Place -->
            @if($top3->has(1))
            <div class="flex flex-col items-center group w-1/3 md:w-auto">
                <div class="relative mb-2 transition-transform duration-300 group-hover:-translate-y-2">
                    <div class="absolute -top-6 left-1/2 -translate-x-1/2 z-20">
                        <span class="text-3xl filter drop-shadow-lg"></span>
                    </div>
                    <div class="w-16 h-16 md:w-24 md:h-24 rounded-full border-4 border-gray-300 overflow-hidden bg-gray-800 shadow-[0_0_20px_rgba(209,213,219,0.3)]">
                        <img src="{{ asset('images/p2.png') }}" alt="Rank 2" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-gray-700 text-white text-[10px] px-2 py-0.5 rounded-full border border-gray-500 whitespace-nowrap">
                        {{ $top3[1]->name }}
                    </div>
                </div>
                <!-- Pedestal -->
                <div class="w-20 md:w-32 bg-gradient-to-b from-gray-400 to-gray-600 rounded-t-lg relative flex flex-col items-center justify-start pt-4 border-t-4 border-gray-300 shadow-[0_10px_30px_rgba(0,0,0,0.5)] h-[160px] md:h-[180px]">
                    <div class="text-2xl md:text-4xl font-black text-gray-200 drop-shadow-md">2</div>
                    <div class="mt-2 text-xs md:text-sm font-bold text-gray-800 bg-white/20 px-2 rounded">
                        {{ number_format($top3[1]->total_score) }}
                    </div>
                    <!-- Decor -->
                    <div class="absolute bottom-0 w-full h-1/3 bg-black/10 rounded-b-lg pointer-events-none"></div>
                </div>
            </div>
            @endif

            <!-- 1st Place -->
            @if($top3->has(0))
            <div class="flex flex-col items-center group w-1/3 md:w-auto z-10">
                <div class="relative mb-4 transition-transform duration-300 group-hover:-translate-y-3">
                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 z-20 animate-bounce">
                        <span class="text-4xl filter drop-shadow-[0_0_10px_rgba(234,179,8,0.8)]"></span>
                    </div>
                    <div class="w-20 h-20 md:w-32 md:h-32 rounded-full border-4 border-yellow-400 overflow-hidden bg-yellow-900 shadow-[0_0_30px_rgba(234,179,8,0.5)]">
                        <img src="{{ asset('images/p1.png') }}" alt="Rank 1" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 bg-yellow-600 text-white text-xs px-3 py-1 rounded-full border border-yellow-400 font-bold whitespace-nowrap shadow-lg">
                        {{ $top3[0]->name }}
                    </div>
                </div>
                <!-- Pedestal -->
                <div class="w-24 md:w-40 bg-gradient-to-b from-yellow-400 to-yellow-600 rounded-t-lg relative flex flex-col items-center justify-start pt-6 border-t-4 border-yellow-200 shadow-[0_0_50px_rgba(234,179,8,0.4)] h-[220px] md:h-[260px]">
                    <div class="text-4xl md:text-6xl font-black text-yellow-100 drop-shadow-md">1</div>
                    <div class="mt-4 text-sm md:text-xl font-bold text-yellow-900 bg-white/30 px-3 py-1 rounded-lg backdrop-blur-sm">
                        {{ number_format($top3[0]->total_score) }}
                    </div>
                     <!-- Decor -->
                     <div class="absolute bottom-4 w-12 h-12 bg-yellow-300/20 rounded-full blur-xl"></div>
                </div>
            </div>
            @endif

            <!-- 3rd Place -->
            @if($top3->has(2))
            <div class="flex flex-col items-center group w-1/3 md:w-auto">
                <div class="relative mb-2 transition-transform duration-300 group-hover:-translate-y-2">
                    <div class="absolute -top-6 left-1/2 -translate-x-1/2 z-20">
                        <span class="text-3xl filter drop-shadow-lg"></span>
                    </div>
                    <div class="w-16 h-16 md:w-24 md:h-24 rounded-full border-4 border-orange-400 overflow-hidden bg-orange-900 shadow-[0_0_20px_rgba(249,115,22,0.3)]">
                        <img src="{{ asset('images/p3.png') }}" alt="Rank 3" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-orange-700 text-white text-[10px] px-2 py-0.5 rounded-full border border-orange-500 whitespace-nowrap">
                        {{ $top3[2]->name }}
                    </div>
                </div>
                <!-- Pedestal -->
                <div class="w-20 md:w-32 bg-gradient-to-b from-orange-400 to-orange-600 rounded-t-lg relative flex flex-col items-center justify-start pt-4 border-t-4 border-orange-300 shadow-[0_10px_30px_rgba(0,0,0,0.5)] h-[130px] md:h-[150px]">
                    <div class="text-2xl md:text-4xl font-black text-orange-100 drop-shadow-md">3</div>
                    <div class="mt-2 text-xs md:text-sm font-bold text-orange-900 bg-white/20 px-2 rounded">
                        {{ number_format($top3[2]->total_score) }}
                    </div>
                </div>
            </div>
            @endif

        </div>
        @endif

        <!-- LIST SECTION (Rank 4+) -->
        <div class="bg-[#1E1E24]/80 backdrop-blur-md rounded-2xl border border-white/10 shadow-2xl overflow-hidden">
            <!-- Table Header -->
            <div class="grid grid-cols-12 gap-4 p-4 bg-black/40 text-gray-400 text-xs md:text-sm font-bold uppercase tracking-wider border-b border-white/5">
                <div class="col-span-2 md:col-span-2 text-center">Rank</div>
                <div class="col-span-7 md:col-span-6">Agent</div>
                <div class="col-span-3 md:col-span-4 text-right pr-4">Score</div>
            </div>

            <!-- List Items -->
            <div class="divide-y divide-white/5 max-h-[400px] overflow-y-auto scrollbar-thin scrollbar-thumb-purple-600 scrollbar-track-black/20">
                @forelse($rest as $user)
                <div class="grid grid-cols-12 gap-4 p-3 md:p-4 items-center hover:bg-white/5 transition-colors group">
                    
                    <!-- Rank -->
                    <div class="col-span-2 md:col-span-2 text-center">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-700/50 text-gray-300 font-bold font-mono group-hover:bg-purple-600 group-hover:text-white transition-colors">
                            {{ $user->rank }}
                        </span>
                    </div>

                    <!-- User -->
                    <div class="col-span-7 md:col-span-6 flex items-center gap-3">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-gray-800 border border-gray-600 overflow-hidden flex-shrink-0">
                            <!-- Random avatar for list users -->
                            @php $avatar = $user->id % 2 == 0 ? 'p4.png' : 'p5.png'; @endphp
                            <img src="{{ asset('images/' . $avatar) }}" alt="User" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity">
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-200 group-hover:text-white truncate">{{ $user->name }}</span>
                            <span class="text-[10px] text-gray-500 font-mono">Lvl {{ $user->completed_levels }} • ⭐ {{ $user->total_stars }}</span>
                        </div>
                    </div>

                    <!-- Score -->
                    <div class="col-span-3 md:col-span-4 text-right pr-4">
                        <span class="font-bold text-yellow-500 font-mono text-sm md:text-base">{{ number_format($user->total_score) }}</span>
                    </div>
                </div>
                @empty
                    @if($top3->count() == 0)
                        <div class="p-8 text-center text-gray-500">
                            No active agents found.
                        </div>
                    @endif
                @endforelse
            </div>
        </div>

        <!-- User's own rank (Sticky Bottom) if authenticated -->
        @auth
        <div class="mt-6 bg-gradient-to-r from-purple-900/80 to-blue-900/80 backdrop-blur rounded-xl p-4 border border-white/20 shadow-lg flex items-center justify-between transform transition-transform hover:scale-[1.02]">
            <div class="flex items-center gap-4">
                <div class="flex flex-col items-center justify-center w-12 h-12 bg-black/30 rounded-lg border border-white/10">
                    <span class="text-[10px] text-gray-400 uppercase">Rank</span>
                    <!-- We need to find auth user's rank from the sorted collection -->
                    @php 
                        $myRank = $top3->merge($rest)->where('id', auth()->id())->first();
                    @endphp
                    <span class="text-xl font-bold text-white">{{ $myRank ? $myRank->rank : '-' }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-bold text-white uppercase tracking-wider">You</span>
                    <span class="text-xs text-purple-300">Level {{ auth()->user()->completed_levels }}</span>
                </div>
            </div>
            <div class="text-right">
                 <span class="block text-[10px] text-gray-400 uppercase">Total Score</span>
                 <span class="text-2xl font-black text-yellow-400 font-mono">{{ number_format(($myRank ? $myRank->total_score : 0)) }}</span>
            </div>
        </div>
        @endauth

    </div>
</div>
