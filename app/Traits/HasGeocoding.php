<?php

namespace App\Traits;
use App\Services\GeocodingService;


trait HasGeocoding{
   
    public function getAddress(): array
    {
        return app(GeocodingService::class)->getAddress($this);
        
    }
}