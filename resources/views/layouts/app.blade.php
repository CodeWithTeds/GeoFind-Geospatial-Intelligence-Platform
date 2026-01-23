<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta-urls')
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
        crossorigin="" />
    <!-- CesiumJS -->
    <script src="https://cesium.com/downloads/cesiumjs/releases/1.119/Build/Cesium/Cesium.js"></script>
    <link href="https://cesium.com/downloads/cesiumjs/releases/1.119/Build/Cesium/Widgets/widgets.css" rel="stylesheet">
    @yield('styles')
    <style>
        .map-container {
            height: 400px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body class="bg-dark text-light">
    <div class="container py-4">
        <h1 class="text-light mb-4 border-bottom pb-3">@yield('header', 'Location Tracker')</h1>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/services/MapService.js') }}"></script>
    <script src="{{ asset('js/location.js') }}"></script>
    <script src="{{ asset('js/geohash-map.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let cesiumViewer = null; // Make viewer globally accessible
        let routeLine = null; // To keep track of the current route line
        let highlightedEntity = null; // To track the highlighted entity

        // --- IMPORTANT: Add your Mapbox Access Token here ---
        const MAPBOX_TOKEN = 'YOUR_MAPBOX_ACCESS_TOKEN'; 

        function initializeCesiumMap(containerId, data) {
            const container = document.getElementById(containerId);
            if (!container) {
                console.error(`Map container with id #${containerId} not found.`);
                return;
            }
            container.innerHTML = ''; // Clear previous content

            Cesium.Ion.defaultAccessToken = data.cesium_token;

            cesiumViewer = new Cesium.Viewer(containerId, {
                terrain: Cesium.Terrain.fromWorldTerrain(),
                infoBox: false, // Disable the default info box
                selectionIndicator: false // Disable the default selection indicator
            });

            cesiumViewer.scene.primitives.add(new Cesium.Cesium3DTileset({ url: Cesium.IonResource.fromAssetId(96188) }));

            cesiumViewer.camera.flyTo({
                destination: Cesium.Cartesian3.fromDegrees(data.center.longitude, data.center.latitude, 2500),
                orientation: {
                    heading: Cesium.Math.toRadians(0.0),
                    pitch: Cesium.Math.toRadians(-35.0),
                }
            });

            data.hotels.forEach(hotel => {
                const hotelEntity = cesiumViewer.entities.add({
                    name: hotel.name,
                    position: Cesium.Cartesian3.fromDegrees(hotel.longitude, hotel.latitude),
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

            const handler = new Cesium.ScreenSpaceEventHandler(cesiumViewer.scene.canvas);
            handler.setInputAction(function(click) {
                const pickedObject = cesiumViewer.scene.pick(click.position);
                if (Cesium.defined(pickedObject) && pickedObject.id && pickedObject.id.point) {
                    handleHotelSelection(pickedObject.id, data.center);
                }
            }, Cesium.ScreenSpaceEventType.LEFT_CLICK);
        }

        function handleHotelSelection(selectedEntity, center) {
            // Reset previously highlighted entity
            if (highlightedEntity && highlightedEntity.point) {
                highlightedEntity.point.pixelSize = 10;
                highlightedEntity.label.scale = 1.0;
            }

            // Highlight the new entity
            selectedEntity.point.pixelSize = 15;
            selectedEntity.label.scale = 1.2;
            highlightedEntity = selectedEntity;

            const endCartesian = selectedEntity.position.getValue(cesiumViewer.clock.currentTime);
            const endCartographic = Cesium.Cartographic.fromCartesian(endCartesian);
            
            const startCoords = [center.longitude, center.latitude];
            const endCoords = [Cesium.Math.toDegrees(endCartographic.longitude), Cesium.Math.toDegrees(endCartographic.latitude)];
            
            getRoute(startCoords, endCoords);
        }

        async function getRoute(start, end) {
            if (MAPBOX_TOKEN === 'YOUR_MAPBOX_ACCESS_TOKEN') {
                console.warn("Mapbox Access Token is not configured. Skipping route generation.");
                return;
            }
            const url = `https://api.mapbox.com/directions/v5/mapbox/driving/${start[0]},${start[1]};${end[0]},${end[1]}?geometries=geojson&access_token=${MAPBOX_TOKEN}`;
            
            try {
                const response = await fetch(url);
                const data = await response.json();
                if (data.routes && data.routes.length > 0) {
                    const routeCoordinates = data.routes[0].geometry.coordinates.flat();
                    drawRoute(routeCoordinates);
                } else {
                    console.error("No route found by Mapbox.");
                }
            } catch (error) {
                console.error("Error fetching route from Mapbox:", error);
            }
        }

        function drawRoute(coordinates) {
            if (routeLine) {
                cesiumViewer.entities.remove(routeLine);
            }
            routeLine = cesiumViewer.entities.add({
                polyline: {
                    positions: Cesium.Cartesian3.fromDegreesArray(coordinates),
                    width: 5,
                    material: Cesium.Color.YELLOW,
                    clampToGround: true
                }
            });
        }

        function flyToLocation(longitude, latitude) {
            if (cesiumViewer) {
                const targetEntity = cesiumViewer.entities.values.find(entity => {
                    if (!entity.position) return false;
                    const position = Cesium.Cartographic.fromCartesian(entity.position.getValue(cesiumViewer.clock.currentTime));
                    return Math.abs(Cesium.Math.toDegrees(position.longitude) - longitude) < 1e-6 &&
                           Math.abs(Cesium.Math.toDegrees(position.latitude) - latitude) < 1e-6;
                });

                if (targetEntity) {
                    cesiumViewer.flyTo(targetEntity).then(() => {
                        const center = { longitude: targetEntity.position.getValue(cesiumViewer.clock.currentTime).x, latitude: targetEntity.position.getValue(cesiumViewer.clock.currentTime).y};
                        // This part needs the original center point to draw the route, which is not available here.
                        // We can modify this later if needed. For now, just flying is fine.
                    });
                }
            }
        }
    </script>
    @stack('scripts')
</body>

</html> 