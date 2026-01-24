
import { initAdminMapPicker } from '../../admin-map-picker.js';

document.addEventListener('alpine:init', () => {
    Alpine.data('questionMap', (config) => ({
        init() {
            // Use Alpine's $el to get the container
            // Use Alpine's $wire to communicate with Livewire
            initAdminMapPicker({
                containerId: this.$el.id,
                accessToken: config.accessToken,
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
});
