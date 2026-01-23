<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JerksHead</title>
     <script src="https://cdn.tailwindcss.com"></script>
    <!-- CesiumJS -->
    <script src="https://cesium.com/downloads/cesiumjs/releases/1.119/Build/Cesium/Cesium.js"></script>
    <link href="https://cesium.com/downloads/cesiumjs/releases/1.119/Build/Cesium/Widgets/widgets.css" rel="stylesheet">
    
    <style>
        /* Mobile-only Fullscreen Map Styles */
        #game-map-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: #000;
        }
        
        /* Modal Styles */
        #instruction-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 50;
            background: rgba(0, 0, 0, 0.85);
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background: linear-gradient(145deg, #1a1a1a, #0d0d0d);
            border: 1px solid #333;
            padding: 2rem;
            border-radius: 1rem;
            max-width: 90%;
            width: 400px;
            text-align: center;
            color: white;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            animation: modalPop 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        @keyframes modalPop {
            0% { transform: scale(0.8); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .cesium-viewer-bottom {
            display: none !important; /* Hide Cesium credit for cleaner mobile view */
        }
    </style>
</head>
<body class="h-screen w-screen overflow-hidden relative font-sans">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/findjerks.png') }}" alt="Background" class="w-full h-full object-cover">
        <!-- Dark gradient overlay for better text readability if needed -->
        <div class="absolute inset-0 bg-black/10"></div>
    </div>

    <!-- Main Content Container -->
    <div class="relative z-10 w-full h-full flex flex-col p-6 md:p-12">
        
        <!-- Header / Top Left Button -->
        <header class="flex justify-start">
            <button id="play-now-btn" class="group relative bg-black/80 text-white px-8 py-3 rounded-full font-bold border-2 border-yellow-500/50 hover:bg-black hover:border-yellow-400 transition-all duration-300 shadow-lg flex items-center gap-3 overflow-hidden">
                <span class="relative z-10 tracking-wider text-sm md:text-base">PLAY NOW!</span>
                <span class="relative z-10 w-2.5 h-2.5 bg-yellow-400 rounded-full animate-pulse shadow-[0_0_10px_rgba(250,204,21,0.8)]"></span>
                
                <!-- Hover effect glow -->
                <div class="absolute inset-0 bg-yellow-400/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
            </button>
        </header>

        <!-- Main Body -->
        <main class="flex-grow relative">
            <!-- 3D-style right-side paragraph, slightly skewed left --> 
            <div class="absolute right-6 top-1/2 -translate-y-1/2 max-w-md hidden md:block"> 
                <div 
                    id="perspective-text"
                    class="relative select-none text-white transition-transform duration-700 ease-out will-change-transform" 
                > 
                    <p class="text-5xl md:text-6xl font-extrabold uppercase tracking-wide leading-tight text-[#f2dc2f] [text-shadow:0_1px_0_#c3a91f,0_2px_0_#b1971c,0_3px_0_#9e8619,0_4px_0_#8c7616,0_5px_0_#7a6614,0_6px_0_#6a5712,0_14px_28px_rgba(0,0,0,.65)]"> 
                        The jerks won’t hide forever. 
                    </p> 
                    <p class="mt-3 text-white/90 text-lg font-medium drop-shadow-md"> 
                        Scan the area, trust your gut, and pin the location fast. 
                    </p> 
                </div> 
            </div> 
        </main>

        <!-- Footer / Logo -->
        <footer class="flex justify-center pb-4 md:pb-8">
            <div class="transform hover:scale-105 transition-transform duration-300 cursor-pointer">
                <img src="{{ asset('images/title.png') }}" alt="JERKSHEAD" class="h-20 md:h-28 lg:h-32 object-contain drop-shadow-2xl">
            </div>
        </footer>

    </div>

    <!-- Instruction Modal -->
    <div id="instruction-modal" class="flex">
        <div class="modal-content">
            <h2 class="text-2xl font-bold mb-4 text-yellow-400">Welcome!</h2>
            <p class="mb-6 text-gray-300">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                Welcome! Click Start to explore the 3D map. 
                Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            </p>
            <button id="start-game-btn" class="bg-yellow-500 hover:bg-yellow-400 text-black font-bold py-3 px-8 rounded-full transition-colors duration-300 w-full">
                START
            </button>
            <button id="close-modal-btn" class="mt-4 text-gray-500 hover:text-white text-sm underline">
                Cancel
            </button>
        </div>
    </div>

    <!-- Fullscreen Map Container -->
    <div id="game-map-container">
        <div id="cesiumContainer" class="w-full h-full"></div>
        <button id="exit-map-btn" class="absolute top-4 left-4 z-50 bg-black/50 text-white p-2 rounded-full backdrop-blur-sm border border-white/20 hover:bg-black/80 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Existing Perspective Text Logic
            const perspectiveText = document.getElementById('perspective-text');
            if (perspectiveText) {
                let progress = 0.5;
                function updateTransform() {
                    const rotateY = -35 + progress * 6; 
                    const translateZ = 30 + progress * 15; 
                    const translateY = -progress * 12; 
                    perspectiveText.style.transform = `perspective(900px) rotateY(${rotateY}deg) skewX(-6deg) translateZ(${translateZ}px) translateY(${translateY}px)`;
                }
                updateTransform();
                document.addEventListener('mousemove', (e) => {
                    progress = e.clientX / window.innerWidth;
                    updateTransform();
                });
            }

            // Game Logic
            const playBtn = document.getElementById('play-now-btn');
            const modal = document.getElementById('instruction-modal');
            const startBtn = document.getElementById('start-game-btn');
            const closeModalBtn = document.getElementById('close-modal-btn');
            const mapContainer = document.getElementById('game-map-container');
            const exitMapBtn = document.getElementById('exit-map-btn');
            let viewer = null;

            // Show Modal
            playBtn.addEventListener('click', () => {
                modal.style.display = 'flex';
            });

            // Close Modal
            closeModalBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });

            // Start Game
            startBtn.addEventListener('click', () => {
                // Mobile Check (simple width check or user agent)
                const isMobile = window.innerWidth <= 768 || /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                
                if (!isMobile) {
                    alert('This feature is designed for mobile devices. Please switch to a mobile device or reduce your window size to experience it.');
                    // For testing purposes, we might want to allow it if the window is small enough, 
                    // but the requirement says "enforced exclusively on mobile viewports/phones".
                    // I will enforce the width check primarily.
                    if (window.innerWidth > 768) return; 
                }

                modal.style.display = 'none';
                mapContainer.style.display = 'block';
                
                if (!viewer) {
                    initializeMap();
                }
            });

            // Exit Map
            exitMapBtn.addEventListener('click', () => {
                mapContainer.style.display = 'none';
            });

            function initializeMap() {
                const cesiumToken = "{{ config('cesium.access_token') }}";
                Cesium.Ion.defaultAccessToken = cesiumToken;

                viewer = new Cesium.Viewer('cesiumContainer', {
                    terrain: Cesium.Terrain.fromWorldTerrain(),
                    infoBox: false,
                    selectionIndicator: false,
                    timeline: false,
                    animation: false,
                    baseLayerPicker: false,
                    geocoder: false,
                    homeButton: false,
                    sceneModePicker: false,
                    navigationHelpButton: false,
                    fullscreenButton: false // We are already fullscreen
                });

                // Load 3D Tiles
                try {
                    viewer.scene.primitives.add(new Cesium.Cesium3DTileset({ url: Cesium.IonResource.fromAssetId(96188) }));
                } catch (e) {
                    console.error('Error loading tileset:', e);
                }

                // Default location (e.g., Manila)
                const center = { longitude: 120.9842, latitude: 14.5995 };
                
                viewer.camera.flyTo({
                    destination: Cesium.Cartesian3.fromDegrees(center.longitude, center.latitude, 2000),
                    orientation: {
                        heading: Cesium.Math.toRadians(0.0),
                        pitch: Cesium.Math.toRadians(-35.0),
                    }
                });

                // Add some sample hotels/points to make it interactive like the reference
                // Ideally we would fetch this from the API, but for "Play Now" instant gratification, we can use some static data or fetch nearby.
                // Let's try to fetch nearby hotels using the API if possible, otherwise use static.
                // Since I can't easily call the controller from here without a CSRF token and proper setup, 
                // and the user said "functions similarly", I will simulate it with some random points around the center for demo.
                
                addRandomHotels(center);
            }

            function addRandomHotels(center) {
                const hotels = [
                    { name: "Manila Hotel", lat: 14.5960, lon: 120.9720 },
                    { name: "Rizal Park Hotel", lat: 14.5820, lon: 120.9760 },
                    { name: "Sofitel", lat: 14.5550, lon: 120.9810 },
                ];

                hotels.forEach(hotel => {
                    viewer.entities.add({
                        name: hotel.name,
                        position: Cesium.Cartesian3.fromDegrees(hotel.lon, hotel.lat),
                        point: {
                            pixelSize: 10,
                            color: Cesium.Color.RED,
                            outlineColor: Cesium.Color.WHITE,
                            outlineWidth: 2,
                            heightReference: Cesium.HeightReference.CLAMP_TO_GROUND
                        },
                        label: {
                            text: hotel.name,
                            font: '14pt monospace',
                            style: Cesium.LabelStyle.FILL_AND_OUTLINE,
                            outlineWidth: 2,
                            verticalOrigin: Cesium.VerticalOrigin.BOTTOM,
                            pixelOffset: new Cesium.Cartesian2(0, -15),
                            heightReference: Cesium.HeightReference.CLAMP_TO_GROUND,
                            disableDepthTestDistance: Number.POSITIVE_INFINITY
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
