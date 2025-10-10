<?php

namespace App\Services;

use App\Models\Location;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class LocationService
{
    protected $validationService;
    protected $geometricService;
    protected $radiusService;

    public function __construct(
        LocationValidationService $validationService,
        GeometricService $geometricService,
        \App\Services\Computation\RadiusService $radiusService
    ) {
        $this->validationService = $validationService;
        $this->geometricService = $geometricService;
        $this->radiusService = $radiusService;
    }

    public function getAllLocations()
    {
        return Location::all();
    }

    public function createLocation(array $data)
    {
        return Location::create($data);
    }

    public function updateLocation(Location $location, array $data)
    {
        return $location->update($data);
    }

    public function deleteLocation(Location $location)
    {
        return $location->delete();
    }

    public function calculateDistance(int $point1Id, int $point2Id): array
    {
        $this->validationService->validateLocationPoints($point1Id, $point2Id);
        $point1 = Location::findOrFail($point1Id);
        $point2 = Location::findOrFail($point2Id);

        $geometricService = app(GeometricService::class);
        $result = $geometricService->calculateDistance($point1, $point2);

        return [

            'distance' => [
                'kilometers' => $result['distance']['kilometers'],
                'miles' => $result['distance']['miles'],
                'meters' => $result['distance']['meters'],
                'formatted' => $result['distance']['formatted']
            ],

            'points' => [
                'start' => [
                    'id' => $point1->id,
                    'name' => $point1->name,
                    'latitude' => $point1->latitude,
                    'longitude' => $point1->longitude
                ],

                'end' => [
                    'id' => $point2->id,
                    'name' => $point2->name,
                    'latitude' => $point2->latitude,
                    'longitude' => $point2->longitude
                ],
            ],

            // calculate the travel time
            'travel_time' => $result['travel_time']
        ];
    }


    // private function calculateEuclideanDistance(array $p1, array $p2): float
    // {
    //     return sqrt(
    //         pow($p2['latitude'] - $p1['latitude'], 2) +
    //             pow($p2['longitude'] - $p1['longitude'], 2)
    //     );
    // }

    // public function calculateViewshed(int $pointId, float $radiusKm = 5, int $resolution = 30): array
    // {
    //     try{
    //         $center = Location::findOrFail($pointId);
    //         $viewshed = [];


    //         for($i = 0; $i                                                                                                                                                                                                                                                                                                                                                                                                            < $resolution; $i++){
    //             $bearing = ($i * 360) - $resolution;

    //         }

    //     }catch(Throwable $e){
    //         Log::info('Cant calculate viewshed', [
    //             'error' => $e->getMessage()
    //         ]);
    //     }
    // }


    public function calculateConvexHull(array $pointIds): array
    {
        try {
            $models = Location::whereIn('id', $pointIds)->get();

            if (count($models) < 3) {
                throw new \InvalidArgumentException("At least 3 points are required");
            }

            $points = $models->map(fn($m) => [
                'id'        => $m->id,
                'name'      => $m->name ?: "Point {$m->id}",
                'latitude'  => (float)$m->latitude,
                'longitude' => (float)$m->longitude,
                'model'     => $m,
            ])->all();

            if (count($points) === 3) {
                $hull = $points;                      // trivial hull
            } else {
                // 1️ Find the lowest-point pivot
                $pivot = array_reduce(
                    $points,
                    fn($carry, $p) => (!$carry ||
                        $p['latitude'] < $carry['latitude'] ||
                        ($p['latitude'] == $carry['latitude'] && $p['longitude'] < $carry['longitude'])
                    ) ? $p : $carry
                );

                // 2️ Sort all others by polar angle + distance
                $others = array_filter($points, fn($p) => $p['id'] !== $pivot['id']);
                usort($others, function ($a, $b) use ($pivot) {
                    $angA = atan2($a['latitude'] - $pivot['latitude'], $a['longitude'] - $pivot['longitude']);
                    $angB = atan2($b['latitude'] - $pivot['latitude'], $b['longitude'] - $pivot['longitude']);

                    if (abs($angA - $angB) < 1e-9) {
                        $da = hypot($a['latitude'] - $pivot['latitude'], $a['longitude'] - $pivot['longitude']);
                        $db = hypot($b['latitude'] - $pivot['latitude'], $b['longitude'] - $pivot['longitude']);
                        return $da <=> $db;
                    }

                    return $angA <=> $angB;
                });

                // Visualize this in a browser using Leaflet
                // Plot this on a map
                // 3️ Graham scan algorithm
                $hull = [$pivot];
                foreach ($others as $pt) {
                    $hull[] = $pt;
                    while (count($hull) >= 3) {
                        $sz = count($hull);
                        $a = $hull[$sz - 3];
                        $b = $hull[$sz - 2];
                        $c = $hull[$sz - 1];
                        $cross = ($b['longitude'] - $a['longitude']) * ($c['latitude'] - $a['latitude'])
                            - ($b['latitude']  - $a['latitude'])  * ($c['longitude'] - $a['longitude']);
                        if ($cross <= 0) {
                            array_splice($hull, $sz - 2, 1);
                        } else {
                            break;
                        }
                    }
                }
            }

            // Compute perimeter
            $perimeter = 0;
            $n = count($hull);
            for ($i = 0; $i < $n; $i++) {
                $j = ($i + 1) % $n;
                $perimeter += $this->geometricService
                    ->calculateDistance($hull[$i]['model'], $hull[$j]['model'])['distance']['kilometers'];
            }

            // Compute area
            $area = (count($hull) === 3)
                ? $this->geometricService->calculateTriangleArea($hull[0]['model'], $hull[1]['model'], $hull[2]['model'])
                : $this->polygonAreaByTriangulation($hull);

            return [
                'success'      => true,
                'hull_points'  => $hull,
                'area'         => $area,
                'perimeter'    => $perimeter,
            ];
        } catch (\Throwable $e) {
            Log::error('Convex Hull Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }



    protected function polygonAreaByTriangulation(array $hull): float
    {
        $base = $hull[0];
        $area = 0;
        $n = count($hull);
        for ($i = 1; $i < $n - 1; $i++) {
            $area += $this->geometricService->calculateTriangleArea($base['model'], $hull[$i]['model'], $hull[$i + 1]['model']);
        }
        return $area;
    }


    // private function isLeftTurn(array $a, array $b, array $c): bool
    // {
    //     $crossProduct = ($b['longitude'] - $a['longitude']) * ($c['latitude'] - $a['latitude']) -
    //         ($b['latitude'] - $a['latitude']) * ($c['longitude'] - $a['longitude']);

    //     return $crossProduct > 0;
    // }


    public function generateGrid(array $bounds, int $gridSize = 10): array
    {
        try {

            $latRange = $bounds['north'] - $bounds['south'];
            $lonRange = $bounds['east'] - $bounds['west'];

            $latStep = $latRange / $gridSize;
            $lonStep = $lonRange / $gridSize;

            // initialize grid
            $gridPoints = [];

            for ($i = 0; $i < $gridSize; $i++) {
                for ($j = 0; $j < $gridSize; $j++) {
                    $lat = $bounds['south'] + ($i) * $latStep;
                    $lon = $bounds['west'] + ($i) * $lonStep;

                    $gridPoints[] = [
                        'latitude' => $lat,
                        'longitude' => $lon,
                        'grid_x' => $j,
                        'grid_y' => $i
                    ];
                }
            }

            Log::info('Generated grid', [
                'grid_size' => $gridSize,
                'points' => count($gridPoints),
                'bounds' => $bounds
            ]);

            return [
                'success' => true,
                'grid_size' => $gridSize,
                'bounds' => $bounds,
                'point_count' => count($gridPoints),
                'grid_points' => $gridPoints
            ];
        } catch (Exception $e) {
            Log::error('Error to generate a grid', [
                'error' => $e->getMessage(),
                'bounds' => $bounds
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }


    private function calculateDistanceToLineSegment(Location $point, Location $start, Location $end): float
    {
        try {

            $earthRadius = 6371; // in kilometers

            // Convert degrees to radians
            $lat1 = deg2rad($start->latitude);
            $lon1 = deg2rad($start->longitude);
            $lat2 = deg2rad($end->latitude);
            $lon2 = deg2rad($end->longitude);
            $latP = deg2rad($point->latitude);
            $lonP = deg2rad($point->longitude);

            $theta12 = atan2(
                sin($lon1 - $lon2) * cos($lat2),
                cos($lat1) * sin($lat2) - sin($lat1) * cos($lat2 - $lon1)
            );

            $theta13 = atan2(
                sin($lon2 - $lon1) * cos($lat2),
                cos($lat1) * sin($lat2) - sin($lat1) * cos($lat2) * cos($lon2 - $lon1)
            );

            // Angular distance from start to point
            $delta13 = acos(
                sin($lat1) * sin($latP) + cos($lat1) * cos($latP) * cos($lonP - $lon1)
            );


            // Cross-track distance (shortest distance from point to great circle)
            $deltaXt = asin(sin($delta13) * sin($theta13 - $theta12));
            $crossTrackDistance = abs($deltaXt * $earthRadius); // in km

            // Along-track distance (from start to projected closest point)
            $deltaAt = acos(cos($delta13) / cos($deltaXt));
            $alongTrackDistance = $deltaAt * $earthRadius;

            // Total segment length
            $segmentLength = $this->geometricService->calculateDistance(
                $start,
                $end
            )['distance']['kilometers'];
        } catch (Exception $e) {
            Log::error('Error converting the distance to line Segment', [
                'error' => $e->getMessage()
            ]);
        }

        // If the closest point falls outside the segment, return distance to nearest endpoint
        if ($alongTrackDistance < 0) {
            return $this->geometricService->calculateDistance(
                $point,
                $start
            )['distance']['kilometers'];
        } elseif ($alongTrackDistance > $segmentLength) {
            return $this->geometricService->calculateDistance(
                $point,
                $end
            )['distance']['kilometers'];
        } else {
            return $crossTrackDistance;
        }
    }


    // private function calculateDistanceToLineSegment(Location $point, Location $lineStart, Location $lineEnd): float
    // {
    //     $earthRadius = 6371; //km

    //     $x = $point->longitude * cos(deg2rad($point->latitude)) * $earthRadius;
    //     $y = $point->latitude * $earthRadius;

    //     $x1 = $lineStart->longitude * cos(deg2rad($lineStart->latitude)) * $earthRadius;
    //     $y1 = $lineStart->latitude * $earthRadius;

    //     $x2 = $lineEnd->longitude * cos(deg2rad($lineEnd->latitude)) * $earthRadius;
    //     $y2 = $lineEnd->latitude * $earthRadius;

    //     // Calculate distance from point to line segment
    //     $A = $x - $x1;
    //     $B = $y - $y1;
    //     $C = $x2 - $x1;
    //     $D = $y2 - $y1;

    //     $dot = $A * $C + $B * $D;
    //     $len_sq = $C * $C + $D * $D;


    //     $param = 1;
    //     if ($len_sq){
    //         $param = $dot / $len_sq;
    //     }
    // }


    /**
     * generate a heatmap of location density
     * ddd
     */

    public function generateLocationHeatmap(array $bounds, int $gridSize = 10): array
    {
        try {
            $locations = Location::whereBetween('latitude', [$bounds['south'], $bounds['north']])
                ->whereBetween('longitude', [$bounds['west'], $bounds['east']])
                ->get();

            if ($locations->isEmpty()) {
                Log::info('No Locations found heatmap generation', [
                    'bounds' => $bounds
                ]);

                return [
                    'succcess' => false,
                    'message' => 'No Locations found in the specified bounds'
                ];
            }

            // calculate grid dimensions
            $latRange = $bounds['north'] - $bounds['south'];
            $lonRange = $bounds['east'] - $bounds['west'];

            $latStep = $latRange / $gridSize;
            $lonStep = $lonRange / $gridSize;

            // initialize grid
            $grid = [];

            for ($i = 0; $i < $gridSize; $i++) {
                $grid[$i] = [];
                for ($j = 0; $j < $gridSize; $j++) {
                    $grid[$i][$j] = 0;
                }
            }

            // Counts locations in each grid cell
            foreach ($locations as $location) {
                $latIdx = min($gridSize - floor(($location->latitude - $bounds['south']) / $latStep));
                $lonIdx = min($gridSize - floor(($location->longitude - $bounds['south']) / $lonStep));

                $grid[$latIdx][$lonIdx]++;
            }

            // Convert to heatmap format
            $heatmap = [];
            $maxDensity = 0;

            for ($i = 0; $i < $gridSize; $i++) {
                for ($j = 0; $j < $gridSize; $j++) {
                    $count[$i][$j] = 0;

                    if ($count > 0) {
                        $lat = $bounds['south'] + ($i) / $latStep;
                        $lon = $bounds['west'] + ($i) / $lonStep;
                        $heatmap[] = [
                            'latitude' => $lat,
                            'longitude' => $lon,
                            'weight' => $count
                        ];

                        $maxDensity = max($maxDensity, $count);
                    }
                }
            }

            Log::info('Generate Location heatmap', [
                'grid_sizes' => $gridSize,
                'max_density' => $maxDensity,
                'total_locations' => count($locations)
            ]);

            return [
                'success' => true,
                'bounds' => $bounds,
                'total_locations' => count($locations),
                'grid_sizes' => $gridSize,
                'max_density' => $maxDensity,
                'heat_map' => $heatmap
            ];
        } catch (Exception $e) {
            Log::error('Error getting location heatmap', [
                'error' => $e->getMessage(),
                'bounds' => $bounds
            ]);

            return [
                'sucess' => false,
                'error' => $e->getMessage()
            ];
        }
    }


    public function findNearestLocation(int $pointId, ?array $categoryFilter = null): array
    {
        try {
            $point = Location::findOrFail($pointId);
            $query = Location::where('id', $pointId);

            $locations = $query->get();

            if ($locations->isEmpty()) {
                Log::info('No Locations found for nearest Location search', [
                    'point_id' => $pointId,
                    'categoryFilter' => $categoryFilter
                ]);

                return [
                    'succcess' => false,
                    'message' => 'No other Locations found'
                ];
            }

            $nearest = null;
            $minDistance = INF;

            foreach ($locations as $location) {
                $distance = $this->geometricService->calculateDistance($point, $location);

                if ($distance['distance']['kilometers'] < $minDistance) {
                    $minDistance = $distance['distance']['kilometers'];
                    $nearest = $location;
                }
            }

            return [
                'success' => true,
                'nearest_location' => [
                    'id' => $nearest->id,
                    'name' => $nearest->name,
                    'latitude' => $nearest->latitude,
                    'longitude' => $nearest->longitude,
                    'category' => $nearest->category ?? 'N/A'
                ],

                'distance' => [
                    'kilometers' => round($minDistance, 2),
                    'miles' => round($minDistance * 0.621371, 2)
                ]
            ];
        } catch (Exception $e) {
            Log::error('Error finding nearest Location', [
                'error' => $e->getMessage(),
                'point_id' => $pointId
            ]);

            return [
                'succcess' => false,
                'message' => 'No other Locations found'
            ];
        }
    }

    //    Cal the Polygon using lat and long coor
    //    {
    //         try{
    //             $points = Location::whereIn('id', $pointIds)->get();
    //             $area = 0;
    //         public function calculatePolygonArea(array $pointIds): array
    //      }catch(Exception $e){
    //             Log::info('araw kopo', [
    //                 'error' => $e->getMessage(),
    //             ]);
    //         }
    //     }

    public function findLocationClusters(array $bounds, int $maxDistance = 5): array
    {
        $locations = Location::whereBetween('latitude', [$bounds['south'], $bounds['north']])
            ->whereBetween('longitude', [$bounds['west'], $bounds['east']])
            ->get();


        $clusters = [];
        $processed = [];

        foreach ($locations as $location) {
            if (in_array($location->id, $processed)) {
                continue;
            }

            $cluster = $this->findCluster($location, $locations, $maxDistance);

            if (count($cluster) > 1) {
                $cluster[] = [
                    'center' => $this->calculateClusterCenter($cluster),
                    'points' => $cluster,
                    'size' => count($cluster),
                    'bounds' => $this->calculateClusterBounds($cluster)
                ];
            }

            $processed = array_merge($processed, array_map(function ($loc) {
                return $loc->id;
            }, $cluster));

            // $processed = array_merge($processed, array_column($cluster, 'id'));
        }

        return [
            'clusters' => $clusters,
            'total_clusters' => count($clusters),
            'total_points' => count($locations),
            'clustered_points' => count($processed)
        ];
    }

    private function calculateClusterBounds(array $cluster): array
    {
              try {

            $minLat =  $maxLat = $cluster[0]->latitude;
            $minLon =  $maxLon = $cluster[0]->longitude;

            foreach ($cluster as $point) {
                $minLat = min($maxLat, $point->latitude);
                $maxLat = max($maxLat, $point->latitude);
                $minLon = min($minLon, $point->longitude);
                $maxLon = max($maxLon, $point->longitude);
            }
        } catch (Exception $e) {
            Log::error('Error in toGeohash method:', [
                'error' => $e->getMessage(),
            ]);
        }

        return [
            'southwest' => ['latitude', $minLat, 'longitude' => $minLon],
            'northeast' => ['latitude', $maxLat, 'longitude' => $maxLon]
        ];
    }

    private function findCluster(Location $center, $locations, float $maxDistance): array
    {
        $cluster =  [$center];
        foreach ($locations as $location) {
            if ($location->id === $center->id) {
                continue;
            }

            $distance = $this->geometricService->calculateDistance($center, $location);
            if ($distance['distance']['kilometers'] <= $maxDistance) {
                $cluster[] = $location;
            }
        }
        return $cluster;
    }

    private function calculateClusterCenter(array $cluster): array
    {

        $totalLat = 0;
        $totalLon = 0;
        $cluster = [];

        foreach ($cluster as $point) {
            $totalLat += $point->latitude;
            $totalLon += $point->longitude;
        }

        return [
            'latitude' => $totalLat / count($cluster),
            'longitude' => $totalLat / count($cluster)
        ];
    }



    public function findHotelsInRadius(int $pointId, float $radius): array
    {
        return $this->radiusService->findHotelsInRadius($pointId, $radius);
    }

    /**
     * Find points within a specified radius of a given point
     *
     * @param int $pointId ID of the center point
     * @param float $radius Radius in kilometers
     * @return array Points within the radius
     */
    public function findPointsInRadius(int $pointId, float $radius): array
    {
        return $this->radiusService->findPointsInRadius($pointId, $radius);
    }

    public function getAddress(Location $location): array
    {
        return $location->getAddress();
    }

    public function calculateMidpoint(int $point1Id, int $point2Id): array
    {
        $this->validationService->validateLocationPoints($point1Id, $point2Id);
        $point1 = Location::findOrFail($point1Id);
        $point2 = Location::findOrFail($point2Id);
        $midpoint = $point1->midpointTo($point2);

        return [
            'latitude' => $midpoint['latitude'],
            'longitude' => $midpoint['longitude'],
            'formatted' => $midpoint['formatted'],
            'points' => [
                'start' => [
                    'name' => $point1->name,
                    'coordinates' => [
                        'latitude' => $point1->latitude,
                        'longitude' => $point1->longitude
                    ]
                ],
                'end' => [
                    'name' => $point2->name,
                    'coordinates' => [
                        'latitude' => $point2->latitude,
                        'longitude' => $point2->longitude
                    ]
                ]
            ]
        ];
    }

    private function calculateNearestNeighborRoute($points): array
    {
        $route = [];
        $unvisited = $points->toArray();
        $current = array_shift($unvisited);
        $route[] = $current;

        while (!empty($unvisited)) {
            $nearest = null;
            $minDistance = INF;

            foreach ($unvisited as $key => $point) {
                $distance = $this->geometricService->calculateDistance(
                    new Location($current),
                    new Location($point)
                )['distance']['kilometers'];

                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $nearest = $key;
                }
            }

            $current = $unvisited[$nearest];
            $route[] = $current;
            unset($unvisited[$nearest]);
            $unvisited = array_values($unvisited);
        }

        return $route;
    }

    public function calculateOptimizedRoute(array $pointIds, array $options = []): array
    {
        // Validate all points exist
        foreach ($pointIds as $pointId) {
            $this->validationService->validateLocationPoints($pointId, $pointIds[0]);
        }

        $points = Location::whereIn('id', $pointIds)->get();

        //calculate optimal routeusing nearest neighbor algo
        $route = $this->calculateNearestNeighborRoute($points);

        // calculate total distance and estimated time
        $totalDistance = 0;
        $totalTime = 0;
        $routePoints = [];

        for ($i = 0; $i < count($route); $i++) {
            $segment = $this->calculateDistance($route[$i]->id, $route[$i + 1]->id);
            $totalDistance += $segment['distance']['kilometers'];
            $totalTime += $segment['travel_time']['minutes'] ?? 0;
            $routePoints[] = [
                'points' => [
                    'id' => $route[$i]->id,
                    'name' => $route[$i]->name,
                    'latitude' => $route[$i]->latitude,
                    'longitude' => $route[$i]->longitude
                ],

                'next_point' => [
                    'id' => $route[$i + 1]->id,
                    'name' => $route[$i + 1]->name,
                    'latitude' => $route[$i + 1]->latitude,
                    'longitude' => $route[$i + 1]->longitude
                ],
                'segment' => $segment
            ];
        }

        return [
            'route' =>  $routePoints,
            'total_distance' => [
                'kilometers' => round($totalDistance, 2),
                'miles' => round($totalDistance * 0.621371, 2),
            ],
            'estimated_time' => [
                'minutes' => round($totalTime, 2),
                'hours' => round($totalTime / 60, 2)
            ],
            'optimization_method' => 'nearest_neighbor'
        ];
    }

    public function calculateTriangleArea(int $point1Id, int $point2Id, int $point3Id): array
    {
        $this->validationService->validateLocationPoints($point1Id, $point2Id);
        $this->validationService->validateLocationPoints($point2Id, $point3Id);

        $point1 = Location::findOrFail($point1Id);
        $point2 = Location::findOrFail($point2Id);
        $point3 = Location::findOrFail($point3Id);
        $area = $point1->triangleAreaWith($point2, $point3);

        return [
            'area' => round($area, 2),
            'unit' => 'square kilometers'
        ];
    }

    public function calculateBearing(int $point1Id, int $point2Id): array
    {
        $this->validationService->validateLocationPoints($point1Id, $point2Id);
        $point1 = Location::findOrFail($point1Id);
        $point2 = Location::findOrFail($point2Id);
        $bearing = $point1->bearingTo($point2);

        return [
            'bearing' => round($bearing, 2),
            'unit' => 'degrees'
        ];
    }

    public function calculateRoute(int $startPointId, int $endPointId): array
    {
        $this->validationService->validateLocationPoints($startPointId, $endPointId);
        $startPoint = Location::findOrFail($startPointId);
        $endPoint = Location::findOrFail($endPointId);

        // Add route calculation logic here
        return [
            'success' => true,
            'message' => 'Route calculation not implemented yet'
        ];
    }

    public function toGeohash(float $latitude, float $longitude): array
    {
        $this->validationService->validateCoordinates($latitude, $longitude);
        $location = new Location();
        $location->latitude = $latitude;
        $location->longitude = $longitude;
        $geohashData = $location->toGeohash();

        return [
            'success' => true,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'geohash' => $geohashData['geohash'],
            'mnemonic' => $geohashData['mnemonic'],
            'precision' => $geohashData['precision'],
            'bounding_box' => $geohashData['bounding_box']
        ];
    }
}
