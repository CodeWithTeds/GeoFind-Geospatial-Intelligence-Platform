/**
 * Play Page Logic
 * Handles 3D map initialization and modal interactions.
 */
import './cesium-config';

import { ReviewMapService } from './services/ReviewMapService';

// Static Configuration / Data
const CONFIG = {
    hotels: [

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
        // Initialize Review Service
        window.reviewMapService = new ReviewMapService(viewer);

        fetchQuestion();
        setupUI(viewer);
    }
});

function updateText(id, text) {
    const el = document.getElementById(id);
    if (el) {
        el.textContent = text;
    } else {
        console.warn(`Element with id '${id}' not found.`);
    }
}

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
            updateText('question-title', question.title);
            updateText('question-description', question.description);
            updateText('question-difficulty', (question.difficulty || 'NORMAL').toUpperCase());
            updateText('question-tolerance', (question.tolerance_meters || 50) + ' METERS');
        } else {
            updateText('question-title', "NO MISSIONS AVAILABLE");
            updateText('question-description', "No active operations found in this sector. Stand by for future updates.");
        }
    } catch (error) {
        console.error('Error fetching question:', error);
        updateText('question-title', "CONNECTION FAILURE");
        updateText('question-description', "Unable to establish uplink with Mission Control. Check secure connection.");
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

                // Disable game active state to prevent further pinning
                isGameActive = false;
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

const COMPLIMENT_TEXTS = [
    "You found me... for now.",
    "Don't get used to this success.",
    "You got lucky. Very lucky.",
    "Acceptable performance. Barely.",
    "I suppose that counts as a win.",
    "Enjoy the victory while it lasts.",
    "Target secured. Don't celebrate yet.",
    "Not bad... but I'm watching you.",
    "You solved it? Must be a glitch.",
    "Fine. You win. This time."
];

const ARCHIVED_TEXTS = [
    "You already solved this. Move on.",
    "Still admiring your own work?",
    "This mission is long over. Why are you here?",
    "Déjà vu? You succeeded here ages ago.",
    "Memory check: You already did this."
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

function showSuccessModal(result) {
    const modal = document.getElementById('mission-success-modal');
    const textElement = document.getElementById('success-compliment-text');
    const starsContainer = document.getElementById('success-stars-container');
    const distanceElement = document.getElementById('success-distance');
    const mapBtn = document.getElementById('success-map-btn');

    // Pick text
    let text;
    if (result.already_answered) {
        text = ARCHIVED_TEXTS[Math.floor(Math.random() * ARCHIVED_TEXTS.length)];
    } else {
        text = COMPLIMENT_TEXTS[Math.floor(Math.random() * COMPLIMENT_TEXTS.length)];
    }

    // Update Distance
    distanceElement.textContent = result.distance_formatted || (Math.round(result.distance_meters) + ' m');

    // Update Stars
    starsContainer.innerHTML = '';
    const stars = result.stars || 0;
    for (let i = 0; i < 3; i++) {
        const star = document.createElement('div');
        if (i < stars) {
            // Filled Star (Yellow)
            star.innerHTML = `<svg class="w-8 h-8 md:w-10 md:h-10 text-yellow-400 drop-shadow-[0_0_10px_rgba(250,204,21,0.8)]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>`;
        } else {
            // Empty Star (Gray)
            star.innerHTML = `<svg class="w-8 h-8 md:w-10 md:h-10 text-gray-700" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>`;
        }
        starsContainer.appendChild(star);
    }

    // Show Modal
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        // Typing Effect
        setTimeout(() => typeText(textElement, text, 40), 300);
    }, 10);

    // Map Button Logic
    mapBtn.onclick = () => {
        modal.classList.add('opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 500);

        // Trigger Review
        if (window.reviewMapService && result.correct_location) {
            window.reviewMapService.showReview(selectedLocation, result.correct_location);
        }
    };
}

function showResult(result) {
    // Check if failed (and not archived)
    if (!result.is_correct && !result.already_answered) {
        showFailedModal();
        return;
    }

    showSuccessModal(result);
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
        // Use Aerial (Asset 2) as base layer
        imageryProvider: new Cesium.IonImageryProvider({ assetId: 2 }),
        baseLayerPicker: true,
        geocoder: true,
        animation: false,
        timeline: false,
        selectionIndicator: false,
        infoBox: false
    });

    // Add Labels Overlay (CartoDB Dark Matter Labels - Tinted Gold)
    const labelsLayer = viewer.imageryLayers.addImageryProvider(
        new Cesium.UrlTemplateImageryProvider({
            url: 'https://{s}.basemaps.cartocdn.com/dark_only_labels/{z}/{x}/{y}.png',
            subdomains: ['a', 'b', 'c', 'd'],
            credit: 'Map tiles by CartoDB, under CC BY 3.0. Data by OpenStreetMap, under ODbL.'
        })
    );

    labelsLayer.brightness = 2.0; // Boost brightness to turn gray pixels to white before tinting
    labelsLayer.contrast = 1.5;   // Increase contrast for sharper text

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

            // Remove existing pin if any
            if (currentPinEntity) {
                viewer.entities.remove(currentPinEntity);
            }

            // Update state
            selectedLocation = { lat: latitude, lng: longitude };

            // Show Submit Button
            const submitContainer = document.getElementById('submit-container');
            submitContainer.style.opacity = '1';
            submitContainer.style.transform = 'translateY(0)';

            // Add a pin at the clicked location
            currentPinEntity = viewer.entities.add({
                position: cartesian,
                point: {
                    pixelSize: 10,
                    color: Cesium.Color.GOLD,
                    outlineColor: Cesium.Color.BLACK,
                    outlineWidth: 2,
                    heightReference: Cesium.HeightReference.CLAMP_TO_GROUND
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
                color: Cesium.Color.GOLD,
                outlineColor: Cesium.Color.BLACK,
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
