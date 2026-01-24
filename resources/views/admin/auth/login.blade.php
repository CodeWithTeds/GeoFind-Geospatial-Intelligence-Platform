<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - JerksHead</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-white h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-gray-800 p-8 rounded-lg shadow-2xl border border-gray-700">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-yellow-500">Admin Panel</h1>
            <p class="text-gray-400 mt-2">Sign in to your account</p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-400 text-center">
            {{ session('status') }}
        </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
        <div class="mb-4 bg-red-900/50 border border-red-500 text-red-200 px-4 py-3 rounded relative" role="alert">
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
                <label for="email" class="block text-gray-300 text-sm font-bold mb-2">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition duration-200 placeholder-gray-500"
                    placeholder="admin@example.com">
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-gray-300 text-sm font-bold mb-2">Password</label>
                <input id="password" type="password" name="password" required
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition duration-200 placeholder-gray-500"
                    placeholder="••••••••">
            </div>

            <!-- Remember Me -->
            <div class="block mb-6">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded bg-gray-700 border-gray-600 text-yellow-500 shadow-sm focus:ring-yellow-500">
                    <span class="ml-2 text-sm text-gray-400">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200 transform hover:scale-[1.02]">
                    Log in
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <a href="/" class="text-sm text-gray-500 hover:text-gray-300 underline">Back to Landing</a>
        </div>
    </div>

</body>

</html>