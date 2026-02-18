@extends('layouts.admin')

@section('title', 'Location Tracker')

@section('meta-urls')
    @include('components.locations.meta-urls')
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <link href="https://cesium.com/downloads/cesiumjs/releases/1.119/Build/Cesium/Widgets/widgets.css" rel="stylesheet">
    <style>
        .map-container {
            height: 400px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
    </style>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.locations.create') }}" class="btn btn-success">
            <i class="fas fa-plus-circle me-2"></i>Add New Location
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            @include('components.locations.table')
        </div>

        <div class="col-lg-6">
            <h2 class="text-dark mb-4">Location Calculations</h2>

            <div class="accordion" id="calculationsAccordion">
                @include('components.locations.geohash-converter')
                @include('components.locations.reverse-geocoding')
                @include('components.locations.distance-calculator')
                @include('components.locations.midpoint-calculator')
                @include('components.locations.triangle-area-calculator')
                @include('components.locations.points-in-radius')
                @include('components.locations.bearing-calculator')
                @include('components.locations.convex-hull')
                @include('components.locations.grid-generator')
                @include('components.locations.heatmap-generator')
                @include('components.locations.cluster-finder')
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script src="https://cesium.com/downloads/cesiumjs/releases/1.119/Build/Cesium/Cesium.js"></script>
    
    <script src="{{ asset('js/services/MapService.js') }}"></script>
    <script src="{{ asset('js/location.js') }}"></script>
    <script src="{{ asset('js/geohash-map.js') }}"></script>
    
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

            // Use our backend proxy for Cesium Ion requests
            Cesium.Ion.defaultServer = window.location.origin + '/api/cesium/';
            // Set placeholder token to silence client-side warnings (real token is on server)
            Cesium.Ion.defaultAccessToken = 'token-secured-by-backend-proxy';

            cesiumViewer = new Cesium.Viewer(containerId, {
                terrain: Cesium.Terrain.fromWorldTerrain(),
                imageryProvider: new Cesium.IonImageryProvider({ assetId: 2 }), // Use Proxy for Bing Maps
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
                });
            });
        }
    </script>
@endsection
