<?php

namespace App\Services;

use App\Models\Location;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class LocationClusteringService
{
    /**
     * 
     * @var \app\Services\GeometricService
     */
    protected $geometricService;

    public const ALGORITHMS = [
        'KMEANS' => 'kmeans', // These divide the dataset into a predefined number of clusters.
        'DBSCAN' => 'dbscan',  // (Density-Based Spatial Clustering of Applications with Noise)
        'GRID' => 'grid',
        'HIERARCHICAL' => 'hierarchical'

    ];

    public function __construct(GeometricService $geometricService)
    {
        $this->geometricService = $geometricService;
    }


    public function clusterLocations($locations, string $algorithm = 'DBSCAN', array $option = [])
    {
        try {
            if (is_array($locations)) {
                $locations = collect($locations);
            }
            $method = 'cluster' . strtolower($algorithm);
            $clusters = $this->$method($locations, $option);

            if (!method_exists($this, $method)) {
                throw new \InvalidArgumentException("Unsupported clustering algorithm: {$algorithm}");
            }
            return response()->json([
                'success' => true,
                'algorithm' => $algorithm,
                'clusters' => $clusters
            ]);
        } catch (Throwable $e) {
            Log::error('error', [
                'message' => $e->getMessage(),
                'algorithm' => $algorithm
                // 'trace' => $e->gert
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function clusterDbscan(Collection $locations, array $option = []): array
    {

        // $dbscan = new DBSCAN($epsilon = 0.5, $minSamples = 2);
        $minPoints = $option['min_points'] ?? 0.5;
        $epsilon = $option['epsilon'] ?? 2;

        $clusters = [];
        $visited = [];
        $noise = [];

        foreach ($locations as $index => $location) {
            if (isset($visted[$location->id])) {
                continue;
            }
            $visited[$location] = true;
            $neighbors = $this->getNeighbors($location, $locations, $epsilon);

            if (count($neighbors) < $minPoints) {
                $noise[] = $location;
                continue;
            }

            $clusters = [$location];

            foreach ($neighbors as $neighborIndex) {
                $neighbor = $locations['neighborIndex'];

                if (!isset($visited[$neighbor->id])) {
                    $visited[$neighbor] = true;
                }

            }
        }
    }

    private function getNeighbors(Location $locations, array $option = []): array 
    {
        
    }
}
