<?php

namespace App\Services;

use App\Models\Location;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeohashService
{
    private const LOCATION_TYPES = [
        'OCEAN' => 'Ocean',
        'SEA' => 'Sea',
        'MOUNTAIN' => 'Mountain',
        'FOREST' => 'Forest',
        'CITY' => 'City',
        'TOWN' => 'Town',
        'VILLAGE' => 'Village',
        'PLACE' => 'Place',
    ];

    private const WORD_MAP = [
        '0' => 'Zero',
        '1' => 'One',
        '2' => 'Two',
        '3' => 'Three',
        '4' => 'Four',
        '5' => 'Five',
        '6' => 'Six',
        '7' => 'Seven',
        '8' => 'Eight',
        '9' => 'Nine',
        'b' => 'Blue',
        'c' => 'Cloud',
        'd' => 'Dream',
        'e' => 'Echo',
        'f' => 'Forest',
        'g' => 'Gold',
        'h' => 'Horizon',
        'j' => 'Journey',
        'k' => 'Kind',
        'm' => 'Moon',
        'n' => 'Nest',
        'p' => 'Path',
        'q' => 'Quiet',
        'r' => 'River',
        's' => 'St',
        't' => 'Trail',
        'u' => 'Unity',
        'v' => 'Valley',
        'w' => 'Wave',
        'x' => 'Xylophone',
        'y' => 'Yellow',
        'z' => 'Zen'
    ];


    public function toGeohash(Location $location): array
    {
        try {
            if (!is_numeric($location->latitude) || !is_numeric($location->longitude)) {
                throw new \InvalidArgumentException('Latitude and Longitude must be numeric.');
            }

            $lat = (float)$location->latitude;
            $lon = (float)$location->longitude;

            if ($lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
                throw new \InvalidArgumentException('Invalid coordinate ranges. Latitude must be between -90 and 90, longitude between -180 and 180');
            }

            Log::info('Converting coordinates to geohash:', [
                'latitude' => $lat,
                'longitude' => $lon
            ]);

            $base32 = '0123456789bcdefghjkmnpqrstuvwxyz';
            $bits = [16, 8, 4, 2, 1];
            $geohash = '';
            $latMin = -90.0;
            $latMax = 90.0;
            $lonMin = -180.0;
            $lonMax = 180.0;
            $bit = 0;
            $ch = 0;
            $even = true;
            $precision = 12;
            $boundingBoxes = [];

            while (strlen($geohash) < $precision) {
                if ($even) {
                    $mid = ($lonMin + $lonMax) / 2;
                    if ($lon > $mid) {
                        $ch |= $bits[$bit];
                        $lonMin = $mid;
                    } else {
                        $lonMax = $mid;
                    }
                } else {
                    $mid = ($latMin + $latMax) / 2;
                    if ($lat > $mid) {
                        $ch |= $bits[$bit];
                        $latMin = $mid;
                    } else {
                        $latMax = $mid;
                    }
                }

                $even = !$even;

                if ($bit < 4) {
                    $bit++;
                } else {
                    $geohash .= $base32[$ch];
                    $boundingBoxes[strlen($geohash)] = [
                        'min_lat' => $latMin,
                        'max_lat' => $latMax,
                        'min_lon' => $lonMin,
                        'max_lon' => $lonMax,
                        'width_km' => $this->calculateDistanceBetweenPoints($latMin, $lonMin, $latMin, $lonMax),
                        'height_km' => $this->calculateDistanceBetweenPoints($latMin, $lonMin, $latMax, $lonMin)
                    ];
                    $bit = 0;
                    $ch = 0;
                }
            }

            $result = [
                'geohash' => $geohash,
                'mnemonic' => $this->generateMnemonic($geohash, $location),
                'precision' => [
                    'length' => strlen($geohash),
                    'description' => match (strlen($geohash)) {
                        1 => '±2500 km',
                        2 => '±630 km',
                        3 => '±78 km',
                        4 => '±20 km',
                        5 => '±2.4 km',
                        6 => '±610 m',
                        7 => '±76 m',
                        8 => '±19 m',
                        9 => '±2.4 m',
                        10 => '±60 cm',
                        11 => '±7.5 cm',
                        12 => '±1.9 cm',
                        default => 'Unknown precision'
                    }
                ],
                'bounding_box' => $boundingBoxes[strlen($geohash)]
            ];

            Log::info('Geohash conversion successful:', $result);
            return $result;
        } catch (\Exception $e) {
            Log::error('Error in toGeohash method:', [
                'error' => $e->getMessage(),
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }


    private function generateMnemonic(string $geohash, Location $location): string
    {
        $words = [];

        for ($i = 0; $i < strlen($geohash); $i++) {
            $char = $geohash[$i];
            if (isset(self::WORD_MAP[$char])) {
                $words[] = self::WORD_MAP[$char];
            }
        }


        $sentence = join(' ', $words);
        $locationType = $this->getLocationType($location);
        return "{$locationType} location: " . $sentence;
    }

    private function getLocationType(Location $location): string
    {
        try {
            $geocodingService = app(GeocodingService::class);
            $address = $geocodingService->getAddress($location);
            $components = $address['address_components'] ?? [];

            return match (true) {
                !empty($components['ocean']) => self::LOCATION_TYPES['OCEAN'],
                !empty($components['sea']) => self::LOCATION_TYPES['SEA'],
                !empty($components['mountain']) => self::LOCATION_TYPES['MOUNTAIN'],
                !empty($components['forest']) => self::LOCATION_TYPES['FOREST'],
                !empty($components['city']) => self::LOCATION_TYPES['CITY'],
                !empty($components['town']) => self::LOCATION_TYPES['TOWN'],
                !empty($components['village']) => self::LOCATION_TYPES['VILLAGE'],
                !empty($components['town']) => self::LOCATION_TYPES['TOWN'],
                !empty($components['village']) => self::LOCATION_TYPES['VILLAGE'],
                default => self::LOCATION_TYPES['PLACE']
            };
        } catch (\Exception $e) {
            Log::error('Error in getLocationType:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 'Location';
        }
    }

    private function calculateDistanceBetweenPoints(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) ** 2 +
            cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
