
import { initAdminMapPicker } from '../../admin-map-picker.js';

const registerMapComponent = () => {
    // Avoid re-registering if already exists to prevent warnings/errors
    // Note: Alpine doesn't expose a clean way to check if a data component exists, 
    // but re-registering usually just overwrites or warns. 
    // However, we want to ensure it's registered at least once.
    
    Alpine.data('questionMap', (config) => ({
        init() {
            // Use Alpine's $el to get the container
            // Use Alpine's $wire to communicate with Livewire
            initAdminMapPicker({
                containerId: this.$el.id,
                initialLat: config.initialLat,
                initialLng: config.initialLng,
                tilesetId: config.tilesetId,
                onLocationPicked: (lat, lng) => {
                    this.$wire.set('answer_latitude', lat);
                    this.$wire.set('answer_longitude', lng);
                }
            });
        }
    }));
};

// Register if Alpine is already initialized
if (window.Alpine) {
    registerMapComponent();
}

// Register on init event (fallback for when script loads before Alpine)
document.addEventListener('alpine:init', registerMapComponent);
