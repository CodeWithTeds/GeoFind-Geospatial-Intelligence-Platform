<?php

namespace App\Services;

use App\Models\Location;
use Throwable;
use Illuminate\Support\Facades\Log;

class SpatialAnalysisService
{
    protected $geometricService;

    public function __construct(GeometricService $geometricService)
    {
        $this->geometricService = $geometricService;
    }

    /**
     *       
     * @param array
     * @return array
     */
    public function calculateLocateDensity(array $bounds)
    {
        try {
            $locations = Location::whereBetween('latitude', [$bounds['south'], $bounds['north']])
                ->whereBetween('longitude', [$bounds['west'], $bounds['east']])
                ->get();

            $area = $this->calculateBoundsArea($bounds);
            $density = count($locations) / $area;

            return response()->json([
                'success' => true,
                'location' => count($locations),
                'density' => $density,
                'area_km2' => $area,
            ]);
        } catch (Throwable $e) {
            Log::error('Density calculation error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * 
     * @param array
     * @return float
     */
    private function calculateBoundsArea(array $bounds): float
    {
        $northWest = new Location([
            'latitude' => $bounds['north'],
            'longitude' => $bounds['west']
        ]);

        $northEast = new Location([
            'latitude' => $bounds['north'],
            'longitude' => $bounds['east']
        ]);

        $southEast = new Location([
            'latitude' => $bounds['south'],
            'longitude' => $bounds['east']
        ]);

        $southWest = new Location([
            'latitude' => $bounds['south'],
            'longitude' => $bounds['West']
        ]);

        $width = $this->geometricService->calculateDistance($northWest, $northEast)['distance']['kilometers'];
        $height = $this->geometricService->calculateDistance($northWest, $southEast)['distance']['kilometers'];


        return $width * $height;
    }
}
