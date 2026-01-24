<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JerksHead - Play</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- CesiumJS -->
    <script src="https://cesium.com/downloads/cesiumjs/releases/1.119/Build/Cesium/Cesium.js"></script>
    <link href="https://cesium.com/downloads/cesiumjs/releases/1.119/Build/Cesium/Widgets/widgets.css" rel="stylesheet">
    
    <!-- Vite Assets -->
    @vite(['resources/css/landing.css', 'resources/js/play.js'])
</head>
<body class="h-screen w-screen overflow-hidden relative font-sans bg-black">

    <!-- Fullscreen Map Container -->
    <div id="game-map-container" class="fixed top-0 left-0 w-full h-full z-[1] bg-black">
        <div id="cesiumContainer" class="w-full h-full"></div>
        <a href="/" id="exit-map-btn" aria-label="Close Map" class="absolute top-4 left-4 z-50 bg-black/50 text-white p-2 rounded-full backdrop-blur-sm border border-white/20 hover:bg-black/80 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
    </div>

    <!-- Global App Config -->
    <script>
        window.AppConfig = {
            // cesium token removed for security
        }
    </script>
</body>
</html>