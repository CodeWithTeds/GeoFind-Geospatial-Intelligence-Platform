<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LandingController;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Auth\LoginController;
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

Route::resource('locations', LocationController::class);

Route::post('/calculate-distance', [LocationController::class, 'calculateDistance'])->name('locations.calculate-distance');
Route::post('/calculate-midpoint', [LocationController::class, 'calculateMidpoint'])->name('locations.calculate-midpoint');
Route::post('/find-points-in-radius', [LocationController::class, 'findPointInRadius'])->name('locations.find-points-in-radius');
Route::post('/find-hotels-in-radius', [LocationController::class, 'findHotelsInRadius'])->name('locations.find-hotels-in-radius');
Route::post('/find-pois-in-radius', [LocationController::class, 'findPOIsInRadius'])->name('locations.find-pois-in-radius');
Route::post('/geofence-visualization', [LocationController::class, 'createGeofenceVisualization'])->name('locations.geofence-visualization');
Route::post('/calculate-triangle-area', [LocationController::class, 'calculateTriangleArea'])->name('locations.calculate-triangle-area');
Route::post('/locations/calculate-bearing', [LocationController::class, 'calculateBearing'])->name('locations.calculate-bearing');
Route::get('/locations/{location}/address', [LocationController::class, 'getAddress'])->name('locations.get-address');
Route::post('/locations/to-geohash', [LocationController::class, 'toGeohash']);
Route::post('/calculate-route', [LocationController::class, 'calculateRoute'])->name('locations.calculate-route');
Route::get('/distance-calculator', function () {
    return view('distance-calculator');
});

// New routes for additional features
Route::post('/calculate-convex-hull', [LocationController::class, 'calculateConvexHull'])->name('locations.calculate-convex-hull');
Route::post('/generate-grid', [LocationController::class, 'generateGrid'])->name('locations.generate-grid');
Route::post('/generate-heatmap', [LocationController::class, 'generateLocationHeatmap'])->name('locations.generate-heatmap');
Route::post('/find-location-clusters', [LocationController::class, 'findLocationClusters'])->name('locations.find-location-clusters');

// Admin Auth Routes
Route::prefix('admin')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('admin.login');
    Route::post('login', [LoginController::class, 'store'])
        ->middleware('throttle:login')
        ->name('admin.login.store');
    Route::post('logout', [LoginController::class, 'destroy'])->name('admin.logout');

    // Admin Dashboard (Protected)
    Route::middleware('auth')->group(function () {
        Route::get('dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        Route::resource('questions', \App\Http\Controllers\Admin\QuestionController::class)->names('admin.questions');
    });
});
