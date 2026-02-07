<div class="w-full" x-data="{ submitting: false, turnstileVerified: {{ app()->environment('local', 'testing') ? 'true' : 'false' }} }" x-on:turnstile-verified.window="turnstileVerified = $event.detail.verified">
    <!-- Rate Limit Warning -->
    @if($secondsRemaining > 0)
    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/50 rounded text-center animate-pulse">
        <p class="text-red-400 text-sm font-bold uppercase tracking-widest">Access Denied</p>
        <p class="text-gray-400 text-xs mt-1">Too many attempts. Retry in <span class="text-white font-mono">{{ $secondsRemaining }}</span> seconds.</p>
    </div>
    @endif

    <form x-on:submit.prevent="submitting = true; $wire.login().then(() => submitting = false).catch(() => submitting = false)" class="space-y-6">
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
            window.dispatchEvent(new CustomEvent('turnstile-verified', { detail: { verified: !!token } }));
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
                wire:target="login"
                :disabled="submitting || {{ $secondsRemaining > 0 ? 'true' : 'false' }} || !turnstileVerified"
                class="w-full bg-yellow-500 hover:bg-yellow-400 text-black font-bold py-4 px-6 rounded uppercase tracking-[0.2em] transition-all duration-300 hover:shadow-[0_0_20px_rgba(234,179,8,0.5)] transform hover:-translate-y-1 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none">
            <span x-show="!submitting" wire:loading.remove wire:target="login">Authenticate</span>
            <span x-show="submitting" wire:loading wire:target="login" class="inline-flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Authenticating...
            </span>
        </button>

        <div class="mt-6 text-center text-sm text-gray-400">
            <span class="uppercase tracking-widest">New Operative?</span>
            <a href="{{ route('register') }}" class="ml-2 text-yellow-500 hover:text-yellow-400 font-bold hover:underline transition-colors uppercase tracking-widest">
                Register Now
            </a>
        </div>
    </form>
</div>
