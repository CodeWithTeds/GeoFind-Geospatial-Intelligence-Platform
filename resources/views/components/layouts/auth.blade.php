<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Oxbit Access Control' }}</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Cloudflare Turnstile (Production Only) -->
    @if(!app()->environment('local'))
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onloadTurnstileCallback&render=explicit" async defer></script>
    <script>
        window.onloadTurnstileCallback = function () {
            const renderTurnstile = () => {
                const container = document.getElementById('turnstile-container');
                if (container) {
                    turnstile.render(container, {
                        sitekey: "{{ config('services.turnstile.key') }}",
                        theme: 'dark',
                        callback: function(token) {
                            if (typeof window.turnstileCallback === 'function') {
                                window.turnstileCallback(token);
                            }
                        },
                        'expired-callback': function() {
                             if (typeof window.turnstileCallback === 'function') {
                                window.turnstileCallback(null);
                            }
                        }
                    });
                }
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', renderTurnstile);
            } else {
                renderTurnstile();
            }
        };
    </script>
    @endif

    <style>
        body { font-family: 'Chakra Petch', sans-serif; }
        
        /* Hide browser default input decorations */
        input::-ms-clear,
        input::-ms-reveal {
            display: none;
        }
        input::-webkit-search-decoration,
        input::-webkit-search-cancel-button,
        input::-webkit-search-results-button,
        input::-webkit-search-results-decoration {
            display: none;
        }
        /* Hide Chrome password eye */
        input[type="password"]::-webkit-inner-spin-button,
        input[type="password"]::-webkit-outer-spin-button { 
            -webkit-appearance: none;
            margin: 0;
        }
        /* Fix Autofill background */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active{
            -webkit-box-shadow: 0 0 0 30px rgba(0,0,0,0.5) inset !important;
            -webkit-text-fill-color: white !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>  
    @stack('styles')
</head>
<body class="h-screen w-screen overflow-hidden bg-black text-white">

    <div class="flex flex-col md:flex-row h-full w-full">
        
       
        <!-- Left Panel: Brand Imagery -->
        <div class="hidden md:flex w-1/2 h-full relative overflow-hidden justify-center items-center">
            <!-- Image displayed naturally -->
            <img src="{{ asset('images/p1.avif') }}" 
                 alt="Oxbit Access" 
                 class="max-w-[80%] max-h-[80%] object-contain drop-shadow-2xl">
        </div>

        <!-- Right Panel: Form -->
        <div class="w-full md:w-1/2 h-full bg-zinc-900 flex flex-col justify-center items-center p-8 relative">
            
            <!-- Tech borders -->
            <div class="absolute top-0 right-0 w-32 h-32 border-t-2 border-r-2 border-yellow-500/30 rounded-tr-3xl"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 border-b-2 border-l-2 border-yellow-500/30 rounded-bl-3xl"></div>

            <div class="w-full max-w-md z-10">
                <div class="mb-10 text-center">
                    <h2 class="text-yellow-500 text-xs tracking-[0.3em] uppercase mb-2">{{ $header ?? 'Secure Uplink' }}</h2>
                    <h1 class="text-4xl font-bold text-white uppercase tracking-wider drop-shadow-lg">{{ $pageTitle ?? 'Identify' }}</h1>
                    <div class="h-1 w-24 bg-yellow-500 mx-auto mt-4 rounded-full"></div>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 text-sm text-green-400 border border-green-500/30 bg-green-900/20 p-3 rounded">
                        {{ session('status') }}
                    </div>
                @endif

                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="absolute bottom-6 text-center text-[10px] text-zinc-600 uppercase tracking-widest">
                System Version 2.0.4 &bull; Prof Alex The G.O.A.T
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>