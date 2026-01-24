<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\Api\QuestionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Location Routes
Route::prefix('locations')->group(function () {
    Route::post('/calculate-bearing', [LocationController::class, 'calculateBearing']);
    Route::post('/calculate-distance', [LocationController::class, 'calculateDistance']);
    Route::post('/calculate-midpoint', [LocationController::class, 'calculateMidpoint']);
    Route::post('/calculate-triangle-area', [LocationController::class, 'calculateTriangleArea']);
    Route::post('/find-points-in-radius', [LocationController::class, 'findPointInRadius']);
    Route::post('/to-geohash', [LocationController::class, 'toGeohash']);
    Route::get('/{location}/address', [LocationController::class, 'getAddress']);
});

// Question Routes
Route::apiResource('questions', QuestionController::class);
