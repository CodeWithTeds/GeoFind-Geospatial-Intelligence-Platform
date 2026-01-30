<div class="w-full">
    <form wire:submit="login" class="space-y-6">
        <!-- Email Address -->
        <div class="group">
            <label for="email" class="block text-xs text-gray-400 uppercase tracking-widest mb-2 group-focus-within:text-yellow-500 transition-colors">Operative Email</label>
            <input wire:model="email" id="email" class="block w-full bg-black/50 border border-zinc-700 text-white p-4 rounded focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 focus:outline-none transition-all placeholder-zinc-600 @error('email') border-red-500 @enderror" 
                   type="email" required autofocus autocomplete="username" placeholder="ENTER CREDENTIALS">
            @error('email')
                <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="group">
            <label for="password" class="block text-xs text-gray-400 uppercase tracking-widest mb-2 group-focus-within:text-yellow-500 transition-colors">Passcode</label>
            <input wire:model="password" id="password" class="block w-full bg-black/50 border border-zinc-700 text-white p-4 rounded focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 focus:outline-none transition-all placeholder-zinc-600 @error('password') border-red-500 @enderror" 
                   type="password" required autocomplete="current-password" placeholder="••••••••">
            @error('password')
                <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <div class="relative">
                    <input wire:model="remember" id="remember_me" type="checkbox" class="sr-only peer">
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
    <div class="mt-4 flex justify-center" wire:ignore>
        <div id="turnstile-container"></div>
    </div>
    @script
    <script>
        window.turnstileCallback = (token) => {
            $wire.set('turnstileToken', token);
        }
    </script>
    @endscript
    @error('turnstileToken')
        <p class="mt-2 text-xs text-red-400 text-center">{{ $message }}</p>
    @enderror
    @endif

        <!-- Submit Button -->
        <button type="submit" 
                wire:loading.attr="disabled"
                class="w-full bg-yellow-500 hover:bg-yellow-400 text-black font-bold py-4 px-6 rounded uppercase tracking-[0.2em] transition-all duration-300 hover:shadow-[0_0_20px_rgba(234,179,8,0.5)] transform hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed">
            <span wire:loading.remove>Authenticate</span>
            <span wire:loading>Authenticating...</span>
        </button>

        <div class="mt-6 text-center text-sm text-gray-400">
            <span class="uppercase tracking-widest">New Operative?</span>
            <a href="{{ route('register') }}" class="ml-2 text-yellow-500 hover:text-yellow-400 font-bold hover:underline transition-colors uppercase tracking-widest">
                Register Now
            </a>
        </div>
    </form>
</div>
