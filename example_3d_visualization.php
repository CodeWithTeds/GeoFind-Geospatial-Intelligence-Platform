<?php

/**
 * Example usage of the enhanced RadiusService with 3D visualization capabilities
 * 
 * This file demonstrates how to use the new 3D geofence visualization features
 * that create spheres, boxes, and polygons around hotels for Cesium.js
 */

require_once 'app/Services/Computation/RadiusService.php';
require_once 'app/Services/LocationValidationService.php';
require_once 'app/Services/GeometricService.php';

use App\Services\Computation\RadiusService;
use App\Services\LocationValidationService;
use App\Services\GeometricService;

// Initialize services
$validationService = new LocationValidationService();
$geometricService = new GeometricService();
$radiusService = new RadiusService($validationService, $geometricService);

// Example 1: Create 3D visualization for hotels in radius
echo "=== Example 1: 3D Hotel Geofence Visualization ===\n";

$pointId = 1; // Replace with actual location ID
$radius = 5.0; // 5 km radius

try {
    // Create visualization with all shape types
    $visualization = $radiusService->createHotelGeofenceVisualization($pointId, $radius, 'all');
    
    if ($visualization['success']) {
        echo "✅ Successfully created visualization for {$visualization['hotel_count']} hotels\n";
        echo "📍 Center: {$visualization['center']['latitude']}, {$visualization['center']['longitude']}\n";
        echo "🔵 Cesium entities: " . count($visualization['cesium_entities']) . "\n";
        echo "📐 Geofence shapes: " . count($visualization['geofence_shapes']) . "\n";
        
        // Generate JavaScript code for Cesium
        $javascript = $radiusService->generateCesiumJavaScript($visualization);
        echo "\n📜 Generated JavaScript code:\n";
        echo "```javascript\n";
        echo $javascript;
        echo "\n```\n";
    } else {
        echo "❌ Failed to create visualization: " . $visualization['message'] . "\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";

// Example 2: Create visualization with specific shape type
echo "=== Example 2: Sphere-only Visualization ===\n";

try {
    $sphereVisualization = $radiusService->createHotelGeofenceVisualization($pointId, $radius, 'sphere');
    
    if ($sphereVisualization['success']) {
        echo "✅ Created sphere visualization for {$sphereVisualization['hotel_count']} hotels\n";
        
        // Show first hotel's sphere data
        if (!empty($sphereVisualization['cesium_entities'])) {
            $firstSphere = $sphereVisualization['cesium_entities'][0];
            echo "🔵 First sphere entity:\n";
            echo "   Name: {$firstSphere['name']}\n";
            echo "   Position: {$firstSphere['position']['longitude']}, {$firstSphere['position']['latitude']}\n";
            echo "   Radius: {$firstSphere['ellipsoid']['radii']['x']}m\n";
            echo "   Color: {$firstSphere['ellipsoid']['material']['color']}\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";

// Example 3: Find hotels by coordinates with visualization
echo "=== Example 3: Hotels by Coordinates with 3D Visualization ===\n";

$latitude = 14.5995; // Manila coordinates
$longitude = 120.9842;
$radius = 2.0; // 2 km radius

try {
    $hotelsResult = $radiusService->findHotelsByCoordinates($latitude, $longitude, $radius);
    
    if (!empty($hotelsResult['hotels'])) {
        echo "✅ Found " . count($hotelsResult['hotels']) . " hotels\n";
        
        // Show visualization data for first hotel
        $firstHotel = $hotelsResult['hotels'][0];
        if (isset($firstHotel['visualization'])) {
            $viz = $firstHotel['visualization'];
            echo "🏨 First hotel: {$firstHotel['name']}\n";
            echo "   📍 Distance: {$firstHotel['distance']} km\n";
            echo "   🎨 Cesium entities: " . count($viz['cesium_entities']) . "\n";
            echo "   📐 Geofence shapes: " . count($viz['geofence_shapes']) . "\n";
            
            // Show sphere details
            if (!empty($viz['cesium_entities'])) {
                $sphere = $viz['cesium_entities'][0];
                echo "   🔵 Sphere radius: {$sphere['ellipsoid']['radii']['x']}m\n";
                echo "   🎨 Sphere color: {$sphere['ellipsoid']['material']['color']}\n";
            }
        }
    } else {
        echo "❌ No hotels found in the specified area\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 60) . "\n";

// Example 4: Different shape types
echo "=== Example 4: Different Shape Types ===\n";

$shapeTypes = ['sphere', 'box', 'polygon'];

foreach ($shapeTypes as $shapeType) {
    try {
        $shapeVisualization = $radiusService->createHotelGeofenceVisualization($pointId, $radius, $shapeType);
        
        if ($shapeVisualization['success']) {
            echo "✅ {$shapeType} visualization: " . count($shapeVisualization['cesium_entities']) . " entities\n";
        }
    } catch (Exception $e) {
        echo "❌ Error with {$shapeType}: " . $e->getMessage() . "\n";
    }
}

echo "\n" . str_repeat("=", 60) . "\n";

// Example 5: JavaScript integration example
echo "=== Example 5: JavaScript Integration Example ===\n";

echo "To use this in your Cesium.js application:\n\n";

echo "1. Include Cesium.js in your HTML:\n";
echo "```html\n";
echo "<script src=\"https://cesium.com/downloads/cesiumjs/releases/1.95/Build/Cesium/Cesium.js\"></script>\n";
echo "<link href=\"https://cesium.com/downloads/cesiumjs/releases/1.95/Build/Cesium/Widgets/widgets.css\" rel=\"stylesheet\">\n";
echo "```\n\n";

echo "2. Initialize Cesium viewer:\n";
echo "```javascript\n";
echo "const viewer = new Cesium.Viewer('cesiumContainer', {\n";
echo "    terrainProvider: Cesium.createWorldTerrain()\n";
echo "});\n";
echo "```\n\n";

echo "3. Call your PHP endpoint to get visualization data:\n";
echo "```javascript\n";
echo "fetch('/api/hotels/geofence-visualization?pointId=1&radius=5&shapeType=all')\n";
echo "    .then(response => response.json())\n";
echo "    .then(data => {\n";
echo "        if (data.success) {\n";
echo "            // Add entities to viewer\n";
echo "            data.cesium_entities.forEach(entity => {\n";
echo "                viewer.entities.add(entity);\n";
echo "            });\n";
echo "        }\n";
echo "    });\n";
echo "```\n\n";

echo "4. Or use the generated JavaScript directly:\n";
echo "```javascript\n";
echo "// Paste the generated JavaScript code here\n";
echo "```\n\n";

echo "🎉 That's it! Your hotels will now be visualized with 3D shapes in Cesium!\n";
