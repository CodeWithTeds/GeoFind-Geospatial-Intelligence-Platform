/**
 * Geohash Map functionality
 * 
 * This file provides functions for visualizing geohash data on maps
 */

document.addEventListener('DOMContentLoaded', function() {
    // CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Geohash form handling
    const geohashForm = document.getElementById('geohashForm');
    if (geohashForm) {
        geohashForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            try {
                const formData = new FormData(e.target);
                const lat = parseFloat(formData.get('latitude'));
                const lng = parseFloat(formData.get('longitude'));
                
                // Validate coordinates
                if (isNaN(lat) || isNaN(lng)) {
                    throw new Error('Invalid coordinates');
                }
                
                if (lat < -90 || lat > 90) {
                    throw new Error('Latitude must be between -90 and 90');
                }
                
                if (lng < -180 || lng > 180) {
                    throw new Error('Longitude must be between -180 and 180');
                }
                
                const response = await fetch('/locations/to-geohash', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        latitude: lat,
                        longitude: lng
                    })
                });
                
                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                // Display the results
                handleGeohashResponse(data);
                
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('geohashResult').innerHTML = 
                    `<div class="alert alert-danger">Error: ${error.message}</div>`;
                
                // Hide map container on error
                const mapContainer = document.getElementById('geohashMapContainer');
                if (mapContainer) {
                    mapContainer.style.display = 'none';
                }
            }
        });
    }

    // Function to handle geohash response
    function handleGeohashResponse(data) {
        const resultDiv = document.getElementById('geohashResult');
        const mapContainer = document.getElementById('geohashMapContainer');
        
        if (!data.success) {
            resultDiv.innerHTML = `<div class="alert alert-danger">Error: ${data.error || 'Unknown error'}</div>`;
            mapContainer.style.display = 'none';
            return;
        }
        
        // Display geohash information
        resultDiv.innerHTML = `
            <div class="alert alert-success">
                <h5>Geohash Results</h5>
                <p><strong>Geohash:</strong> ${data.geohash}</p>
                <p><strong>Mnemonic:</strong> ${data.mnemonic || 'N/A'}</p>
                <p><strong>Precision:</strong> ${data.precision || 'N/A'}</p>
                <p><strong>Location:</strong> ${data.latitude.toFixed(6)}, ${data.longitude.toFixed(6)}</p>
            </div>
        `;
        
        // Display map if container exists
        if (mapContainer) {
            // Initialize map using the MapService
            try {
                mapService.displayGeohash(
                    'geohashMapContainer',
                    data.latitude,
                    data.longitude,
                    data.geohash,
                    data.bounding_box
                );
            } catch (error) {
                console.error('Error initializing geohash map:', error);
                mapContainer.innerHTML = '<div class="alert alert-danger">Error displaying map</div>';
            }
        }
    }
});


















