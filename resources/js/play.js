/**
 * Play Page Logic
 * Handles 3D map initialization and modal interactions.
 */
import './cesium-config';

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

let isGameActive = false;

document.addEventListener('DOMContentLoaded', () => {
    // Initialize map immediately
    const viewer = initializeMap();
    
    if (viewer) {
        fetchQuestion();
        setupUI(viewer);
    }
});

async function fetchQuestion() {
    try {
        const targetLevel = window.AppConfig.targetLevel;
        let url = '/api/questions';
        
        if (targetLevel) {
            url = `/api/questions/level/${targetLevel}`;
        }

        // Fetch questions from API
        const response = await fetch(url);
        if (!response.ok) throw new Error('Network response was not ok');
        
        const data = await response.json();
        
        let question;
        if (targetLevel) {
            question = data;
        } else {
            const questions = data.data || data; // Handle pagination or direct array
            if (questions && questions.length > 0) {
                // Get the most recent question
                question = questions[questions.length - 1];
            }
        }
        
        if (question) {
            // Update UI
            document.getElementById('question-title').textContent = question.title;
            document.getElementById('question-description').textContent = question.description;
            document.getElementById('question-difficulty').textContent = (question.difficulty || 'NORMAL').toUpperCase();
            document.getElementById('question-tolerance').textContent = (question.tolerance_meters || 50) + ' METERS';
        } else {
            document.getElementById('question-title').textContent = "NO MISSIONS AVAILABLE";
            document.getElementById('question-description').textContent = "No active operations found in this sector. Stand by for future updates.";
        }
    } catch (error) {
        console.error('Error fetching question:', error);
        document.getElementById('question-title').textContent = "CONNECTION FAILURE";
        document.getElementById('question-description').textContent = "Unable to establish uplink with Mission Control. Check secure connection.";
    }
}

function setupUI(viewer) {
    const confirmBtn = document.getElementById('confirm-question-btn');
    const panel = document.getElementById('question-panel');
    
    confirmBtn.addEventListener('click', () => {
        // Enable game mode
        isGameActive = true;
        
        // Hide panel (slide down)
        panel.style.transform = 'translateY(120%)';
        
        // Optional: Reset camera or focus on start area if needed
    });
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

    // Use our backend proxy for Cesium Ion requests
    Cesium.Ion.defaultServer = window.location.origin + '/api/cesium/';
    Cesium.Ion.defaultAccessToken = 'token-secured-by-backend-proxy';

    // Initialize Cesium Viewer with default widgets enabled
    const viewer = new Cesium.Viewer('cesiumContainer', {
        terrain: Cesium.Terrain.fromWorldTerrain(),
        // Explicitly use Bing Maps Aerial (Asset ID 2) via our Proxy
        imageryProvider: new Cesium.IonImageryProvider({ assetId: 2 }),
        baseLayerPicker: true, 
        geocoder: true,
        animation: false,
        timeline: false
    });

    // Load 3D Tiles
    try {
        viewer.scene.primitives.add(new Cesium.Cesium3DTileset({ url: Cesium.IonResource.fromAssetId(CONFIG.tilesetId) }));
    } catch (e) {
        console.error('Error loading tileset:', e);
    }

    // Set Camera View
    viewer.camera.setView({
        destination: Cesium.Cartesian3.fromDegrees(CONFIG.camera.longitude, CONFIG.camera.latitude, CONFIG.camera.height),
        orientation: {
            heading: Cesium.Math.toRadians(CONFIG.camera.heading),
            pitch: Cesium.Math.toRadians(CONFIG.camera.pitch),
        }
    });

    addHotelsToMap(viewer, CONFIG.hotels);

    // Add click handler for pinning location
    viewer.screenSpaceEventHandler.setInputAction(function(click) {
        // Block interaction if game hasn't started (OK button not clicked)
        if (!isGameActive) return;

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
