<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Http\Requests\LocationValidationRequest;
use App\Http\Requests\RadiusValidationRequest;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;


class LocationController extends Controller
{
    public function __construct(protected LocationService $locationService)
    { 
    }                        

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('locations.index', ['locations' => $this->locationService->getAllLocations()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('locations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LocationValidationRequest $request)
    {
        $this->locationService->createLocation($request->validated());
        return redirect()->route('admin.locations.index')->with('success', 'Location added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    /**
     * Calculate distance between two points
     */
    public function calculateDistance(Request $request): JsonResponse
    {
        try {
            return response()->json($this->locationService->calculateDistance(
                $request->input('point1_id'),
                $request->input('point2_id')
            ));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Find points within radius
     */
    public function findPointInRadius(RadiusValidationRequest $request): JsonResponse
    {
        try {
            return response()->json($this->locationService->findPointsInRadius(
                $request->input('point_id'),
                $request->input('radius')
            ));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Get address for location
     */
    public function getAddress(Location $location): JsonResponse
    {
        return response()->json($this->locationService->getAddress($location));
    }

    /**
     * Calculate midpoint between two points
     */
    public function calculateMidpoint(Request $request): JsonResponse
    {
        try {
            return response()->json($this->locationService->calculateMidpoint(
                $request->input('point1_id'),
                $request->input('point2_id')
            ));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LocationValidationRequest $request, Location $location)
    {
        $this->locationService->updateLocation($location, $request->validated());
        return redirect()->route('admin.locations.index')->with('success', 'Location updated successfully');
    }

    /**
     * Calculate triangle area
     */
    public function calculateTriangleArea(Request $request): JsonResponse
    {
        try {
            return response()->json($this->locationService->calculateTriangleArea(
                $request->input('point1_id'),
                $request->input('point2_id'),
                $request->input('point3_id')
            ));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Calculate bearing between two points
     */
    public function calculateBearing(Request $request): JsonResponse
    {
        try {
            return response()->json($this->locationService->calculateBearing(
                $request->input('point1_id'),
                $request->input('point2_id')
            ));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Calculate route between points
     */
    public function calculateRoute(Request $request): JsonResponse
    {
        try {
            return response()->json($this->locationService->calculateRoute(
                $request->input('start_point_id'),
                $request->input('end_point_id')
            ));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Convert coordinates to geohash
     */
    public function toGeohash(Request $request): JsonResponse
    {
        try {
            return response()->json($this->locationService->toGeohash(
                $request->input('latitude'),
                $request->input('longitude')
            ));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Calculate convex hull for a set of points
     */
    public function calculateConvexHull(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'point_ids' => 'required|array|min:3',
                'point_ids.*' => 'integer|exists:locations,id'
            ]);
            
            $result = $this->locationService->calculateConvexHull(
                $request->input('point_ids')
            );
            
            if (!$result['success']) {
                return response()->json(['error' => $result['error']], 422);
            }
            
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Controller error in calculateConvexHull', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate a grid within specified bounds
     */
    public function generateGrid(Request $request): JsonResponse
    {
        try {
            return response()->json($this->locationService->generateGrid(
                $request->input('bounds'),
                $request->input('grid_size', 10)
            ));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Generate a heatmap of location density
     */
    public function generateLocationHeatmap(Request $request): JsonResponse
    {
        try {
            return response()->json($this->locationService->generateLocationHeatmap(
                $request->input('bounds'),
                $request->input('grid_size', 10)
            ));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Find location clusters within specified bounds
     */
    public function findLocationClusters(Request $request): JsonResponse
    {
        try {
            return response()->json($this->locationService->findLocationClusters(
                $request->input('bounds'),
                $request->input('max_distance', 5)
            ));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Find hotels within radius
     */
    public function findHotelsInRadius(RadiusValidationRequest $request): JsonResponse
    {
        try {
            $radiusService = app(\App\Services\Computation\RadiusService::class);
            
            $result = $radiusService->findHotelsInRadius(
                $request->input('point_id'),
                $request->input('radius')
            );
            
            // Add success flag
            $result['success'] = true;
            
            // Add visualization data to the result
            if (isset($result['hotels']) && !empty($result['hotels'])) {
                $result['visualization'] = [
                    'success' => true,
                    'cesium_entities' => [],
                    'geofence_shapes' => []
                ];
                
                // Collect visualization data from hotels
                foreach ($result['hotels'] as $hotel) {
                    if (isset($hotel['visualization'])) {
                        $result['visualization']['cesium_entities'] = array_merge(
                            $result['visualization']['cesium_entities'],
                            $hotel['visualization']['cesium_entities']
                        );
                        $result['visualization']['geofence_shapes'] = array_merge(
                            $result['visualization']['geofence_shapes'],
                            $hotel['visualization']['geofence_shapes']
                        );
                    }
                }
            }
            
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error in findHotelsInRadius', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Find POIs within radius
     */
    public function findPOIsInRadius(RadiusValidationRequest $request): JsonResponse
    {
        try {
            // Get POI types or use default if not provided
            $poiTypes = $request->input('poi_types', ['tourism']);
            
            $radiusService = app(\App\Services\Computation\RadiusService::class);
            
            return response()->json($radiusService->findPOIsInRadius(
                $request->input('point_id'),
                $request->input('radius'),
                $poiTypes
            ));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Create 3D geofence visualization for hotels
     */
    public function createGeofenceVisualization(RadiusValidationRequest $request): JsonResponse
    {
        try {
            $radiusService = app(\App\Services\Computation\RadiusService::class);
            
            $shapeType = $request->input('shape_type', 'all');
            
            return response()->json($radiusService->createHotelGeofenceVisualization(
                $request->input('point_id'),
                $request->input('radius'),
                $shapeType
            ));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        $this->locationService->deleteLocation($location);
        return redirect()->route('admin.locations.index')->with('success', 'Location Deleted Successfully');
    }
}
