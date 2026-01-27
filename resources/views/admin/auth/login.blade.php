<!DOCTYPE html>
<html lang="en" translate="no">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google" content="notranslate">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://*.cloudflare.com https://challenges.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com https://*.cloudflare.com https://challenges.cloudflare.com data:; frame-src https://*.cloudflare.com https://challenges.cloudflare.com; connect-src 'self' https://*.cloudflare.com https://challenges.cloudflare.com; img-src 'self' data:;">
    <title>Admin Login - JerksHead</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onloadTurnstileCallback&render=explicit" async defer></script>
    <script>
        window.onloadTurnstileCallback = function () {
            const renderTurnstile = () => {
                const container = document.getElementById('turnstile-container');
                if (container) {
                    turnstile.render(container, {
                        sitekey: "{{ config('services.turnstile.key') }}",
                        appearance: 'always',
                        callback: function(token) {
                            console.log('Turnstile challenge success');
                        },
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Admin Panel</h1>
            <p class="text-gray-500 mt-2">Sign in to your account</p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600 text-center bg-green-50 p-2 rounded-md">
            {{ session('status') }}
        </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg relative" role="alert">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 placeholder-gray-400 shadow-sm"
                    placeholder="admin@example.com">
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                <input id="password" type="password" name="password" required
                    class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 placeholder-gray-400 shadow-sm"
                    placeholder="••••••••">
            </div>

            <!-- Remember Me -->
            <div class="block mb-6">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <!-- Turnstile Widget -->
            <div class="mb-6 flex justify-center">
                <div id="turnstile-container"></div>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 shadow-md transform hover:-translate-y-0.5">
                    Log in
                </button>
            </div>
        </form>

        <div class="mt-8 text-center border-t border-gray-100 pt-6">
            <a href="/" class="text-sm text-gray-500 hover:text-blue-600 transition-colors duration-200 flex items-center justify-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Landing
            </a>
        </div>
    </div>

</body>

</html>