<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - JerksHead</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            font-family: 'Chakra Petch', sans-serif;
            background-color: #000000;
        }
    </style>
</head>
<body class="h-screen w-screen overflow-hidden relative text-white bg-black">
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

    <!-- Center Content -->
    <main class="relative z-10 w-full h-full flex flex-col items-center justify-center p-4">
        <!-- Lazy Loaded Image -->
        <div class="relative group flex items-center justify-center">
            <!-- Glow effect behind image -->
            <div class="absolute inset-0 bg-yellow-500/20 blur-3xl rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
            
            <img 
                src="{{ asset('images/p1.png') }}" 
                alt="Character" 
                class="relative max-w-[90%] max-h-[50vh] w-auto h-auto object-contain drop-shadow-2xl transition-transform duration-500 hover:scale-105"
                loading="lazy"
            >
        </div>

        <!-- Text Section (Bottom) -->
        <div class="w-full max-w-4xl text-center mt-4">
            <h1 class="text-3xl md:text-5xl font-black uppercase tracking-tighter transform -skew-x-6 text-transparent bg-clip-text bg-gradient-to-r from-white via-gray-200 to-gray-400 drop-shadow-[0_5px_5px_rgba(0,0,0,0.5)] select-none"
                style="text-shadow: 1px 1px 0 #ccc, 2px 2px 0 #bbb, 3px 3px 0 #aaa, 4px 4px 0 #999, 5px 5px 0 #888;">
                FindingJerks
            </h1>
            <h2 class="text-sm md:text-base font-bold uppercase tracking-widest text-yellow-500 mt-2 mb-4 transform -skew-x-6 drop-shadow-lg">
                Geospatial Intelligence Platform
            </h2>
        </div>
    </main>

    <!-- Footer -->
    @include('client.partials.footer')

    <!-- Custom JS for Mobile Menu (Inline for simplicity as we moved to Tailwind) -->
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
    </script>
    
    <!-- Finisher Header Scripts -->
    <script src="{{ asset('js/libs/finisher-header.es5.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/finisher-init.js') }}" type="text/javascript"></script>
</body>
</html>
