/**
 * Service to handle map review visualization.
 * Shows correct location, user location, and the path between them.
 */
export class ReviewMapService {
    constructor(viewer) {
        this.viewer = viewer;
        this.entities = [];
    }

    /**
     * Visualizes the result on the map.
     * @param {Object} userLocation { lat: number, lng: number }
     * @param {Object} correctLocation { latitude: number, longitude: number }
     */
    showReview(userLocation, correctLocation) {
        this.clear();

        if (!userLocation || !correctLocation) {
            console.warn("Missing location data for review");
            return;
        }

        // 1. Add Correct Location Pin (Image)
        const correctPin = this.viewer.entities.add({
            position: Cesium.Cartesian3.fromDegrees(correctLocation.longitude, correctLocation.latitude),
            billboard: {
                image: '/images/image.png', // Using existing image
                width: 40,
                height: 40,
                verticalOrigin: Cesium.VerticalOrigin.BOTTOM,
                heightReference: Cesium.HeightReference.CLAMP_TO_GROUND,
                disableDepthTestDistance: Number.POSITIVE_INFINITY
            },
            label: {
                text: 'TARGET LOCATION',
                font: 'bold 14px "Chakra Petch", monospace',
                style: Cesium.LabelStyle.FILL_AND_OUTLINE,
                fillColor: Cesium.Color.fromCssColorString('#22c55e'),
                outlineColor: Cesium.Color.BLACK,
                outlineWidth: 4,
                verticalOrigin: Cesium.VerticalOrigin.BOTTOM,
                pixelOffset: new Cesium.Cartesian2(0, -45), // Adjusted for image height
                heightReference: Cesium.HeightReference.CLAMP_TO_GROUND,
                disableDepthTestDistance: Number.POSITIVE_INFINITY,
                distanceDisplayCondition: new Cesium.DistanceDisplayCondition(0, 5000000)
            }
        });
        this.entities.push(correctPin);

        // 2. Add Line Connecting Pins
        const start = Cesium.Cartesian3.fromDegrees(userLocation.lng, userLocation.lat);
        const end = Cesium.Cartesian3.fromDegrees(correctLocation.longitude, correctLocation.latitude);

        const line = this.viewer.entities.add({
            polyline: {
                positions: [start, end],
                width: 4,
                material: new Cesium.PolylineDashMaterialProperty({
                    color: Cesium.Color.YELLOW,
                    dashLength: 20
                }),
                clampToGround: true
            }
        });
        this.entities.push(line);

        // 3. Zoom to fit both points (with relaxed range to avoid super zoom)
        const boundingSphere = Cesium.BoundingSphere.fromPoints([start, end]);
        
        // Ensure minimum radius for very close points
        const radius = Math.max(boundingSphere.radius, 500); // Minimum 500m radius

        this.viewer.camera.flyToBoundingSphere(new Cesium.BoundingSphere(boundingSphere.center, radius), {
            offset: new Cesium.HeadingPitchRange(0, -Cesium.Math.PI_OVER_FOUR, radius * 4.0), // Increased multiplier for further distance
            duration: 2
        });
    }

    /**
     * Clears review entities from the map.
     */
    clear() {
        this.entities.forEach(entity => {
            this.viewer.entities.remove(entity);
        });
        this.entities = [];
    }
}
