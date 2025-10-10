<?php

namespace App\Providers;

use App\Services\LocationValidationService;
use App\Services\Computation\RadiusService;
use App\Services\GeometricService;
use Illuminate\Support\ServiceProvider;

class LocationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(LocationValidationService::class, function ($app) {
            return new LocationValidationService();
        });
        
        $this->app->singleton(RadiusService::class, function ($app) {
            return new RadiusService(
                $app->make(LocationValidationService::class),
                $app->make(GeometricService::class)
            );
        });
    }

    
    public function boot(): void
    {
        //
    }
} 