<?php

namespace App\Services\Computation;

use App\Models\Location;
use App\Services\LocationValidationService;
use App\Services\GeometricService;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\RadiusValidationRequest;
use Illuminate\Support\Facades\Http;


use Throwable;

class RadiusService
{
    protected $validationService;
    protected $geometricService;

    /**
     * Constructor
     *
     * @param LocationValidationService $validationService
     * @param GeometricService $geometricService
     */
    public function __construct(
        LocationValidationService $validationService,
        GeometricService $geometricService
    ) {
        $this->validationService = $validationService;
        $this->geometricService = $geometricService;
    }




    public function findPointsInRadius(int $pointId, float $radius): array
    {
        $this->validationService->validateRadius($radius);
        $point = Location::findOrFail($pointId);
        return $point->pointWithinRadius($radius);
    }


    public function findPOIsInRadius(int $pointId, float $radius, array $poiTypes = ['tourism']): array
    {
        try {
            $this->validationService->validateRadius($radius);
            $point = Location::findOrFail($pointId);

            $lat = $point->latitude;
            $lon = $point->longitude;

            $boundingBox = $this->calculateBoundingBoxFromRadius($lat, $lon, $radius);

            // Overpass API is more efficient if we query for all types at once.
            // We'll create a mapping of the poi types to search for any value.
            $tags = array_fill_keys($poiTypes, '*');

            $query = $this->buildOverpassQuery($boundingBox, $tags);
            $turboUrl = "https://overpass-turbo.eu/?Q=" . urlencode($query);
            $poiData = $this->fetchFromOverpass($query);

            if (!isset($poiData['elements'])) {
                return [
                    'pois' => [],
                    'center' => ['latitude' => $lat, 'longitude' => $lon],
                    'radius' => $radius,
                    'debug' => [
                        'api_response' => $poiData,
                        'query' => $query,
                        'turbo_url' => $turboUrl
                    ]
                ];
            }

            $pois = $this->processElementsData($poiData['elements'], $point, $radius);

            return [
                'pois' => $pois,
                'center' => [
                    'latitude' => $lat,
                    'longitude' => $lon,
                ],
                'radius' => $radius,
                'count' => count($pois),
                'debug' => [
                    'api_response' => $poiData,
                    'query' => $query,
                    'processed_count' => count($pois),
                    'turbo_url' => $turboUrl,
                ],
            ];
        } catch (Throwable $e) {
            Log::error('Error finding POIs in radius', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return [
                'error' => $e->getMessage(),
                'success' => false,
            ];
        }
    }

    public function createHotelGeofenceVisualization(int $pointId, float $radius, string $shapeType = 'all'): array
    {
        // Reuse the findHotelsInRadius logic to get the data
        $data = $this->findHotelsInRadius($pointId, $radius);
        
        // Add visualization specific data
        $data['visualization'] = [
            'shape_type' => $shapeType,
            'enabled' => true
        ];
        
        return $data;
    }

    public function createGeofence(int $pointId, int $centerPoint, float $radius): array
    {
        // Placeholder for future implementation
        return []; 
    }

    protected function processElementsData(array $elements, Location $centerPoint, float $radius, string $defaultName = 'Unnamed POI'): array
    {
        $items = [];

        foreach ($elements as $element) {
            $item = [];
            $tags = $element['tags'] ?? [];

            if ($element['type'] === 'node') {
                $item = [
                    'id' => $element['id'],
                    'type' => 'node',
                    'latitude' => $element['lat'],
                    'longitude' => $element['lon'],
                    'tags' => $tags,
                ];
            } elseif (isset($element['center'])) {
                $item = [
                    'id' => $element['id'],
                    'type' => $element['type'], // way or relation
                    'latitude' => $element['center']['lat'],
                    'longitude' => $element['center']['lon'],
                    'tags' => $tags,
                ];
            }

            if (!empty($item) && isset($item['latitude'], $item['longitude'])) {
                $itemLocation = new Location([
                    'latitude' => $item['latitude'],
                    'longitude' => $item['longitude'],
                ]);

                $distanceResult = $this->geometricService->calculateDistance($centerPoint, $itemLocation);
                $item['distance'] = $distanceResult['distance']['kilometers'];
                $item['name'] = $tags['name'] ?? $defaultName;

                // Filter by radius
                if ($item['distance'] <= $radius) {
                    $items[] = $item;
                }
            }
        }

        usort($items, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        return $items;
    }


    /**
     * Find hotels within a specified radius using Overpass API
     * @param int $pointId ID of the center point
     * @param float $radius Radius in kilometers
     * @return array Hotel data within the radius
     */
    public function findHotelsInRadius(int $pointId, float $radius): array
    {
        try {
            $this->validationService->validateRadius($radius);
            $point = Location::findOrFail($pointId);

            $lat = $point->latitude;
            $lon = $point->longitude;

            $boundingBox = $this->calculateBoundingBoxFromRadius($lat, $lon, $radius);
            $query = $this->buildOverpassQuery($boundingBox, ['tourism' => 'hotel']);

            $turboUrl = "https://overpass-turbo.eu/?Q=" . urlencode($query);

            $hotelsData = $this->fetchFromOverpass($query);

            if (!isset($hotelsData['elements'])) {
                return [
                    'hotels' => [],
                    'center' => ['latitude' => $lat, 'longitude' => $lon],
                    'radius' => $radius,
                    'count' => 0,
                    'debug' => [
                        'api_response' => $hotelsData,
                        'query' => $query,
                        'turbo_url' => $turboUrl
                    ]
                ];
            }

            $hotels = $this->processElementsData($hotelsData['elements'], $point, $radius, 'Unnamed Hotel');

            return [
                'hotels' => $hotels,
                'center' => [
                    'latitude' => $lat,
                    'longitude' => $lon,
                ],
                'radius' => $radius,
                'count' => count($hotels),
                'debug' => [
                    'api_response' => $hotelsData,
                    'query' => $query,
                    'processed_count' => count($hotels)
                ],
            ];
        } catch (\Throwable $e) {
            Log::error('Error finding hotels in radius', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return [
                'error' => $e->getMessage(),
                'success' => false,
            ];
        }
    }

    protected function buildOverpassQuery(array $boundingBox, array $tags): string
    {
        // Use sprintf for safe and precise formatting of coordinates
        $bboxString = sprintf(
            '%f,%f,%f,%f',
            $boundingBox['south'],
            $boundingBox['west'],
            $boundingBox['north'],
            $boundingBox['east']
        );

        $queryParts = [];
        foreach ($tags as $key => $value) {
            if (is_array($value)) { // for cases like "amenity" => ["restaurant", "cafe"]
                foreach ($value as $val) {
                    $queryString = "[\"$key\"=\"$val\"]";
                    $queryParts[] = "node$queryString($bboxString);";
                    $queryParts[] = "way$queryString($bboxString);";
                    $queryParts[] = "relation$queryString($bboxString);";
                }
            } elseif (is_string($value) && $value !== '*') { // for key=value, e.g. 'tourism' => 'hotel'
                $queryString = "[\"$key\"=\"$value\"]";
                $queryParts[] = "node$queryString($bboxString);";
                $queryParts[] = "way$queryString($bboxString);";
                $queryParts[] = "relation$queryString($bboxString);";
            } else { // for key=*, e.g. 'tourism' => '*' or just 'tourism'
                $queryString = "[\"$key\"]";
                $queryParts[] = "node$queryString($bboxString);";
                $queryParts[] = "way$queryString($bboxString);";
                $queryParts[] = "relation$queryString($bboxString);";
            }
        }

        $queryBody = implode("\n", $queryParts);

        return <<<QUERY
            [out:json];
            (
            $queryBody
            );
            out center;
QUERY;
    }




    protected function fetchFromOverpass(string $query): array
    {
        $url = 'https://overpass-api.de/api/interpreter';

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(['data' => $query]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_errno($curl)) {
            Log::error('Overpass API request failed (curl)', [
                'error' => curl_error($curl),
            ]);
            curl_close($curl);
            throw new \RuntimeException('Wala ka-connect sa Overpass API. Paki-check imong network.');
        }

        curl_close($curl);
        if ($httpCode !== 200) {
            Log::error('Overpass API nagbalik og sayop nga status', [
                'status' => $httpCode,
                'response' => $response, // << ADD THIS
            ]);
            throw new \RuntimeException("Overpass API nagbalik og status code: $httpCode");
        }
        $data = json_decode($response, true);

        if ($data === null) {
            Log::error('Overpass API returned non-JSON response', [
                'status' => $httpCode,
                'response' => $response,
            ]);
            throw new \RuntimeException("Overpass API returned non-JSON response for status code: $httpCode");
        }

        return $data;
    }




    protected function calculateBoundingBoxFromRadius(float $lat, float $lon, float $radius): array
    {
        // Rough approximation: 1 degree lat = 111 km
        $latDelta = $radius / 111;
        // 1 degree lon = 111 * cos(lat) km
        $lonDelta = $radius / (111 * cos(deg2rad($lat)));

        return [
            'south' => max(-90.0, $lat - $latDelta), // Clamp to -90
            'north' => min(90.0, $lat + $latDelta),  // Clamp to 90
            'west' => $lon - $lonDelta,
            'east' => $lon + $lonDelta
        ];
    }



    public function punoNgSaging(): array
    {
        try {

            return [];
        } catch (Throwable $e) {
            Log::error('', []);

            return [];
        }
    }
}
