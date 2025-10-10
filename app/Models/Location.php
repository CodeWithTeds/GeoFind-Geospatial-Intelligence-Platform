<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasGeocoding;
use App\Traits\HasGeometricCalculations;
use App\Traits\HasGeohash;

class Location extends Model
{
    use HasFactory;
    use HasGeocoding;
    use HasGeometricCalculations;
    use HasGeohash;

    protected $fillable = [
        'name',
        'latitude',
        'longitude'
    ];

  
}
