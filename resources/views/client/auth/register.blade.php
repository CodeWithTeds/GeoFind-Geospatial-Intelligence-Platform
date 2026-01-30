<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Oxbit Registration</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS & Custom Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/auth.css'])

    <!-- Cloudflare Turnstile (Production Only) -->
    @if(!app()->environment('local'))
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
</head>
<body class="h-screen w-screen overflow-hidden bg-black text-white">

    <div class="flex flex-col md:flex-row h-full w-full">
        
        <!-- Left Panel: Brand Imagery -->
        <div class="hidden md:flex w-1/2 h-full relative overflow-hidden justify-center items-center">
            <img src="{{ asset('images/p1.png') }}" 
                 alt="Oxbit Access" 
                 class="max-w-[80%] max-h-[80%] object-contain drop-shadow-2xl">
        </div>

        <!-- Right Panel: Registration Form -->
        <div class="w-full md:w-1/2 h-full bg-zinc-900 flex flex-col justify-center items-center p-8 relative overflow-y-auto">
            
            <!-- Tech borders -->
            <div class="absolute top-0 right-0 w-32 h-32 border-t-2 border-r-2 border-yellow-500/30 rounded-tr-3xl"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 border-b-2 border-l-2 border-yellow-500/30 rounded-bl-3xl"></div>

            <div class="w-full max-w-md z-10 py-8">
                <div class="mb-8 text-center">
                    <h2 class="text-yellow-500 text-xs tracking-[0.3em] uppercase mb-2">New Operative</h2>
                    <h1 class="text-4xl font-bold text-white uppercase tracking-wider drop-shadow-lg">Enlist</h1>
                    <div class="h-1 w-24 bg-yellow-500 mx-auto mt-4 rounded-full"></div>
                </div>

                @livewire('client.auth.register')
            </div>
            
            <!-- Footer -->
            <div class="absolute bottom-4 text-center text-[10px] text-zinc-600 uppercase tracking-widest">
                System Version 2.0.4 &bull; Secure Connection
            </div>
        </div>
    </div>

</body>
</html>
