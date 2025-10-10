document.addEventListener('DOMContentLoaded', function () {
    // CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Bearing calculation
    document.getElementById('bearingForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            const formData = new FormData(e.target);
            const response = await fetch('/locations/calculate-bearing', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    point1_id: parseInt(formData.get('point1_id')),
                    point2_id: parseInt(formData.get('point2_id'))
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            document.getElementById('bearingResult').innerHTML =
                `<div class="alert alert-info">Bearing: ${data.bearing} ${data.unit}</div>`;
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('bearingResult').innerHTML =
                `<div class="alert alert-danger">Error calculating bearing: ${error.message}</div>`;
        }
    });

    // Distance calculation
    const calculateDistanceForm = document.getElementById('calculateDistanceForm');
    if (calculateDistanceForm) {
        calculateDistanceForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            try {
                const formData = new FormData(e.target);
                const point1Id = parseInt(formData.get('point1_id'));
                const point2Id = parseInt(formData.get('point2_id'));

                // Validate the form data
                if (!point1Id || !point2Id) {
                    throw new Error('Please select both points');
                }

                if (point1Id === point2Id) {
                    throw new Error('Please select different points');
                }

                const response = await fetch('/calculate-distance', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        point1_id: point1Id,
                        point2_id: point2Id
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                document.getElementById('distanceResult').innerHTML =
                    `<div class="alert alert-info">
                        <h5>Distance information</h5>
                        <p><strong>Distance: ${data.distance.formatted} </strong></p>
                        <p><strong>kilometers: ${data.distance.kilometers} kilometers</strong></p>
                        <p><strong>meters: ${data.distance.meters} meters</strong></p>
                        <p><strong>Miles: ${data.distance.miles} miles</strong></p>

                        <h6 class="mt-3">Travel time: </h6>
                        <ul>
                            ${Object.entries(data.travel_time).map(([mode, time]) =>
                        `<li><strong>${mode}:</strong> ${time}</li>`
                    ).join('')}
                        </ul>
                    </div>`;
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('distanceResult').innerHTML =
                    `<div class="alert alert-danger">Error calculating distance: ${error.message}</div>`;
            }
        });
    }

    // Midpoint calculation
    document.getElementById('midpointForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            const formData = new FormData(e.target);
            const response = await fetch('/calculate-midpoint', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    point1_id: formData.get('point1_id'),
                    point2_id: formData.get('point2_id')
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            document.getElementById('midpointResult').innerHTML =
                `<div class="alert alert-info">
                    <h5>Midpoint Location</h5>
                    <p><strong>Latitude:</strong> ${data.formatted.latitude}</p>
                    <p><strong>Longitude:</strong> ${data.formatted.longitude}</p>
                    <hr>
                    
                    <h6 class ="text-muted">
                        Between ${data.points.start.name} and ${data.points.end.name}
                    </h6>
                </div>`;
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('midpointResult').innerHTML =
                `<div class="alert alert-danger">Error calculating midpoint: ${error.message}</div>`;
        }
    });

    // Triangle area calculation 
    document.getElementById('triangleAreaForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            const formData = new FormData(e.target);

            const response = await fetch('/calculate-triangle-area', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(formData))
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('triangle calculation response:', data)
            document.getElementById('triangleAreaResult').innerHTML =
                `<div class="alert alert-info">
                    <h5>Triangle Analysingle Analysis</h5>
                    <div class="mt-3">
                        <h6>Area information</h6>
                        <p><strong>Area:</strong> ${data.area.toLocaleString()} ${data.unit}</p>
                    </div>
                </div>`;
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('triangleAreaResult').innerHTML =
                `<div class="alert alert-danger">Error calculating triangle Area: ${error.message}</div>`;
        }
    });

    // Find hotels in radius
    document.getElementById('radiusForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const formData = new FormData(e.target);
        const pointId = formData.get('point_id');
        const radius = formData.get('radius');
        const radiusResultDiv = document.getElementById('radiusResult');
        const mapContainer = document.getElementById('hotelMapContainer');

        try {
            radiusResultDiv.innerHTML = '<div class="alert alert-info">Searching for hotels...</div>';
            mapContainer.style.display = 'none';

            // Fetch hotels data
            const hotelsResponse = await fetch('/find-hotels-in-radius', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    point_id: pointId,
                    radius: radius
                })
            });

            if (!hotelsResponse.ok) {
                const errorData = await hotelsResponse.json();
                throw new Error(errorData.error || `HTTP error! status: ${hotelsResponse.status}`);
            }

            const hotelsData = await hotelsResponse.json();

            // Clear previous results
            radiusResultDiv.innerHTML = '';
            
            // Display debug information if available
            if (hotelsData.debug && hotelsData.debug.turbo_url) {
                const debugDiv = document.createElement('div');
                debugDiv.className = 'alert alert-warning mt-3';
                debugDiv.innerHTML = `
                    <h5 class="alert-heading">API Debug Info</h5>
                    <p>If you're seeing 0 results, the API may not have data for this area.</p>
                    <p><strong>Click this link to run the exact same query in a new window and see the raw results for yourself:</strong></p>
                    <a href="${hotelsData.debug.turbo_url}" target="_blank" class="btn btn-primary">Test API Query Directly</a>
                    <hr>
                    <p class="mb-0">Raw API Response:</p>
                    <pre style="white-space: pre-wrap; word-wrap: break-word; max-height: 200px; overflow-y: auto;">${JSON.stringify(hotelsData.debug.api_response, null, 2)}</pre>
                `;
                radiusResultDiv.appendChild(debugDiv);
            }

            // Add hotel count to results
            const hotelCount = hotelsData.count || 0;
            const hotelCountElement = document.createElement('div');
            hotelCountElement.className = 'alert alert-success mt-3';
            hotelCountElement.innerHTML = `<strong>Hotels found:</strong> ${hotelCount}`;
            radiusResultDiv.appendChild(hotelCountElement);

            if (hotelCount > 0) {
                mapContainer.style.display = 'block';
                initializeCesiumMap('hotelMapContainer', hotelsData);

                // Create a clickable list of hotels
                const hotelList = document.createElement('ul');
                hotelList.className = 'list-group mt-3';
                hotelList.style.maxHeight = '250px'; // Set max height for scrolling
                hotelList.style.overflowY = 'auto'; // Enable vertical scrolling

                hotelsData.hotels.forEach(hotel => {
                    const listItem = document.createElement('li');
                    listItem.className = 'list-group-item list-group-item-action';
                    listItem.innerHTML = `
                        <strong>${hotel.name}</strong><br>
                        <small>Distance: ${hotel.distance.toFixed(2)} km</small><br>
                        <small>Coords: ${hotel.latitude.toFixed(6)}, ${hotel.longitude.toFixed(6)}</small>
                    `;
                    listItem.style.cursor = 'pointer';
                    listItem.addEventListener('click', () => {
                        const hotelEntity = cesiumViewer.entities.values.find(entity => entity.name === hotel.name);
                        if (hotelEntity) {
                            handleHotelSelection(hotelEntity, hotelsData.center);
                            flyToLocation(hotel.longitude, hotel.latitude);
                        }
                    });
                    hotelList.appendChild(listItem);
                });
                radiusResultDiv.appendChild(hotelList);
            }

        } catch (error) {
            console.error('Error fetching hotels:', error);
            radiusResultDiv.innerHTML =
                `<div class="alert alert-danger">Error loading hotel data: ${error.message}</div>`;
            mapContainer.style.display = 'none';
        }
    });

    // Utility to debounce a function
    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // Reverse geocoding
    document.getElementById('reverseGeocodingForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();

        const locationId = document.getElementById('location_id').value;
        const resultDiv = document.getElementById('geocodingResult');
        let abortController = new AbortController();

        // Debounced function to handle fetch
        const fetchAddress = debounce(async () => {
            try {
                resultDiv.innerHTML = '<div class="alert alert-info">Loading address information...</div>';

                const response = await fetch(`/locations/${locationId}/address`, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    signal: abortController.signal
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                }

                let html = '';

                if (data.error) {
                    html = `
                        <div class="alert alert-warning p-3 rounded">
                            <h5>Error Loading Address</h5>
                            <p>${data.message || 'An error occurred while fetching the address.'}</p>
                            <p><small>Location: (${data.latitude}, ${data.longitude})</small></p>
                        </div>`;
                } else {
                    // Build address components
                    const addressItems = Object.entries(data.address_components)
                        .filter(([_, value]) => value)
                        .map(([key, value]) => `<li><strong>${key.replace('_', ' ')}</strong>: ${value}</li>`)
                        .join('');

                    html = `
                    <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Address Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <p class="mb-2"><strong>Full Address:</strong> ${data.full_address}</p>
                            <button class="btn btn-sm btn-outline-primary copy-address" data-address="${data.full_address}">
                                <i class="fas fa-copy"></i> Copy Address
                            </button>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Postal Code:</strong> ${data.address_components?.postal_code || data.address_components?.postcode || 'N/A'}</p>
                                <p><strong>Country Code:</strong> ${data.address_components?.country_code || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Elevation:</strong> ${data.elevation?.elevation ? `${data.elevation.elevation} ${data.elevation.unit} above sea level` : 'N/A'}</p>
                                <p><strong>Timezone:</strong> ${data.timezone?.name || 'N/A'}</p>
                            </div>
                        </div>

                        <p>
                            <a href="${data.google_maps_link}" target="_blank" class="btn btn-primary btn-sm">
                                <i class="fas fa-map-marker-alt"></i> View on Google Maps
                            </a>
                        </p>

                        <hr>

                        <h6 class="mt-3 mb-2">Address Components:</h6>
                        <ul class="list-unstyled">
                            ${addressItems}
                        </ul>

                        ${data.nearby_places ? `
                        <hr>
                        <h6 class="mt-3 mb-2">Nearby Places:</h6>
                        <ul class="list-unstyled">
                            ${data.nearby_places.map(place => `
                                <li><i class="fas fa-map-pin"></i> ${place.name} (${place.distance}km)</li>
                            `).join('')}
                        </ul>
                        ` : ''}
                    </div>
                </div>`;

                    // Add weather information if available
                    if (data.weather) {
                        const { current, daily } = data.weather;
                        console.log('Weather time data:', {
                            currentTime: current.time,
                            currentTimeType: typeof current.time,
                            currentTimeString: String(current.time)
                        });
                        html += `
                        <hr>
                        <h6>Current Weather:</h6>
                        <ul class="list-unstyled">
                            <li><strong>Temperature:</strong> ${current.temperature ?? 'N/A'} °C</li>
                            <li><strong>Windspeed:</strong> ${current.windspeed ?? 'N/A'} km/h</li>
                            <li><strong>Wind Direction:</strong> ${current.winddirection ?? 'N/A'}°</li>
                            <li><strong>Weather Code:</strong> ${current.weathercode ?? 'N/A'}</li>
                            <li><strong>Time:</strong> ${current.time?.formatted ?? 'N/A'}</li>
                            <li><strong>Daytime:</strong> ${current.is_day === 1 ? 'Yes' : 'No'}</li>
                        </ul>
                        <h6>Daily Weather:</h6>
                        <ul class="list-unstyled">
                            <li><strong>Max Temperature:</strong> ${daily.max_temp ?? 'N/A'} °C</li>
                            <li><strong>Min Temperature:</strong> ${daily.min_temp ?? 'N/A'} °C</li>
                        </ul>`;
                    }
                }

                resultDiv.innerHTML = html;
            } catch (error) {
                if (error.name === 'AbortError') {
                    console.log('Fetch aborted');
                    return;
                }
                console.error('Error:', error);
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <h5>Error Loading Address</h5>
                        <p>${error.message}</p>
                        <p><small>Please try again later or contact support if the problem persists.</small></p>
                    </div>`;
            }
        }, 300);

        // Execute the debounced fetch
        fetchAddress();

        // Cleanup: abort previous fetch if a new one is triggered
        return () => abortController.abort();
    });

    // Add copy address functionality
    document.addEventListener('click', function (e) {
        if (e.target.closest('.copy-address')) {
            const button = e.target.closest('.copy-address');
            const address = button.dataset.address;

            navigator.clipboard.writeText(address).then(() => {
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i> Copied!';
                setTimeout(() => {
                    button.innerHTML = originalText;
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy address:', err);
            });
        }
    });

    document.getElementById('geohashForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        // Get the button and add loading state
        const convertBtn = document.getElementById('convertBtn');
        const originalBtnText = convertBtn.innerHTML;
        convertBtn.disabled = true;
        convertBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Converting...
        `;

        const latitude = parseFloat(formData.get('latitude'));
        const longitude = parseFloat(formData.get('longitude'));

        if (isNaN(latitude) || isNaN(longitude)) {
            document.getElementById('geohashResult').innerHTML = `
            <div class="alert alert-danger">Please enter valid numeric coordinates</div>`;
            // Reset button state
            convertBtn.disabled = false;
            convertBtn.innerHTML = originalBtnText;
            return;
        }

        fetch('/locations/to-geohash', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                latitude: formData.get('latitude'),
                longitude: formData.get('longitude'),
            }),
        })
            .then((response) => {
                if (!response.ok) {
                    return response.json().then((err) => Promise.reject(err));
                }
                return response.json();
            })
            .then((data) => {
                if (data.success) {
                    const resultHtml = `
                <div class="alert alert-success">
                    <h4>Geohash Conversion Successful</h4>
                    <div class="result-content">
                    <p><strong>Geohash:</strong> ${data.geohash}</p>
                    <p><strong>Mnemonic:</strong> ${data.mnemonic}</p>
                    <p><strong>Coordinates:</strong></p>
                    <ul>
                        <li>Latitude: ${data.latitude}</li>
                        <li>Longitude: ${data.longitude}</li>
                    </ul>
                    </div>
                </div>`;

                    document.getElementById('geohashResult').innerHTML = resultHtml;
                    document.getElementById('showMapBtn').style.display = 'inline-block';
                    window.lastGeohashData = data;
                } else {
                    throw new Error(data.message || 'Failed to convert Coordinates');
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                document.getElementById('geohashResult').innerHTML = `
            <div class="alert alert-danger">
                Error converting coordinates to geohash
            </div>`;
            })
            .finally(() => {
                // Reset button state
                convertBtn.disabled = false;
                convertBtn.innerHTML = originalBtnText;
            });
    });

    document.getElementById('routeAnalysisForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const resultDiv = document.getElementById('routeAnalysisResult');
        resultDiv.innerHTML = '<div class="alert alert-info">Analyzing route...</div>';

        try {
            const formData = new FormData(e.target);
            const response = await fetch('/calculate-route', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    start_point_id: formData.get('start_point_id'),
                    end_point_id: formData.get('end_point_id')
                })
            });

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Failed to analyze route');
            }

            resultDiv.innerHTML = `
                <div class="alert alert-success">
                    <h6 class="mb-2">Route Analysis</h6>
                    <div class="mb-2">
                        <strong>From:</strong> ${data.data.start.name}<br>
                        <strong>To:</strong> ${data.data.end.name}<br>
                        <strong>Distance:</strong> ${data.data.distance} km
                    </div>
                    <div class="mt-3">
                        ${data.data.analysis}
                    </div>
                </div>
            `;
        } catch (error) {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    Error: ${error.message}
                </div>
            `;
        }
    });

    // Initialize map variable globally
    let routeMap = null;
    let routeLayer = null;

    document.getElementById('routeAnalysisForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const resultDiv = document.getElementById('routeAnalysisResult');
        resultDiv.innerHTML = '<div class="alert alert-info">Analyzing route...</div>';

        try {
            const formData = new FormData(e.target);
            const response = await fetch('/calculate-route', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    start_point_id: formData.get('start_point_id'),
                    end_point_id: formData.get('end_point_id')
                })
            });

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Failed to analyze route');
            }

            // Display the analysis
            resultDiv.innerHTML = `
            <div class="alert alert-success">
                <h6 class="mb-2">Route Analysis</h6>
                <div class="mb-2">
                    <strong>From:</strong> ${data.data.start.name}<br>
                    <strong>To:</strong> ${data.data.end.name}<br>
                    <strong>Distance:</strong> ${data.data.distance} km
                </div>
                <div class="mt-3">
                    ${data.data.analysis}
                </div>
            </div>
        `;

            // Initialize or update the map
            if (!routeMap) {
                routeMap = L.map('routeMap').setView([
                    (data.data.start.coordinates.latitude + data.data.end.coordinates.latitude) / 2,
                    (data.data.start.coordinates.longitude + data.data.end.coordinates.longitude) / 2
                ], 10);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(routeMap);
            } else {
                routeMap.setView([
                    (data.data.start.coordinates.latitude + data.data.end.coordinates.latitude) / 2,
                    (data.data.start.coordinates.longitude + data.data.end.coordinates.longitude) / 2
                ], 10);
            }

            // Clear previous route if exists
            if (routeLayer) {
                routeMap.removeLayer(routeLayer);
            }

            // Create markers for start and end points
            const startMarker = L.marker([
                data.data.start.coordinates.latitude,
                data.data.start.coordinates.longitude
            ]).addTo(routeMap).bindPopup(`Start: ${data.data.start.name}`);

            const endMarker = L.marker([
                data.data.end.coordinates.latitude,
                data.data.end.coordinates.longitude
            ]).addTo(routeMap).bindPopup(`End: ${data.data.end.name}`);

            // Draw a line between the points
            routeLayer = L.polyline([
                [data.data.start.coordinates.latitude, data.data.start.coordinates.longitude],
                [data.data.end.coordinates.latitude, data.data.end.coordinates.longitude]
            ], {
                color: 'blue',
                weight: 3,
                opacity: 0.7
            }).addTo(routeMap);

            // Fit the map to show both markers
            routeMap.fitBounds(routeLayer.getBounds(), {
                padding: [50, 50]
            });

        } catch (error) {
            console.error('Error:', error);
            resultDiv.innerHTML = `
            <div class="alert alert-danger">
                Error: ${error.message}
            </div>
        `;
        }
    });

    // Coordinates to Geohash conversion
    document.getElementById('coordinatesToGeohashForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            const formData = new FormData(e.target);
            const latitude = parseFloat(formData.get('latitude'));
            const longitude = parseFloat(formData.get('longitude'));

            console.log('sending data', { latitude, longitude });
            const response = await fetch('/locations/to-geohash', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    latitude: latitude,
                    longitude: longitude
                })
            });
            if (!response.ok) {
                const errorData = await response.json().catch(() => null); // Prevent crashing on invalid JSON
                const message = errorData?.message || `HTTP error! status: ${response.status}`;
                throw new Error(message);
            }

            const data = await response.json();
            const resultDiv = document.getElementById('coordinatesToGeohashResult');
            if (resultDiv) {
                resultDiv.innerHTML = `<div class="alert alert-info">
                Geohash: ${data.geohash}
            </div>`;
            }
        } catch (error) {
            console.error('Error:', error);
            const resultDiv = document.getElementById('coordinatesToGeohashResult');
            if (resultDiv) {
                resultDiv.innerHTML = `<div class="alert alert-danger">Error converting coordinates: ${error.message}</div>`;
            }
        }
    });

    // Initialize map variable globally
    let distanceMap = null;
    let distanceLayer = null;

    function calculateGeohashBounds(geohash) {
        $base32 = '0123456789bcdefghjkmnpqrstuvwxyz';
        let latMin = -90.0, latMax = 90.0;
        let lonMin = -180.0, lonMax = 180.0;
        let bit = 0;
        let ch = 0;
        let even = true;

        for (let i = 0; i < geohash.length; i++) {
            const cd = base32.indexOf(geohash[i]);
            for (let j = 0; j < 5; j++) {
                const mask = 1 << (4 + j);

                if (even) {
                    const lonMid = (lonMin + lonMax) / 2;
                    if ((cd & mask) !== 0) {
                        lonMin = lonMid;
                    } else {
                        lonMax = lonMid;
                    }
                } else {
                    const latMid = (latMin + latMax) / 2;
                    if ((cd % mask)) {
                        latMin = latMid;

                    } else {
                        latMax = latMid;
                    }

                }
                even = !even;
            }
        }

        return [[latMin, lonMin], [latMax, lonMax]];
    }

    document.getElementById('chatbotForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const resultDiv = document.getElementById('chatbotResult');
        resultDiv.innerHTML = '<div class="alert alert-info">Generating route...</div>';

        try {
            const formData = new FormData(e.target);
            const response = await fetch('/chatbot/generate-route', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'text/event-stream'
                },
                body: JSON.stringify({
                    start_location: formData.get('start_location'),
                    end_location: formData.get('end_location')
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let buffer = '';

            while (true) {
                const { value, done } = await reader.read();
                if (done) break;

                buffer += decoder.decode(value, { stream: true });
                const lines = buffer.split('\n');
                buffer = lines.pop();

                for (const line of lines) {
                    if (line.startsWith('data: ')) {
                        try {
                            const data = JSON.parse(line.slice(6));
                            if (data.text) {
                                updateChatbotResponse(data.text);
                            }
                        } catch (e) {
                            console.error('Error parsing SSE data:', e);
                        }
                    }
                }
            }

        } catch (error) {
            console.error('Error:', error);
            resultDiv.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
        }
    });

    let currentResponse = '';

    function updateChatbotResponse(text) {
        const resultDiv = document.getElementById('chatbotResult');
        currentResponse += text;

        // Format the response
        const formattedText = formatStreamingResponse(currentResponse);

        resultDiv.innerHTML = `
            <div class="alert alert-success">
                <div class="route-response">
                    ${formattedText}
                </div>
            </div>
        `;
    }

    function formatStreamingResponse(text) {
        return text
            .split('\n')
            .map(line => {
                if (line.startsWith('#')) {
                    return `<h4 class="mt-3">${line.replace('#', '')}</h4>`;
                }
                if (line.startsWith('-')) {
                    return `<li>${line.replace('-', '')}</li>`;
                }
                return `<p>${line}</p>`;
            })
            .join('');
    }

    // Convex Hull calculation
    document.getElementById('convexHullForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            const formData = new FormData(e.target);
            const pointIds = Array.from(formData.getAll('point_ids[]')).map(id => parseInt(id));
            
            if (pointIds.length < 3) {
                throw new Error('Please select at least 3 points');
            }
            
            const response = await fetch('/calculate-convex-hull', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    point_ids: pointIds
                })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            let resultHtml = `
                <div class="alert alert-info">
                    <h5>Convex Hull Results</h5>
                    <p><strong>Points in Hull:</strong> ${data.hull_points.length}</p>
                    <p><strong>Area:</strong> ${data.area.toFixed(2)} square km</p>
                    <p><strong>Perimeter:</strong> ${data.perimeter.toFixed(2)} km</p>
                </div>
            `;
            
            if (data.hull_points && data.hull_points.length > 0) {
                resultHtml += `
                    <div class="mt-3">
                        <h6>Hull Points:</h6>
                        <ul>
                            ${data.hull_points.map(point => 
                                `<li>${point.name}: (${point.latitude.toFixed(6)}, ${point.longitude.toFixed(6)})</li>`
                            ).join('')}
                        </ul>
                    </div>
                    <div id="convexHullMap" class="map-container mt-3" style="height: 400px;"></div>
                `;
            }
            
            document.getElementById('convexHullResult').innerHTML = resultHtml;
            
            // Initialize map if we have hull points
            if (data.hull_points && data.hull_points.length > 0) {
                initializeConvexHullMap(data.hull_points);
            }
            
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('convexHullResult').innerHTML =
                `<div class="alert alert-danger">Error calculating convex hull: ${error.message}</div>`;
        }
    });

    // Function to initialize convex hull map
    function initializeConvexHullMap(hullPoints) {
        // Create a map centered on the first point
        const map = L.map('convexHullMap');
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Create markers for each hull point
        const markers = [];
        const polygonPoints = [];
        
        hullPoints.forEach(point => {
            // Create marker
            const marker = L.marker([point.latitude, point.longitude])
                .bindPopup(`<strong>${point.name}</strong><br>Lat: ${point.latitude.toFixed(6)}, Lng: ${point.longitude.toFixed(6)}`)
                .addTo(map);
            
            markers.push(marker);
            polygonPoints.push([point.latitude, point.longitude]);
        });
        
        // Create a polygon for the hull
        const polygon = L.polygon(polygonPoints, {
            color: 'red',
            fillColor: '#f03',
            fillOpacity: 0.2,
            weight: 3
        }).addTo(map);
        
        // Create a bounds object and fit the map to it
        const bounds = L.latLngBounds(polygonPoints);
        map.fitBounds(bounds.pad(0.1)); // Add 10% padding around the bounds
        
        // Add line connecting last point to first point to complete the hull
        if (polygonPoints.length > 2) {
            L.polyline([polygonPoints[polygonPoints.length - 1], polygonPoints[0]], {
                color: 'red',
                weight: 3,
                opacity: 0.7,
                dashArray: '5, 10'
            }).addTo(map);
        }
    }

    // Grid Generator
    document.getElementById('gridGeneratorForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            const formData = new FormData(e.target);
            
            // Validate bounds
            const north = parseFloat(formData.get('north'));
            const south = parseFloat(formData.get('south'));
            const east = parseFloat(formData.get('east'));
            const west = parseFloat(formData.get('west'));
            const gridSize = parseInt(formData.get('grid_size'));
            
            if (north <= south) {
                throw new Error('North boundary must be greater than South boundary');
            }
            
            if (east <= west) {
                throw new Error('East boundary must be greater than West boundary');
            }
            
            const response = await fetch('/generate-grid', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    bounds: {
                        north: north,
                        south: south,
                        east: east,
                        west: west
                    },
                    grid_size: gridSize
                })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            let resultHtml = `
                <div class="alert alert-info">
                    <h5>Grid Generation Results</h5>
                    <p><strong>Grid Size:</strong> ${data.grid_size} x ${data.grid_size}</p>
                    <p><strong>Total Points:</strong> ${data.point_count}</p>
                    <p><strong>Bounds:</strong> (${data.bounds.south.toFixed(6)}, ${data.bounds.west.toFixed(6)}) to 
                                               (${data.bounds.north.toFixed(6)}, ${data.bounds.east.toFixed(6)})</p>
                </div>
            `;
            
            document.getElementById('gridGeneratorResult').innerHTML = resultHtml;
            
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('gridGeneratorResult').innerHTML =
                `<div class="alert alert-danger">Error generating grid: ${error.message}</div>`;
        }
    });

    // Heatmap Generator
    document.getElementById('heatmapForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            const formData = new FormData(e.target);
            
            // Validate bounds
            const north = parseFloat(formData.get('north'));
            const south = parseFloat(formData.get('south'));
            const east = parseFloat(formData.get('east'));
            const west = parseFloat(formData.get('west'));
            const gridSize = parseInt(formData.get('grid_size'));
            
            if (north <= south) {
                throw new Error('North boundary must be greater than South boundary');
            }
            
            if (east <= west) {
                throw new Error('East boundary must be greater than West boundary');
            }
            
            const response = await fetch('/generate-heatmap', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    bounds: {
                        north: north,
                        south: south,
                        east: east,
                        west: west
                    },
                    grid_size: gridSize
                })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            let resultHtml = `
                <div class="alert alert-info">
                    <h5>Location Heatmap Results</h5>
                    <p><strong>Total Locations:</strong> ${data.total_locations}</p>
                    <p><strong>Grid Size:</strong> ${data.grid_sizes} x ${data.grid_sizes}</p>
                    <p><strong>Max Density:</strong> ${data.max_density} locations per cell</p>
                </div>
            `;
            
            document.getElementById('heatmapResult').innerHTML = resultHtml;
            
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('heatmapResult').innerHTML =
                `<div class="alert alert-danger">Error generating heatmap: ${error.message}</div>`;
        }
    });

    // Cluster Finder
    document.getElementById('clusterForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            const formData = new FormData(e.target);
            
            // Validate bounds
            const north = parseFloat(formData.get('north'));
            const south = parseFloat(formData.get('south'));
            const east = parseFloat(formData.get('east'));
            const west = parseFloat(formData.get('west'));
            const maxDistance = parseFloat(formData.get('max_distance'));
            
            if (north <= south) {
                throw new Error('North boundary must be greater than South boundary');
            }
            
            if (east <= west) {
                throw new Error('East boundary must be greater than West boundary');
            }
            
            const response = await fetch('/find-location-clusters', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    bounds: {
                        north: north,
                        south: south,
                        east: east,
                        west: west
                    },
                    max_distance: maxDistance
                })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            let resultHtml = `
                <div class="alert alert-info">
                    <h5>Location Clusters Results</h5>
                    <p><strong>Total Clusters:</strong> ${data.total_clusters}</p>
                    <p><strong>Total Points:</strong> ${data.total_points}</p>
                    <p><strong>Clustered Points:</strong> ${data.clustered_points}</p>
                </div>
            `;
            
            if (data.clusters && data.clusters.length > 0) {
                resultHtml += `
                    <div class="mt-3">
                        <h6>Clusters:</h6>
                        <ul>
                            ${data.clusters.map((cluster, index) => 
                                `<li>Cluster ${index + 1}: ${cluster.size} points, centered at (${cluster.center.latitude.toFixed(6)}, ${cluster.center.longitude.toFixed(6)})</li>`
                            ).join('')}
                        </ul>
                    </div>
                `;
            }
            
            document.getElementById('clusterResult').innerHTML = resultHtml;
            
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('clusterResult').innerHTML =
                `<div class="alert alert-danger">Error finding clusters: ${error.message}</div>`;
        }
    });
});

function initializeForm(formId, endpoint, responseHandler) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const button = form.querySelector('button[type="submit"]');
        const spinner = button.querySelector('.spinner-border');
        const buttonText = button.querySelector('.btn-text');

        try {
            // Show loading state
            button.disabled = true;
            spinner.classList.remove('d-none');
            buttonText.textContent = 'Calculating...';

            const formData = new FormData(form);
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(Object.fromEntries(formData))
            });

            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'An error occurred');
            }

            responseHandler(data, form);
        } catch (error) {
            showError(form, error.message);
        } finally {
            // Reset button state
            button.disabled = false;
            spinner.classList.add('d-none');
            buttonText.textContent = button.dataset.originalText || 'Calculate';
        }
    });

    // Store original button text
    const button = form.querySelector('button[type="submit"]');
    const buttonText = button.querySelector('.btn-text');
    button.dataset.originalText = buttonText.textContent;
}

function showError(form, message) {
    const resultDiv = form.nextElementSibling;
    resultDiv.innerHTML = `
        <div class="alert alert-danger">
            ${message}
        </div>
    `;
}

function showSuccess(form, message) {
    const resultDiv = form.nextElementSibling;
    resultDiv.innerHTML = `
        <div class="alert alert-success">
            ${message}
        </div>
    `;
}

// Response Handlers
function handleDistanceResponse(data, form) {
    const resultDiv = document.getElementById('distanceResult');
    resultDiv.innerHTML = `
        <div class="alert alert-success">
            Distance: ${data.distance.toFixed(2)} km
        </div>
    `;

    // Initialize map if it exists
    const mapDiv = document.getElementById('distanceMap');
    if (mapDiv && data.point1 && data.point2) {
        initializeDistanceMap(mapDiv, data.point1, data.point2);
    }
}

function handleMidpointResponse(data, form) {
    showSuccess(form, `
        Midpoint: (${data.latitude.toFixed(6)}, ${data.longitude.toFixed(6)})
    `);
}

function handleTriangleAreaResponse(data, form) {
    showSuccess(form, `
        Triangle Area: ${data.area.toFixed(2)} square kilometers
    `);
}

function handleRadiusResponse(data, form) {
    const resultDiv = document.getElementById('radiusResult');
    if (data.points.length === 0) {
        resultDiv.innerHTML = `
            <div class="alert alert-info">
                No points found within the specified radius.
            </div>
        `;
        return;
    }

    let html = `
        <div class="alert alert-success">
            <h5>Points within radius:</h5>
            <ul class="list-group">
    `;

    data.points.forEach(point => {
        html += `
            <li class="list-group-item">
                ${point.name} (${point.latitude.toFixed(6)}, ${point.longitude.toFixed(6)})
                <br>
                Distance: ${point.distance.toFixed(2)} km
            </li>
        `;
    });

    html += `
            </ul>
        </div>
    `;

    resultDiv.innerHTML = html;
}

function handleBearingResponse(data, form) {
    showSuccess(form, `
        Bearing: ${data.bearing.toFixed(2)}°
    `);
}

function handleGeohashResponse(data, form) {
    const resultDiv = document.getElementById('geohashResult');
    resultDiv.innerHTML = `
        <div class="alert alert-success">
            Geohash: ${data.geohash}
        </div>
    `;

    // Show map button if coordinates are available
    const showMapBtn = document.getElementById('showMapBtn');
    if (showMapBtn) {
        showMapBtn.style.display = 'inline-block';
        showMapBtn.onclick = () => {
            initializeGeohashMap(data.latitude, data.longitude);
        };
    }
}

function handleGeocodingResponse(data, form) {
    showSuccess(form, `
        Address: ${data.address}
    `);
}

// Map Functions
function initializeDistanceMap(container, point1, point2) {
    const map = L.map(container).setView([0, 0], 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    // Add markers
    const marker1 = L.marker([point1.latitude, point1.longitude])
        .addTo(map)
        .bindPopup(point1.name);

    const marker2 = L.marker([point2.latitude, point2.longitude])
        .addTo(map)
        .bindPopup(point2.name);

    // Draw line between points
    L.polyline([
        [point1.latitude, point1.longitude],
        [point2.latitude, point2.longitude]
    ]).addTo(map);

    // Fit bounds to show both points
    map.fitBounds([
        [point1.latitude, point1.longitude],
        [point2.latitude, point2.longitude]
    ]);
}

function initializeGeohashMap(latitude, longitude) {
    const mapDiv = document.createElement('div');
    mapDiv.className = 'map-container mt-3';
    document.getElementById('geohashResult').appendChild(mapDiv);

    const map = L.map(mapDiv).setView([latitude, longitude], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    L.marker([latitude, longitude])
        .addTo(map)
        .bindPopup(`Location: ${latitude}, ${longitude}`)
        .openPopup();
}



  