<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JerksHead</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen w-screen overflow-hidden relative font-sans">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/findjerks.png') }}" alt="Background" class="w-full h-full object-cover">
        <!-- Dark gradient overlay for better text readability if needed -->
        <div class="absolute inset-0 bg-black/10"></div>
    </div>

    <!-- Main Content Container -->
    <div class="relative z-10 w-full h-full flex flex-col p-6 md:p-12">
        
        <!-- Header / Top Left Button -->
        <header class="flex justify-start">
            <button class="group relative bg-black/80 text-white px-8 py-3 rounded-full font-bold border-2 border-yellow-500/50 hover:bg-black hover:border-yellow-400 transition-all duration-300 shadow-lg flex items-center gap-3 overflow-hidden">
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
                <img src="{{ asset('images/title.png') }}" alt="JERKSHEAD" class="h-20 md:h-28 lg:h-32 object-contain drop-shadow-2xl">
            </div>
        </footer>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const perspectiveText = document.getElementById('perspective-text');
            let progress = 0.5; // Default progress value (middle)

            function updateTransform() {
                const rotateY = -35 + progress * 6; // -35deg -> -29deg 
                const translateZ = 30 + progress * 15; // 30px -> 45px 
                const translateY = -progress * 12; // lift up to 12px 
                
                perspectiveText.style.transform = `perspective(900px) rotateY(${rotateY}deg) skewX(-6deg) translateZ(${translateZ}px) translateY(${translateY}px)`;
            }

            // Initialize
            updateTransform();

            // Optional: Map mouse position to progress for parallax effect
            document.addEventListener('mousemove', (e) => {
                // Normalize mouse X to 0..1 range
                progress = e.clientX / window.innerWidth;
                updateTransform();
            });
        });
    </script>
</body>
</html>
