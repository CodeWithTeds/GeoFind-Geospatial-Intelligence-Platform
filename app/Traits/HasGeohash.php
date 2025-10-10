<?php

namespace App\Traits;

use App\Services\GeohashService;


trait HasGeohash
{

    public function toGeohash(): array
    {
        return app(GeohashService::class)->toGeohash($this);
    }
}
