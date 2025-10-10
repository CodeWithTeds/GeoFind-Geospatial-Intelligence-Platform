/**
 * Map Service - Handles map initialization and hotel POI rendering
 */
class MapService {
    constructor() {
        this.maps = {};
    }

    /**
     * Initialize or reinitialize a map
     * @param {string} containerId - The ID of the container element
     * @param {object} center - Center coordinates {latitude, longitude}
     * @param {number} zoom - Initial zoom level
     * @returns {object} Leaflet map instance
     */
    initializeMap(containerId, center, zoom = 13) {
        // Clean up existing map if it exists
        if (this.maps[containerId]) {
            this.maps[containerId].remove();
            delete this.maps[containerId];
        }
        
        const mapContainer = document.getElementById(containerId);
        if (!mapContainer) {
            console.error(`Map container ${containerId} not found`);
            return null;
        }
        
        // Ensure the container is visible before initializing
        mapContainer.style.display = 'block';
        
        try {
            // Create new map
            const map = L.map(containerId).setView(
                [center.latitude || 0, center.longitude || 0],
                zoom
            );
            
            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Store reference to the map
            this.maps[containerId] = map;
            
            return map;
        } catch (error) {
            console.error('Error initializing map:', error);
            return null;
        }
    }
    
    /**
     * Display hotels on a map
     * @param {string} containerId - The ID of the container element
     * @param {object} data - Hotel data from API
     * @returns {object} Map instance
     */
    displayHotels(containerId, data) {
        const mapContainer = document.getElementById(containerId);
        
        // If there's an error in the data, display it
        if (data.error) {
            mapContainer.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
            return null;
        }
        
        // Get center coordinates and radius
        const center = data.center || { latitude: 0, longitude: 0 };
        const radius = data.radius || 1;
        const hotels = data.hotels || [];
        
        // Initialize map
        const map = this.initializeMap(containerId, center);
        if (!map) return null;
        
        // Add center marker
        const centerMarker = L.marker([center.latitude, center.longitude], {
            icon: L.divIcon({
                className: 'center-marker',
                html: '<div style="background-color: #007bff; width: 12px; height: 12px; border-radius: 50%; border: 2px solid white;"></div>',
                iconSize: [12, 12],
                iconAnchor: [6, 6]
            })
        }).addTo(map);
        
        centerMarker.bindPopup('<strong>Center Point</strong>');
        
        // Add radius circle
        L.circle([center.latitude, center.longitude], {
            color: '#007bff',
            fillColor: '#007bff',
            fillOpacity: 0.1,
            radius: radius * 1000 // Convert to meters
        }).addTo(map);
        
        // Create a custom hotel icon
        const hotelIcon = L.divIcon({
            className: 'hotel-marker',
            html: '<div style="color: #FF5722; font-size: 20px;"><i class="fas fa-hotel"></i></div>',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });
        
        // Add hotel markers
        const hotelMarkers = [];
        
        hotels.forEach(hotel => {
            if (hotel.latitude && hotel.longitude) {
                const marker = L.marker([hotel.latitude, hotel.longitude], { 
                    icon: hotelIcon
                }).addTo(map);
                
                const name = hotel.name || 'Unnamed Hotel';
                const distance = typeof hotel.distance === 'number' ? 
                    `<br>Distance: ${hotel.distance.toFixed(2)} km` : '';
                
                marker.bindPopup(`<strong>${name}</strong>${distance}`);
                hotelMarkers.push(marker);
            }
        });
        
        // If we have hotels, fit the map to show all of them plus the center
        if (hotelMarkers.length > 0) {
            const group = L.featureGroup([...hotelMarkers, centerMarker]);
            map.fitBounds(group.getBounds().pad(0.1));
        } else {
            // If no hotels, show a reasonable view based on the radius
            const zoomLevel = 14 - Math.log2(radius);
            map.setZoom(Math.max(10, Math.min(15, zoomLevel)));
        }
        
        // Add a legend
        const legend = L.control({position: 'bottomright'});
        
        legend.onAdd = function(map) {
            const div = L.DomUtil.create('div', 'info legend');
            div.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
            div.style.padding = '8px';
            div.style.borderRadius = '4px';
            div.style.border = '1px solid #ccc';
            
            div.innerHTML = `
                <div><i class="fas fa-hotel" style="color: #FF5722;"></i> Hotels (${hotels.length})</div>
                <div style="margin-top: 5px;"><span style="display: inline-block; width: 12px; height: 12px; background-color: #007bff; border-radius: 50%; border: 2px solid white;"></span> Center Point</div>
                <div style="margin-top: 5px;"><span style="display: inline-block; width: 10px; height: 10px; border: 2px solid #007bff;"></span> ${radius} km Radius</div>
            `;
            
            return div;
        };
        
        legend.addTo(map);
        
        return map;
    }
    
    /**
     * Display a geohash on the map
     * @param {string} containerId - The ID of the container element
     * @param {number} latitude - Latitude coordinate
     * @param {number} longitude - Longitude coordinate
     * @param {string} geohash - Geohash string
     * @param {object} boundingBox - Geohash bounding box
     * @returns {object} Map instance
     */
    displayGeohash(containerId, latitude, longitude, geohash, boundingBox) {
        const center = { latitude, longitude };
        
        // Initialize map
        const map = this.initializeMap(containerId, center, 15);
        if (!map) return null;
        
        // Add marker at the point
        L.marker([latitude, longitude]).addTo(map)
            .bindPopup(`<strong>Location</strong><br>Geohash: ${geohash}`)
            .openPopup();
            
        // If we have bounding box info, draw the geohash rectangle
        if (boundingBox) {
            const bounds = [
                [boundingBox.sw.lat, boundingBox.sw.lng],  // Southwest corner
                [boundingBox.ne.lat, boundingBox.ne.lng]   // Northeast corner
            ];
            
            L.rectangle(bounds, {
                color: "#ff7800",
                weight: 1,
                fillOpacity: 0.2
            }).addTo(map);
            
            // Fit map to the geohash bounds
            map.fitBounds(bounds);
        }
        
        return map;
    }
    
    /**
     * Destroy a map instance
     * @param {string} containerId - The ID of the container element
     */
    destroyMap(containerId) {
        if (this.maps[containerId]) {
            this.maps[containerId].remove();
            delete this.maps[containerId];
        }
    }
}

// Create a singleton instance
const mapService = new MapService(); 