<?php

namespace App\Traits;
use App\Services\GeometricService;
use App\Models\Location;

trait HasGeometricCalculations
{
  
      
    /**
     * Calculate distance to another location using Haversine formula
     */
    public function distanceTo(Location $other): array
    {
        return app(GeometricService::class)->calculateDistance($this, $other);
    }

    /**
     * Calculate midpoint between two locations
     */
    public function midpointTo(Location $other): array
    {
        return app(GeometricService::class)->calculateMidpoint($this, $other);
    }

    /**
     * Calculate triangle area with two other points
     */
    public function triangleAreaWith(Location $point2, Location $point3): float
    {
       return app(GeometricService::class)->calculateTriangleArea($this, $point2, $point3);
    }
    
    /**
     * Find points within a given radius
     */
    public function pointWithinRadius(float $radius): array
    {
        return app(GeometricService::class)->findPointsInRadius($this, $radius);
    }

    /**
     * Calculate bearing to another location
     */
    public function bearingTo(Location $other): float
    {
        return app(GeometricService::class)->calculateBearing($this, $other);
    }


}