<?php

namespace App\Services;

use App\Models\Location;
use Throwable;

class GeometricService
{
    private const EARTH_RADIUS = 6371; // Earth's radius in kilometers

    public function calculateDistance(Location $point1, Location $point2): array
    {
        $lat1 = deg2rad($point1->latitude);
        $lon1 = deg2rad($point1->longitude);
        $lat2 = deg2rad($point2->latitude);
        $lon2 = deg2rad($point2->longitude);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;


        // haversine formula
        $a = sin($dlat / 2) ** 2 +
            cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distanceInKm = self::EARTH_RADIUS * $c;


        return [
            'distance' => [
                'kilometers' => round($distanceInKm, 2),
                'miles' => round($distanceInKm * 0.621371, 2),
                'meters' => round($distanceInKm * 1000),
                'formatted' => $this->formatDistance($distanceInKm),
            ],
            'points' => [
                'start' => [
                    'latitude' => $point1->latitude,
                    'longitude' => $point1->longitude,
                ],
                'end' => [
                    'latitude' => $point2->latitude,
                    'longitude' => $point2->longitude,
                ],
            ],
            // calculate the travel time
            'travel_time' => $this->estimatedTravelTime($distanceInKm),
        ];
    }


    private function formatDistance(float $distanceInKm): string
    {
        if ($distanceInKm < 1) {
            return round($distanceInKm * 1000) . ' meters';
        } elseif ($distanceInKm < 10) {
            return round($distanceInKm, 1) . ' km';
        } else {
            return round($distanceInKm) . ' km';
        }
    }

    private function estimatedTravelTime(float $distanceInKm): array
    {
        $speeds = [
            'walking' => 5,
            'cycling' => 15,
            'driving' => 50,
            'public_transport' => 30
        ];

        $times = [];

        foreach ($speeds as $mode => $speed) {
            $hours = $distanceInKm / $speed;
            $minutes = round($hours * 60);

            if ($minutes < 60) {
                $times[$mode] = $minutes . 'minutes';
                $minutes = round($hours * 60);
            } else {
                $hours = floor($minutes / 60);
                $remainingMinutes = $minutes % 60;
                $times[$mode] = $hours . ' hours ' . ($hours > 1 ? 's' : '') .
                    ($remainingMinutes > 0 ? ' ' . $remainingMinutes . ' minutes' : '');
            }
        }

        return $times;
    }


    public function calculateMidpoint(Location $point1, Location $point2): array
    {
        $lat1 = deg2rad($point1->latitude);
        $lon1 = deg2rad($point1->longitude);
        $lat2 = deg2rad($point2->latitude);
        $lon2 = deg2rad($point2->longitude);

        $dlon = $lon2 - $lon1;

        $bx = cos($lat2) * cos($dlon);
        $by = cos($lat2) * sin($dlon);
        $latMid = atan2(sin($lat1) + sin($lat2), sqrt((cos($lat1) + $bx) ** 2 + $by ** 2));
        $lonMid = $lon1 + atan2($by, cos($lat1) + $bx);

        $latitude = rad2deg($latMid);
        $longitude = rad2deg($lonMid);

        return [
            'latitude' => rad2deg($latMid),
            'longitude' => rad2deg($lonMid),
            'formatted' => [
                'latitude' => sprintf("%.6f° %s", abs($latitude), $latitude >= 0 ? 'N' : 'S'),
                'longitude' => sprintf("%.6f° %s", abs($longitude), $longitude >= 0 ? 'E' : 'W'),
            ]
        ];
    }

    public function calculateDistanceDetails(Location $point1, Location $point2, Location $point3): array
    {
        $a = $this->calculateDistance($point1, $point2)['distance']['kilometers'];
        $b = $this->calculateDistance($point2, $point3)['distance']['kilometers'];
        $c = $this->calculateDistance($point3, $point1)['distance']['kilometers'];
        // Heron's Formula for Area of Triangle
        $s = ($a + $b + $c) / 2;
        $area = sqrt($s * ($s - $a) * ($s - $b) * ($s - $c));

        $angleA = rad2deg(acos(($b * $b + $c * $c - $a * $a) / (2 * $b * $c)));
        $angleB = rad2deg(acos(($a * $a + $c * $c - $b * $b) / (2 * $a * $c)));
        $angleC = rad2deg(acos(($a * $a + $b * $b - $c * $c) / (2 * $a * $b)));

        return [
            'area' => [
                'value' => round($area, 2),
                'unit' => 'square kilometers',
                'formatted' => $this->formatArea($area)
            ],
            'sides' => [
                'a' => [
                    'value' => round($a, 2),
                    'unit' => 'kilometers',
                    'formatted' => $this->formatDistance($a),
                    'points' => [
                        'start' => $point1->name,
                        'end' => $point2->name
                    ]
                ],

                'b' => [
                    'value' => round($b, 2),
                    'unit' => 'kilometers',
                    'formatted' => $this->formatDistance($b),
                    'points' => [
                        'start' => $point2->name,
                        'end' => $point3->name
                    ]
                ],


                'c' => [
                    'value' => round($c, 2),
                    'unit' => 'kilometers',
                    'formatted' => $this->formatDistance($c),
                    'points' => [
                        'start' => $point3->name,
                        'end' => $point1->name
                    ]
                ]
            ],

            'angles' => [
                'A' => [
                    'value' => round($angleA, 2),
                    'unit' => 'degrees',
                    'formatted' => round($angleA, 2),
                    'vertex' => $point1->name
                ],

                'B' => [
                    'value' => round($angleB, 2),
                    'unit' => 'degrees',
                    'formatted' => round($angleB, 2),
                    'vertex' => $point2->name
                ],

                'C' => [
                    'value' => round($angleC, 2),
                    'unit' => 'degrees',
                    'formatted' => round($angleC, 2),
                    'vertex' => $point3->name
                ]
            ],

            'perimeter' => [
                'value' => round($a + $b + $c, 2),
                'unit' => 'kilometers',
                'formatted' => $this->formatDistance($a + $b + $c)
            ],

            'semi_perimeter' => [
                'value' => round($s, 2),
                'unit' => 'kilometers',
                'formatted' => $this->formatDistance($s)
            ]
        ];
    }

    public function calculateTriangleArea(Location $point1, Location $point2, Location $point3): float
    {
        $a = $this->calculateDistance($point1, $point2)['distance']['kilometers'];
        $b = $this->calculateDistance($point2, $point3)['distance']['kilometers'];
        $c = $this->calculateDistance($point3, $point1)['distance']['kilometers'];

        // Heron's Formula for Area of Triangle
        $s = ($a + $b + $c) / 2;
        return sqrt($s * ($s - $a) * ($s - $b) * ($s - $c));
    }

    private function formatArea(float $area): string
    {
        if ($area < 0.01) {
            return round($area * 1000000, 2) . 'square meters';
        } elseif ($area < 1) {
            return round($area * 100, 2) . 'hectares';
        } else {
            return round($area, 2) . 'square meters';
        }
    }

    public function calculateBearing(Location $point1, Location $point2): float
    {
        $lat1 = deg2rad($point1->latitude);
        $lon1 = deg2rad($point1->longitude);
        $lat2 = deg2rad($point2->latitude);
        $lon2 = deg2rad($point2->longitude);

        $dlon = $lon2 - $lon1;

        $y = sin($dlon) * cos($lat2);
        $x = cos($lat1) * sin($lat2) -
            sin($lat1) * cos($lat2) * cos($dlon);

        $initial_bearing = atan2($y, $x);

        return fmod((rad2deg($initial_bearing) + 360), 360);
    }

    public function findPointsInRadius(Location $center, float $radius): array
    {
        return Location::all()->filter(function ($location) use ($center, $radius) {
            if ($location->id === $center->id) {
                return false;
            }
            $distance = $this->calculateDistance($center, $location);
            $location->distance = $distance;
            return $distance['distance']['kilometers'] <= $radius;
        })->values()->toArray();
    }
}
