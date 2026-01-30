<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LandingController;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Client\Auth\LoginController as ClientLoginController;
use App\Livewire\Client\Auth\Login;
use App\Http\Middleware\IpControlMiddleware;
use App\Livewire\Client\Auth\Register;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [LandingController::class, 'index']);

use App\Http\Controllers\Client\Auth\RegisterController;

// Client Auth Routes
Route::middleware('guest:web')->group(function () {
    Route::get('login', Login::class)
        ->middleware([IpControlMiddleware::class])
        ->name('login');
    
    Route::get('register', Register::class)->name('register');
    Route::post('register', [RegisterController::class, 'store']);
});

Route::post('logout', [ClientLoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Protected Game Route
Route::get('/play', function () {
    return view('play');
})->middleware(['auth'])->name('play');

// Admin Auth Routes
Route::prefix('admin')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('admin.login');
    Route::post('login', [LoginController::class, 'store'])
        ->middleware('throttle:login')
        ->name('admin.login.store');
    Route::post('logout', [LoginController::class, 'destroy'])->name('admin.logout');

    // Admin Dashboard (Protected)
    Route::middleware('auth')->group(function () {
        Route::get('dashboard', \App\Livewire\Admin\Dashboard::class)->name('admin.dashboard');

        // Questions CRUD (Livewire SPA)
        Route::prefix('questions')->name('admin.questions.')->group(function () {
            Route::get('/', \App\Livewire\Admin\Questions\Index::class)->name('index');
            Route::get('/create', \App\Livewire\Admin\Questions\Create::class)->name('create');
            Route::get('/{id}/edit', \App\Livewire\Admin\Questions\Edit::class)->name('edit');
        });

        // Location Routes (Protected)
        Route::resource('locations', LocationController::class)->names('admin.locations');
        
        Route::post('/calculate-distance', [LocationController::class, 'calculateDistance'])->name('admin.locations.calculate-distance');
        Route::post('/calculate-midpoint', [LocationController::class, 'calculateMidpoint'])->name('admin.locations.calculate-midpoint');
        Route::post('/find-points-in-radius', [LocationController::class, 'findPointInRadius'])->name('admin.locations.find-points-in-radius');
        Route::post('/find-hotels-in-radius', [LocationController::class, 'findHotelsInRadius'])->name('admin.locations.find-hotels-in-radius');
        Route::post('/find-pois-in-radius', [LocationController::class, 'findPOIsInRadius'])->name('admin.locations.find-pois-in-radius');
        Route::post('/geofence-visualization', [LocationController::class, 'createGeofenceVisualization'])->name('admin.locations.geofence-visualization');
        Route::post('/calculate-triangle-area', [LocationController::class, 'calculateTriangleArea'])->name('admin.locations.calculate-triangle-area');
        Route::post('/locations/calculate-bearing', [LocationController::class, 'calculateBearing'])->name('admin.locations.calculate-bearing');
        Route::get('/locations/{location}/address', [LocationController::class, 'getAddress'])->name('admin.locations.get-address');
        Route::post('/locations/to-geohash', [LocationController::class, 'toGeohash'])->name('admin.locations.to-geohash');
        Route::post('/calculate-route', [LocationController::class, 'calculateRoute'])->name('admin.locations.calculate-route');
        Route::get('/distance-calculator', function () {
            return view('distance-calculator');
        })->name('admin.locations.distance-calculator-view');

        Route::post('/calculate-convex-hull', [LocationController::class, 'calculateConvexHull'])->name('admin.locations.calculate-convex-hull');
        Route::post('/generate-grid', [LocationController::class, 'generateGrid'])->name('admin.locations.generate-grid');
        Route::post('/generate-heatmap', [LocationController::class, 'generateLocationHeatmap'])->name('admin.locations.generate-heatmap');
        Route::post('/find-location-clusters', [LocationController::class, 'findLocationClusters'])->name('admin.locations.find-location-clusters');
    });
});
