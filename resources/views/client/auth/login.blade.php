<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Oxbit Access Control</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Cloudflare Turnstile (Production Only) -->
    @if(!app()->environment('local'))
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif

    <style>
        body {
            font-family: 'Chakra Petch', sans-serif;
        }

        .parallax-bg {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        /* Custom scrollbar for game feel */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #18181b;
        }

        ::-webkit-scrollbar-thumb {
            background: #eab308;
            border-radius: 4px;
        }
    </style>
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

        <!-- Right Panel: Login Form -->
        <div class="w-full md:w-1/2 h-full bg-zinc-900 flex flex-col justify-center items-center p-8 relative">

            <!-- Tech borders -->
            <div class="absolute top-0 right-0 w-32 h-32 border-t-2 border-r-2 border-yellow-500/30 rounded-tr-3xl"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 border-b-2 border-l-2 border-yellow-500/30 rounded-bl-3xl"></div>

            <div class="w-full max-w-md z-10">
                <div class="mb-10 text-center">
                    <h2 class="text-yellow-500 text-xs tracking-[0.3em] uppercase mb-2">Secure Uplink</h2>
                    <h1 class="text-4xl font-bold text-white uppercase tracking-wider drop-shadow-lg">Identify</h1>
                    <div class="h-1 w-24 bg-yellow-500 mx-auto mt-4 rounded-full"></div>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                <div class="mb-4 text-sm text-green-400 border border-green-500/30 bg-green-900/20 p-3 rounded">
                    {{ session('status') }}
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div class="group">
                        <label for="email" class="block text-xs text-gray-400 uppercase tracking-widest mb-2 group-focus-within:text-yellow-500 transition-colors">Operative Email</label>
                        <input id="email" class="block w-full bg-black/50 border border-zinc-700 text-white p-4 rounded focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 focus:outline-none transition-all placeholder-zinc-600"
                            type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="ENTER CREDENTIALS">
                        @error('email')
                        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="group">
                        <label for="password" class="block text-xs text-gray-400 uppercase tracking-widest mb-2 group-focus-within:text-yellow-500 transition-colors">Passcode</label>
                        <input id="password" class="block w-full bg-black/50 border border-zinc-700 text-white p-4 rounded focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 focus:outline-none transition-all placeholder-zinc-600"
                            type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                        @error('password')
                        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                            <div class="relative">
                                <input id="remember_me" type="checkbox" class="sr-only peer" name="remember">
                                <div class="w-5 h-5 border border-zinc-600 bg-black/50 rounded peer-checked:bg-yellow-500 peer-checked:border-yellow-500 transition-all"></div>
                                <svg class="absolute w-3 h-3 text-black top-1 left-1 opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="ml-2 text-sm text-gray-400 group-hover:text-gray-300 transition-colors">Maintain Uplink</span>
                        </label>

                        @if (Route::has('password.request'))
                        <a class="text-sm text-yellow-500/70 hover:text-yellow-400 hover:underline transition-colors" href="{{ route('password.request') }}">
                            Lost Passcode?
                        </a>
                        @endif
                    </div>

                    <!-- Turnstile (Production Only) -->
                    @if(!app()->environment('local'))
                    <div class="mt-4 flex justify-center">
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.key') }}" data-theme="dark"></div>
                    </div>
                    @error('cf-turnstile-response')
                    <p class="mt-2 text-xs text-red-400 text-center">{{ $message }}</p>
                    @enderror
                    @endif

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-400 text-black font-bold py-4 px-6 rounded uppercase tracking-[0.2em] transition-all duration-300 hover:shadow-[0_0_20px_rgba(234,179,8,0.5)] transform hover:-translate-y-1">
                        Authenticate
                    </button>

                    <div class="mt-6 text-center text-sm text-gray-400">
                        <span class="uppercase tracking-widest">New Operative?</span>
                        <a href="{{ route('register') }}" class="ml-2 text-yellow-500 hover:text-yellow-400 font-bold hover:underline transition-colors uppercase tracking-widest">
                            Register Now
                        </a>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="absolute bottom-6 text-center text-[10px] text-zinc-600 uppercase tracking-widest">
                System Version 2.0.4 &bull; Prof Alex The G.O.A.T
            </div>
        </div>
    </div>

</body>

</html>