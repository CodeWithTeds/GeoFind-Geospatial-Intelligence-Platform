<?php

namespace App\Services\Computation;

use App\Models\Location;
use League\Geotools\Coordinate\Coordinate;
use League\Geotools\Coordinate\Ellipsoid;
use League\Geotools\Geotools;
use League\Geotools\CLI\Command\Distance\Haversine;
use League\Geotools\Convert\Convert;
use App\Utils\response;
use Faker\Core\Coordinates;
use Geocoder\Collection;
use Throwable;
use Illuminate\Support\Facades\Log;
use League\Geotools\Polygon\Polygon;

use function Laravel\Prompts\error;

/**
 * A utility class for performing geodesic calculations.
 */
class GeoToolsFeatures
{
    /**
     * @var Geotools|null The Geotools instance.
     */
    private static ?Geotools $geotools = null;

    /**
     * @return Geotools
     */


    private static function getGeotools(): Geotools
    {
        if (!isset(self::$geotools)) {
            self::$geotools = new Geotools();
        }

        return self::$geotools;
    }



    public function distance($from, $to)
    {
        if (!$from instanceof Coordinate) {
            $from = $this->coordinate($from);
        }

        if (!$to instanceof Coordinate) {
            $to = $this->coordinate($to);
        }

        return $this->geotools->distance()->setFrom($from)->setTo($to);
    }


    // public function findLocationsWithRadius(float $latitude, float $longitude, float $radiuskm, Collection $locations): Collection 
    // {
    //     $center = new Coordinate([$latitude, $longitude]);     

    //     return $locations->map(function ($location) use ($center) {

    //     });
    // }


    public function bearing($from, $to)
    {
        if (!$from instanceof Coordinate) {
            $from = $this->coordinate($from);
        }

        if (!$to instanceof Coordinate) {
            $to = $this->coordinate($to);
        }

        return $this->geotools->distance()->setFrom($from)->setTo($to);
    }


    public function vertex($from)
    {
        if (!$from instanceof Coordinate) {
            $from = $this->coordinate($from);
        }

        return $this->geotools->vertex()->setFrom($from);
    }


    public function calculateGreatCirclePath(float $lat1, float $lat2, float $lon1, float $lon2): array
    {
        $from = new Coordinate([$lat1, $lon1]);
        $to = new Coordinate([$lat2, $lon2]);
        
        // TODO: Implement calculation
        return [];
    }



    public function isPointInPolygon(float $latitude, float $longitude, array $vertices): bool
    {
        $polygon = new Polygon();

        foreach ($vertices as $vertex) {
            $polygon->add(new Coordinate([$vertex[0], $vertex[1]]));
        }

        $point = new Coordinate([$latitude, $longitude]);

        return $polygon->pointInPolygon($point);
    }



    // public function calculateFlatDistance(float $lat1, float $lat2, float $lon1, float $lon2, string $unit = 'km'): array
    // {
    //     $from = new Coordinate([$lat1, $lon1]);
    //     $to = new Coordinate([$lat2, $lon2]);

    //     $distance = (new Geotools())->distance()->setFrom($from)->setTo($to);

    //     return [
    //         'flat_in_meters' => $distance->intial(),
    //         'flat_in_unit' => $distance->in($unit)->flat()
    //     ];
    // }



    // public static function convertToDMS(float $latitude, float $longitude): array
    // {
    //     $convert = new Coordinate([$latitude, $longitude]);
    //     $dms = $convert->toDMS();

    //     return [
    //         'latitude' => [],
    //         'longitude' => [
    //             'decimal' => $longitude,
    //             'dms' => $convert->getLongitude(), // this too
    //         ],
    //     ];
    // }

    //    public function calculateVincentyDistance(float $lat1, float $lng1, float $lat2, float $lng2): array 
    //     {
    //         $from = new Coordinate([$lat1, $lng1]);
    //         $to = new Coordinate([$lat2, $lng2]);

    //         $distance = self::getGeotools()->distance()->setFrom($from)->setTo($to);
    //     }


    public function coordinate($coordinates): Coordinate
    {
        if (!$coordinates instanceof Coordinate) {
            return $coordinates;
        }

        if ($coordinates instanceof Location) {
            return new Coordinate([$coordinates->latitude, $coordinates->longitude]);
        }

        return new Coordinate($coordinates);
    }


    // public static function generateBoundingBox(float $latitude, float $longitude): array
    // {
    //     $geotools = new Geotools();
    //     $coordinate = new Coordinate([$latitude, $longitude]);

    //     $dmsConverter = $geotools->convert($coordinate);
    //     $dms = $dmsConverter->();
    // }   


    public function convert($coordinates)
    {
        if (!$coordinates instanceof Coordinate) {
            $coordinates = $this->coordinate($coordinates);
        }
    }


    // public static function getDistance(): array
    // {
    // $coordA   = Geotools::coordinate([48.8234055, 2.3072664]);
    // $coordB   = Geotools::coordinate([43.296482, 5.36978]);
    // $distance = Geotools::distance()->setFrom($coordA)->setTo($coordB);

    // printf("%s\n",$distance->flat()); // 659166.50038742 (meters)
    // printf("%s\n",$distance->in('km')->haversine()); // 659.02190812846
    // printf("%s\n",$distance->in('mi')->vincenty()); // 409.05330679648
    // printf("%s\n",$distance->in('ft')->flat()); // 2162619.7519272
    // }

    // public function calculateHaversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): array 
    // {
    //     $from = new Coordinate([$lat1, $lng1]);
    //     $to = new Coordinate([$lat2, $lng2]);

    //     $distance = $this->geotools->distance()->setFrom($from)->setTo($to);

    //     $distanceInMeters = $distance->haversine();
    //     $distanceInKm = $distanceInMeters / 1000;

    // }



    public function geohash()
    {
        return $this->geotools->geohash();
    }

    // public function convertDegreesMinutesSeconds(float $latitude, float $longitude, ?string $format = null): array
    // {
    //     try{
    //         $coordinate = new Coordinate([$latitude, $longitude]);
    //         $converter = new Convert($coordinate);

    //         if($format){
    //             $dm = $converter->toDecimalMinutes();
    //         }else{
    //             $dm = $converter->toDecimalMinutes();
    //         }       

    //         return [
    //             'latitude' => [
    //                 'decimal' => $latitude,
    //                 'dm' => $dm->getLatitude(),
    //             ],
    //             'longitude' => [
    //                 'decimal' => $longitude,
    //                 'dm' => $dm->getLongitude(),
    //             ],
    //     ];

    //     }catch (Throwable $e) {
    //         Log::error('Density calculation error', [
    //             'message' => $e->getMessage(),
    //     ]);
    // }

    // public function convertToDMS(float $latitude, float $longitude): array
    // {
    //     $coordinate = new Coordinate([$latitude, $longitude]);
    //     $converter = new Convert($coordinate);

    //     $dmsObject = $converter->toDegreesMinutesSeconds(); // ✅ this is an object, not array

    //     return [
    //         'latitude' => [
    //             'decimal' = > $latitude,
    //             'dms' => $dmsObject->getLatitude(),
    //         ],
    //         'longitude' => [
    //             'decimal' => $longitude,
    //             'dms' => $dmsObject->getLongitude(),
    //         ],
    //     ];
    // }
}
