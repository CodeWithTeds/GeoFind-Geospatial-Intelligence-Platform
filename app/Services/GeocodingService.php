<?php

namespace App\Services;

use app\Traits\HasGeocoding;
use App\Models\Location;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use Throwable;


class GeocodingService
{

    use HasGeocoding;

    private const POI_SEARCH_RADIUS = 1000;
    private const POI_CATEGORIES = [
        'tourism' => 'Tourism',
        'amenity' => 'Amenity',
        'shop' => 'Shopping',

        'leisure' => 'Leisure'
    ];



    public function __construct(
        private GeometricService $geometricService
    ) {}



    public function getAddress(Location $location): array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'LocationApp/1.0'
            ])->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $location->latitude,
                'lon' => $location->longitude,
                'zoom' => 18,
                'addressdetails' => 1,
                'namedetails' => 1,
                'extratags' => 1,
                'statecode' => 1
            ]);

            Log::info('nominatim response', [

                'status' => $response->status(),
                'body' => $response->json()
            ]);

            $response->throw();

            if ($response->failed()) {
                throw new \Exception("failed to fetch address: " . $response->status());
            }

            // Get weather data
            $weatherResponse = Http::get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'current_weather' => true,
                'daily' => 'temperature_2m_max,temperature_2m_min',
                'timezone' => 'auto',
            ]);

            $elevationData = $this->getElevationData($location);

            $data = $response->json();
            $weatherResponse->throw();
            $weatherData = $weatherResponse->json();

            if (!isset($data['display_name'], $data['address'])) {
                throw new \Exception("Invalid address data received");
            }

            // Format the current time
            $currentTime = null;
            if (isset($weatherData['current_weather']['time'])) {
                try {
                    $dateTime = new \DateTime($weatherData['current_weather']['time']);
                    $currentTime = [
                        'formatted' => $dateTime->format('F j, Y, g:i A'),
                        'date' => $dateTime->format('Y-m-d'),
                        'time' => $dateTime->format('H:i:s'),
                        'is_daytime' => $weatherData['current_weather']['is_day'] ?? null
                    ];
                } catch (\Exception $e) {
                    Log::warning('Error formatting time', [
                        'error' => $e->getMessage(),
                        'time' => $weatherData['current_weather']['time']
                    ]);
                }
            }    


            $pointsOfInterest = $this->getPointsOfInterest($location);

            return [
                'full_address' => $data['display_name'],
                'latitude' => $data['lat'],
                'longitude' => $data['lon'],
                'address_components' => $this->formatAddressComponents($data['address']),
                'google_maps_link' => $this->getGoogleMapsLink($location),
                'elevation' => $elevationData,
                'points_of_interst' => $pointsOfInterest,
                'weather' => [
                    'current' => [
                        'temperature' => $weatherData['current_weather']['temperature'] ?? null,
                        'windspeed' => $weatherData['current_weather']['windspeed'] ?? null,
                        'winddirection' => $weatherData['current_weather']['winddirection'] ?? null,
                        'weathercode' => $weatherData['current_weather']['weathercode'] ?? null,
                        'time' => $currentTime,
                        'is_day' => $weatherData['current_weather']['is_day'] ?? null,
                    ],
                    'daily' => [
                        'max_temp' => $weatherData['daily']['temperature_2m_max'][0] ?? null,
                        'min_temp' => $weatherData['daily']['temperature_2m_min'][0] ?? null,
                    ]
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching address', [
                'location_id' => $location->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function getPointsOfInterest(Location $location): array
    {
        try {
            $poiQuery = $this->buildPoiQuery($location);

            $poiResponse = Http::get('http://overpass-api.de/api/interpreter', [
                'data' => $poiQuery,
            ]);

            if ($poiResponse->failed()) {
                throw new Exception('Failed to fetch points of interest: ' . $poiResponse->status());
            }

            $poiData = $poiResponse->json();
            if (!is_array($poiData)) {
                Log::warning('Overpass API returned an empty or invalid JSON payload', [
                    'status' => $poiResponse->status(),
                    'body' => $poiResponse->body(),
                    'location' => [
                        'latitude' => $location->latitude,
                        'longitude' => $location->longitude,
                    ],
                ]);

                return [];
            }

            return $this->processPoiData($poiData, $location);
        } catch (Throwable $e) {
            Log::warning('Error fetching PoI', [
                'error' => $e->getMessage(),
                'location' => [
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude
                ]
            ]);
            return [];
        }
    }

    private function processPoiData(array $poiData, Location $location): array
    {
        if (!isset($poiData['elements']) || !is_array($poiData['elements'])) {
            return [];
        }

        $pois = array_values(array_filter(array_map(function ($node) use ($location) {
            if (!is_array($node) || !isset($node['lat'], $node['lon'])) {
                return null;
            }

            $poiLocation = new Location([
                'latitude' => $node['lat'],
                'longitude' => $node['lon']
            ]);

            $distance = $this->geometricService->calculateDistance(
                $poiLocation,
                $location
            );

            return [
                'name' => $node['tags']['name'] ?? 'Unknown',
                'type' => $this->getPoiType($node['tags'] ?? []),
                'category' => $this->getPoiCategories($node['tags'] ?? []),
                'coordinates' => [$node['lat'], $node['lon']],
                'distance' => $distance['distance'],
                'address' => $node['tags']['addr:full'] ?? null,
                'description' => $node['tags']['description'] ?? null,
                'tags' => $node['tags'] ?? []
            ];
        }, $poiData['elements'])));

        usort($pois, function ($a, $b) {
            return ($a['distance']['kilometers'] ?? INF) <=> ($b['distance']['kilometers'] ?? INF);
        });

        return $pois;
    }



    private function getPoiCategories(array $tags): string
    {
        foreach (self::POI_CATEGORIES as $key => $category) {
            if (isset($tags[$key])) {
                return $category;
            }
        }

        return 'Unknown';
    }

    private function getPoiType(array $tags): string
    {
        foreach (self::POI_CATEGORIES as $key => $category) {
            if (isset($tags[$key])) {
                return (string) $tags[$key];
            }
        }

        return 'Other';
    }

    private function buildPoiQuery(Location $location): string
    {   
        $categories = array_keys(self::POI_CATEGORIES);
        $queryParts = [];

        foreach($categories as $category){
        $queryParts[] = "node(around:". self::POI_SEARCH_RADIUS .
            ", {$location->latitude}, {$location->longitude}, {$category})";
        }

        return "[out:json]; \n" . implode('\n', $queryParts) . "\nout body";
    }

    private function getElevationData(Location $location): array
    {
        $cacheKey = "Elevation_{$location->latitude}_{$location->longitude}";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::get('https://api.open-elevation.com/api/v1/lookup', [
                'locations' => "{$location->latitude},{$location->longitude}"
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['results'][0]['elevation'])) {
                    $elevationData = [
                        'elevation' => $data['results'][0]['elevation'],
                        'unit' => 'meters',
                        'source' => 'open-elevation'
                    ];

                    Cache::put($cacheKey, $elevationData, now()->addHour());

                    return $elevationData;
                }
            }

            return [
                'elevation' => null,
                'unit' => 'meters',
                'source' => 'unknown'
            ];
        } catch (\Throwable $e) {
            Log::warning('Error fetching elevation data from Open-Elevation API', [
                'error' => $e->getMessage(),
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
            ]);
        }

        return [
            'error' => 'Unable to fetch elevation data',
            'latitude' => $location->latitude,
            'longitude' => $location->longitude
        ];
    }

    private function getGoogleMapsLink(Location $location): string
    {
        return "https://www.google.com/maps?q={$location->latitude},{$location->longitude}";
    }

    private function formatAddressComponents(array $address): array
    {
        return [
            'house_number' => $address['house_number'] ?? null,
            'road' => $address['road'] ?? null,
            'neighbourhood' => $address['neighbourhood'] ?? null,
            'suburb' => $address['suburb'] ?? null,
            'city_district' => $address['city_district'] ?? null,
            'city' => $address['city']
                ?? $address['town']
                ?? $address['village']
                ?? $address['hamlet']
                ?? null,
            'county' => $address['county'] ?? null,
            'state_district' => $address['state_district'] ?? null,
            'state' => $address['state'] ?? null,
            'postcode' => $address['postcode'] ?? $address['postal_code'] ?? null,
            'country' => $address['country'] ?? null,
            'country_code' => $address['country_code'] ?? null,
            'continent' => $address['continent'] ?? null,
            'quarter' => $address['quarter'] ?? null,
            'commercial' => $address['commercial'] ?? null,
            'industrial' => $address['industrial'] ?? null,
            'residential' => $address['residential'] ?? null,
            'building' => $address['building'] ?? null,
            'floor' => $address['floor'] ?? null,
            'unit' => $address['unit'] ?? null,
            'region' => $address['region'] ?? null,
            'province' => $address['province'] ?? null,
            'municipality' => $address['municipality'] ?? null,
            'locality' => $address['locality'] ?? null,
            'district' => $address['district'] ?? null,
            'island' => $address['island'] ?? null,
            'archipelago' => $address['archipelago'] ?? null,
            'ocean' => $address['ocean'] ?? null,
            'sea' => $address['sea'] ?? null,
        ];
    }
}
