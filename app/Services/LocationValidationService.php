<?php

namespace App\Services;

use App\Models\Location;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LocationValidationService
{


    /**
     * Validate location points
     *
     * @param int $point1Id
     * @param int $point2Id
     * @return bool
     * @throws ValidationException
     */
    public function validateLocationPoints(int $point1Id, int $point2Id): bool
    {
        $validator = Validator::make(
            ['point1_id' => $point1Id, 'point2_id' => $point2Id],
            [
                'point1_id' => ['required', 'integer', 'exists:locations,id'],
                'point2_id' => ['required', 'integer', 'exists:locations,id', 'different:point1_id'],
            ]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    /**
     * Validate radius
     *
     * @param float $radius
     * @return bool
     * @throws ValidationException
     */
    public function validateRadius(float $radius): bool
    {
        $validator = Validator::make(
            ['radius' => $radius],
            ['radius' => ['required', 'numeric', 'min:0']]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return true;
    }

    /**
     * Validate radius
     *
     * @param float $latitude
     * @return float $longitude
     * @return bool
     * @throws Validation Exception
     */

    public function validateCoordinates(float $latitude, float $longitude): bool
    {
        $validator = Validator::make(
            ['latitude' => $latitude, 'longitude' => $longitude],
            [
                'latitude' => ['required', 'numeric', 'between: -90,90'],
                'longitude' => ['required', 'numeric', 'between:180,180'],
            ]
        );

        return true;
    }

    public function validateTrianglePoints(int $point1Id, int $point2Id, int $point3Id): bool
    {
        $validator = Validator::make(
            [
                'point1_id' => $point1Id,
                'point2_id' => $point2Id,
                'point3_id' => $point3Id
            ],
            [
                'point1_id' => ['required', 'integer', 'exists:locations,id'],
                'point2_id' => ['required', 'integer', 'exists:locations,id', 'different:point1_id'],
                'point3_id' => ['required', 'integer', 'exists:locations,id', 'different:point1_id', 'different:point3_id'],

            ],
            [
                'point2_id.different' => 'Point 2 must be different from Point 1',
                'point3_id.different' => 'Point 3 must be different from point 1 and point 2',

            ]
        );

        if($validator->fails()){
            throw new ValidationException($validator);
        }

        return true;
    }



    /**
     * Validate route locations
     *
     * @param string $startLocation
     * @param string $endLocation
     * @return bool
     * @throws ValidationException
     */
    public function validateRouteLocations(string $startLocation, string $endLocation): bool
    {
        $validator = Validator::make(
            [
                'start_location' => $startLocation,
                'end_location' => $endLocation
            ],
            [
                'start_location' => ['required', 'string', 'min:3'],
                'end_location' => ['required', 'string', 'min:3', 'different:start_location'],
            ]

        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        } else {
            return true;
        }
    }

    /**
     * Check if location exists
     *
     * @param int $locationId
     * @return bool
     */
    public function locationExists(int $locationId): bool
    {
        return Location::where('id', $locationId)->exists();
    }
}
