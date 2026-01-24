/**
 * Landing Page Logic
 * Handles 3D map initialization, modal interactions, and mobile detection.
 */

// Static Configuration / Data
const CONFIG = {
    hotels: [
        { name: "Prof Alex The GOAT", lat: 14.5960, lon: 120.9720 },
    ],
    camera: {
        longitude: 120.9842,
        latitude: 14.5995,
        height: 2000,
        heading: 0.0,
        pitch: -35.0
    },
    tilesetId: 96188
};

document.addEventListener('DOMContentLoaded', () => {
    initPerspectiveText();
    initGameLogic();
});

/**
 * Initializes the 3D perspective text effect.
 */
function initPerspectiveText() {
    const perspectiveText = document.getElementById('perspective-text');
    if (!perspectiveText) return;

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

/**
 * Initializes game interactions (Modal, Map, Mobile Check).
 */
function initGameLogic() {
    const elements = {
        playBtn: document.getElementById('play-now-btn'),
        modal: document.getElementById('instruction-modal'),
        startBtn: document.getElementById('start-game-btn'),
        closeModalBtn: document.getElementById('close-modal-btn'),
        mapContainer: document.getElementById('game-map-container'),
        exitMapBtn: document.getElementById('exit-map-btn'),
    };

    // Guard clause if essential elements are missing
    if (!elements.modal) return;

    let viewer = null;

    // Show Modal
    if (elements.playBtn) {
        elements.playBtn.addEventListener('click', () => {
            elements.modal.classList.remove('hidden');
            elements.modal.classList.add('flex');
        });
    }

    // Close Modal
    if (elements.closeModalBtn) {
        elements.closeModalBtn.addEventListener('click', () => {
            elements.modal.classList.add('hidden');
            elements.modal.classList.remove('flex');
        });
    }

    // Start Game
    if (elements.startBtn && elements.mapContainer) {
        elements.startBtn.addEventListener('click', () => {
            // Mobile check removed to allow desktop access as per request
            // if (!isMobileDevice()) { ... }

            elements.modal.classList.add('hidden');
            elements.modal.classList.remove('flex');

            elements.mapContainer.classList.remove('hidden');
            elements.mapContainer.style.display = 'block';

            if (!viewer) {
                viewer = initializeMap();
            }
        });
    }

    // Exit Map
    if (elements.exitMapBtn && elements.mapContainer) {
        elements.exitMapBtn.addEventListener('click', () => {
            elements.mapContainer.classList.add('hidden');
            elements.mapContainer.style.display = 'none';
        });
    }
}

/**
 * Checks if the user is on a mobile device.
 * @returns {boolean}
 */
function isMobileDevice() {
    return window.innerWidth <= 768 || /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

/**
 * Initializes the Cesium Map Viewer.
 * @returns {Cesium.Viewer|null}
 */
function initializeMap() {
    if (typeof Cesium === 'undefined') {
        console.error('Cesium is not loaded.');
        return null;
    }

    // Securely access configuration from window.AppConfig
    const cesiumToken = window.AppConfig?.cesium?.token;

    if (!cesiumToken) {
        console.error('Cesium token is missing in AppConfig.');
        return null;
    }

    Cesium.Ion.defaultAccessToken = cesiumToken;

    // Initialize Cesium Viewer with default widgets enabled (matches Cesium Viewer demo)
    const viewer = new Cesium.Viewer('cesiumContainer', {
        terrain: Cesium.Terrain.fromWorldTerrain()
    });

    // Load 3D Tiles
    try {
        viewer.scene.primitives.add(new Cesium.Cesium3DTileset({ url: Cesium.IonResource.fromAssetId(CONFIG.tilesetId) }));
    } catch (e) {
        console.error('Error loading tileset:', e);
    }

    // Set Camera View
    viewer.camera.flyTo({
        destination: Cesium.Cartesian3.fromDegrees(CONFIG.camera.longitude, CONFIG.camera.latitude, CONFIG.camera.height),
        orientation: {
            heading: Cesium.Math.toRadians(CONFIG.camera.heading),
            pitch: Cesium.Math.toRadians(CONFIG.camera.pitch),
        }
    });

    addHotelsToMap(viewer, CONFIG.hotels);

    // Add click handler for pinning location
    viewer.screenSpaceEventHandler.setInputAction(function(click) {
        const cartesian = viewer.scene.pickPosition(click.position);
        if (cartesian) {
            const cartographic = Cesium.Cartographic.fromCartesian(cartesian);
            const longitudeString = Cesium.Math.toDegrees(cartographic.longitude).toFixed(6);
            const latitudeString = Cesium.Math.toDegrees(cartographic.latitude).toFixed(6);

            console.log(`Pinned Location: Lat ${latitudeString}, Lon ${longitudeString}`);

            // Add a pin at the clicked location
            viewer.entities.add({
                position: cartesian,
                point: {
                    pixelSize: 10,
                    color: Cesium.Color.YELLOW,
                    outlineColor: Cesium.Color.BLACK,
                    outlineWidth: 2,
                    heightReference: Cesium.HeightReference.CLAMP_TO_GROUND
                },
                label: {
                    text: `Lat: ${latitudeString}, Lon: ${longitudeString}`,
                    font: '12pt monospace',
                    style: Cesium.LabelStyle.FILL_AND_OUTLINE,
                    outlineWidth: 2,
                    verticalOrigin: Cesium.VerticalOrigin.BOTTOM,
                    pixelOffset: new Cesium.Cartesian2(0, -10),
                    heightReference: Cesium.HeightReference.CLAMP_TO_GROUND,
                    disableDepthTestDistance: Number.POSITIVE_INFINITY
                }
            });
        }
    }, Cesium.ScreenSpaceEventType.LEFT_CLICK);

    return viewer;
}

/**
 * Adds hotel markers to the map.
 * @param {Cesium.Viewer} viewer 
 * @param {Array} hotels 
 */
function addHotelsToMap(viewer, hotels) {
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
