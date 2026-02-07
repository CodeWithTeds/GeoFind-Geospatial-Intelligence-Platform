<div class="w-full" x-data="{ submitting: false, turnstileVerified: {{ app()->environment('local', 'testing') ? 'true' : 'false' }} }" x-on:turnstile-verified.window="turnstileVerified = $event.detail.verified">
    <!-- Rate Limit Warning -->
    @if($secondsRemaining > 0)
    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/50 rounded text-center animate-pulse">
        <p class="text-red-400 text-sm font-bold uppercase tracking-widest">Access Denied</p>
        <p class="text-gray-400 text-xs mt-1">Too many attempts. Retry in <span class="text-white font-mono">{{ $secondsRemaining }}</span> seconds.</p>
    </div>
    @endif

    <form x-on:submit.prevent="submitting = true; $wire.register().then(() => submitting = false).catch(() => submitting = false)" class="space-y-4">
        <!-- Name -->
        <div class="group">
            <label for="name" class="block text-xs text-gray-400 uppercase tracking-widest mb-1 group-focus-within:text-yellow-500 transition-colors">Codename</label>
            <div class="relative">
                <input wire:model.live.debounce.300ms="name" id="name"
                    class="block w-full bg-black/50 border border-zinc-700 text-white p-3 rounded focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 focus:outline-none transition-all placeholder-zinc-600 @error('name') border-red-500 @enderror"
                    type="text" maxlength="20" placeholder="ENTER CODENAME (10-20)">
            </div>
            @error('name')
            <p class="mt-1 text-xs text-red-400 animate-pulse">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="group">
            <label for="email" class="block text-xs text-gray-400 uppercase tracking-widest mb-1 group-focus-within:text-yellow-500 transition-colors">Comm Link (Email)</label>
            <div class="relative">
                <input wire:model.live.debounce.500ms="email" id="email"
                    class="block w-full bg-black/50 border border-zinc-700 text-white p-3 rounded focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 focus:outline-none transition-all placeholder-zinc-600 @error('email') border-red-500 @enderror"
                    type="email" placeholder="ENTER GMAIL ADDRESS">
            </div>
            @error('email')
            <p class="mt-1 text-xs text-red-400 animate-pulse">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="group">
            <label for="password" class="block text-xs text-gray-400 uppercase tracking-widest mb-1 group-focus-within:text-yellow-500 transition-colors">Passcode</label>
            <div class="relative">
                <input wire:model.live.debounce.300ms="password" id="password"
                    class="block w-full bg-black/50 border border-zinc-700 text-white p-3 rounded focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 focus:outline-none transition-all placeholder-zinc-600 @error('password') border-red-500 @enderror"
                    type="password" maxlength="20" placeholder="10-20 CHARS">
            </div>
            @error('password')
            <p class="mt-1 text-xs text-red-400 animate-pulse">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="group">
            <label for="password_confirmation" class="block text-xs text-gray-400 uppercase tracking-widest mb-1 group-focus-within:text-yellow-500 transition-colors">Confirm Passcode</label>
            <div class="relative">
                <input wire:model.live.debounce.300ms="password_confirmation" id="password_confirmation"
                    class="block w-full bg-black/50 border border-zinc-700 text-white p-3 rounded focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 focus:outline-none transition-all placeholder-zinc-600"
                    type="password" maxlength="20" placeholder="CONFIRM">
            </div>
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
                window.dispatchEvent(new CustomEvent('turnstile-verified', {
                    detail: {
                        verified: !!token
                    }
                }));
            }
        </script>
        @endscript
        @error('turnstileToken')
        <p class="mt-1 text-xs text-red-400 animate-pulse text-center">{{ $message }}</p>
        @enderror
        @endif

        <!-- Submit Button -->
        <button type="submit"
            wire:loading.attr="disabled"
            wire:target="register"
            :disabled="submitting || {{ $secondsRemaining > 0 ? 'true' : 'false' }} || !turnstileVerified || {{ $errors->any() ? 'true' : 'false' }}"
            class="w-full bg-yellow-500 hover:bg-yellow-400 text-black font-bold py-4 px-6 rounded uppercase tracking-[0.2em] transition-all duration-300 hover:shadow-[0_0_20px_rgba(234,179,8,0.5)] transform hover:-translate-y-1 mt-6 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none">
            <span x-show="!submitting" wire:loading.remove wire:target="register">Initialize Profile</span>
            <span x-show="submitting" wire:loading wire:target="register" class="inline-flex items-center">
                Processing...
            </span>
        </button>

        <div class="text-center mt-6">
            <a href="{{ route('login') }}" class="text-sm text-yellow-500/70 hover:text-yellow-400 hover:underline transition-colors">
                Already have an account? Login
            </a>
        </div>
    </form>
</div>