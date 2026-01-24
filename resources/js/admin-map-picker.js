
export function initAdminMapPicker(config) {
    const {
        containerId,
        initialLat,
        initialLng,
        onLocationPicked,
        tilesetId // New config option
    } = config;

    if (!window.Cesium) {
        console.error('Cesium is not loaded.');
        return;
    }

    // Use our backend proxy for Cesium Ion requests
    Cesium.Ion.defaultServer = window.location.origin + '/api/cesium/';
    Cesium.Ion.defaultAccessToken = 'token-secured-by-backend-proxy';

    const viewer = new Cesium.Viewer(containerId, {
        terrain: Cesium.Terrain.fromWorldTerrain(),
        // Explicitly use Bing Maps Aerial (Asset ID 2) via our Proxy
        imageryProvider: new Cesium.IonImageryProvider({ assetId: 2 }),
        baseLayerPicker: true, 
        geocoder: true,
        animation: false,
        timeline: false,
        fullscreenButton: false, // We use our own custom fullscreen toggle
        sceneModePicker: true, // Useful for switching 2D/3D
        selectionIndicator: false,
        infoBox: false,
        navigationHelpButton: true, // Helpful for new users
        homeButton: true, // Helpful to reset view
    });

    // Load 3D Tileset if provided
    if (tilesetId) {
        try {
            viewer.scene.primitives.add(
                new Cesium.Cesium3DTileset({ 
                    url: Cesium.IonResource.fromAssetId(tilesetId) 
                })
            );
        } catch (e) {
            console.error('Error loading tileset:', e);
        }
    }

    // Remove default credits to clean up UI (optional, but check license requirements)
    // viewer.scene.debugShowFramesPerSecond = true;

    // Initial pin if exists
    let pinEntity = null;

    const addPin = (lat, lng) => {
        if (pinEntity) {
            viewer.entities.remove(pinEntity);
        }

        pinEntity = viewer.entities.add({
            position: Cesium.Cartesian3.fromDegrees(lng, lat),
            point: {
                pixelSize: 10,
                color: Cesium.Color.YELLOW,
                outlineColor: Cesium.Color.BLACK,
                outlineWidth: 2,
                disableDepthTestDistance: Number.POSITIVE_INFINITY, // Always on top
            },
            label: {
                text: `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`,
                font: '14px sans-serif',
                fillColor: Cesium.Color.WHITE,
                outlineColor: Cesium.Color.BLACK,
                outlineWidth: 2,
                style: Cesium.LabelStyle.FILL_AND_OUTLINE,
                pixelOffset: new Cesium.Cartesian2(0, -20),
                verticalOrigin: Cesium.VerticalOrigin.BOTTOM,
                disableDepthTestDistance: Number.POSITIVE_INFINITY,
            }
        });
    };

    if (initialLat && initialLng) {
        addPin(initialLat, initialLng);
        viewer.camera.flyTo({
            destination: Cesium.Cartesian3.fromDegrees(initialLng, initialLat, 10000)
        });
    } else {
        // Default view (Manila) if no pin
        viewer.camera.flyTo({
            destination: Cesium.Cartesian3.fromDegrees(120.9842, 14.5995, 2000),
            orientation: {
                heading: 0.0,
                pitch: Cesium.Math.toRadians(-35.0), // Match game map pitch for consistency
            }
        });
    }

    // Click handler
    const handler = new Cesium.ScreenSpaceEventHandler(viewer.scene.canvas);
    handler.setInputAction((click) => {
        const ray = viewer.camera.getPickRay(click.position);
        const cartesian = viewer.scene.globe.pick(ray, viewer.scene);

        if (cartesian) {
            const cartographic = Cesium.Cartographic.fromCartesian(cartesian);
            const lng = Cesium.Math.toDegrees(cartographic.longitude);
            const lat = Cesium.Math.toDegrees(cartographic.latitude);

            addPin(lat, lng);

            if (onLocationPicked) {
                onLocationPicked(lat, lng);
            }
        }
    }, Cesium.ScreenSpaceEventType.LEFT_CLICK);
    
    // Make viewer available globally for debugging if needed
    window.adminMapViewer = viewer;
}
