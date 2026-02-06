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
let currentQuestionId = null;
let currentPinEntity = null;
let selectedLocation = null; // { lat: number, lng: number }

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
            currentQuestionId = question.id;
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
    const submitBtn = document.getElementById('submit-answer-btn');
    const submitContainer = document.getElementById('submit-container');

    confirmBtn.addEventListener('click', () => {
        // Enable game mode
        isGameActive = true;

        // Hide panel (slide down)
        panel.style.transform = 'translateY(120%)';

        // Optional: Reset camera or focus on start area if needed
    });

    submitBtn.addEventListener('click', async () => {
        if (!selectedLocation || !currentQuestionId) return;

        // Disable button to prevent double submit
        submitBtn.disabled = true;
        submitBtn.textContent = "TRANSMITTING...";

        try {
            const response = await fetch('/play/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.AppConfig.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    question_id: currentQuestionId,
                    latitude: selectedLocation.lat,
                    longitude: selectedLocation.lng
                })
            });

            const result = await response.json();

            if (response.ok) {
                showResult(result);
                // Hide submit button
                submitContainer.style.opacity = '0';
                submitContainer.style.transform = 'translate(-50%, 100px)';
            } else {
                alert('Transmission Failed: ' + (result.message || 'Unknown error'));
                submitBtn.disabled = false;
                submitBtn.textContent = "LOCK IN COORDINATES";
            }

        } catch (error) {
            console.error('Submission error:', error);
            alert('System Error: Unable to transmit coordinates.');
            submitBtn.disabled = false;
            submitBtn.textContent = "LOCK IN COORDINATES";
        }
    });
}

const TEASING_TEXTS = [
    "Did you even look at the map?",
    "My grandmother pins better than this.",
    "Recalculating... actually, never mind.",
    "GPS signal lost... along with your hope.",
    "Target missed. jerkshead is laughing at you.",
    "Are you playing with your eyes closed?",
    "Mission failed successfully.",
    "You call that a location? I call it a guess.",
    "Maybe try throwing a dart next time."
];

function typeText(element, text, speed = 50) {
    element.textContent = '';
    let i = 0;
    function type() {
        if (i < text.length) {
            element.textContent += text.charAt(i);
            i++;
            setTimeout(type, speed);
        }
    }
    type();
}

function showFailedModal() {
    const modal = document.getElementById('mission-failed-modal');
    const textElement = document.getElementById('failed-teasing-text');

    // Pick random text
    const text = TEASING_TEXTS[Math.floor(Math.random() * TEASING_TEXTS.length)];

    modal.classList.remove('hidden');
    // Allow display:flex to apply
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        // Start typing effect after modal fades in
        setTimeout(() => typeText(textElement, text), 300);
    }, 10);
}

function showResult(result) {
    // Check if failed (and not archived)
    if (!result.is_correct && !result.already_answered) {
        showFailedModal();
        return;
    }

    const modal = document.getElementById('result-modal');
    const starsContainer = document.getElementById('stars-container');
    const resultDistance = document.getElementById('result-distance');
    const resultStatus = document.getElementById('result-status');
    const viewMapBtn = document.getElementById('view-map-btn');

    // Show modal
    modal.classList.remove('hidden');
    // Small delay to allow display:block to apply before opacity transition
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        document.getElementById('result-content').classList.remove('scale-90');
    }, 10);

    // Update Content
    resultDistance.textContent = result.distance_formatted || (Math.round(result.distance_meters) + ' m');

    if (result.already_answered) {
        resultStatus.textContent = "MISSION ARCHIVED";
        resultStatus.className = "text-yellow-500 font-bold uppercase tracking-wider";

        // Remove any existing info message
        const existingInfo = document.getElementById('archive-info-msg');
        if (existingInfo) existingInfo.remove();

        const infoDiv = document.createElement('div');
        infoDiv.id = 'archive-info-msg';
        infoDiv.className = "mt-2 text-xs text-gray-400 font-mono text-center border-t border-white/10 pt-2";
        infoDiv.innerHTML = `<span class="text-yellow-500">⚠</span> RETRIEVED PREVIOUS RECORD<br>Target Secured. Re-submission disabled.`;

        resultStatus.parentNode.appendChild(infoDiv);
    } else if (result.is_correct) {
        resultStatus.textContent = "MISSION ACCOMPLISHED";
        resultStatus.className = "text-green-500 font-bold uppercase tracking-wider";
    } else {
        // Fallback for unexpected state
        resultStatus.textContent = "MISSION FAILED";
        resultStatus.className = "text-red-500 font-bold uppercase tracking-wider";
    }

    // Stars
    starsContainer.innerHTML = '';
    const stars = result.stars || 0;
    for (let i = 0; i < 3; i++) {
        const star = document.createElement('div');
        if (i < stars) {
            // Filled Star
            star.innerHTML = `<svg class="w-8 h-8 text-yellow-400 drop-shadow-[0_0_10px_rgba(250,204,21,0.8)]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>`;
        } else {
            // Empty Star
            star.innerHTML = `<svg class="w-8 h-8 text-gray-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>`;
        }
        starsContainer.appendChild(star);
    }

    // View Map Button Logic
    viewMapBtn.onclick = () => {
        modal.classList.add('opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 500);
        // Show correct answer location on map if needed?
        // Ideally we should show the correct pin vs user pin
        // For now, just lets them see the map
    };
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
        timeline: false,
        selectionIndicator: false,
        infoBox: false
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
    viewer.screenSpaceEventHandler.setInputAction(function (click) {
        // Block interaction if game hasn't started (OK button not clicked)
        if (!isGameActive) return;

        const cartesian = viewer.scene.pickPosition(click.position);
        if (cartesian) {
            const cartographic = Cesium.Cartographic.fromCartesian(cartesian);
            const longitude = Cesium.Math.toDegrees(cartographic.longitude);
            const latitude = Cesium.Math.toDegrees(cartographic.latitude);

            // Show fixed precision (6 decimals)
            const longitudeString = longitude.toFixed(6);
            const latitudeString = latitude.toFixed(6);

            console.log(`Pinned Location: Latitude: ${latitudeString} | Longitude: ${longitudeString}`);

            // Remove existing pin if any
            if (currentPinEntity) {
                viewer.entities.remove(currentPinEntity);
            }

            // Update state
            selectedLocation = { lat: latitude, lng: longitude };

            // Show Submit Button
            const submitContainer = document.getElementById('submit-container');
            submitContainer.style.opacity = '1';
            submitContainer.style.transform = 'translate(-50%, 0)';

            // Add a pin at the clicked location
            currentPinEntity = viewer.entities.add({
                position: cartesian,
                point: {
                    pixelSize: 10,
                    color: Cesium.Color.YELLOW,
                    outlineColor: Cesium.Color.BLACK,
                    outlineWidth: 2,
                    heightReference: Cesium.HeightReference.CLAMP_TO_GROUND
                },
                label: {
                    text: `Latitude: ${latitudeString} | Longitude: ${longitudeString}`,
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
