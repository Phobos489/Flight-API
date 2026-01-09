<?php
// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AirlineController,
    AirportController,
    FlightController
};

// Public routes - Prefix: /api
Route::prefix('v1')->group(function () {
    
    // Airlines
    Route::get('airlines', [AirlineController::class, 'index']);
    Route::get('airlines/{airline}', [AirlineController::class, 'show']);
    
    // Airports
    Route::get('airports', [AirportController::class, 'index']);
    Route::get('airports/{airport}', [AirportController::class, 'show']);
    
    // Flights
    Route::get('flights', [FlightController::class, 'index']);
    Route::get('flights/departures', [FlightController::class, 'departures']);
    Route::get('flights/arrivals', [FlightController::class, 'arrivals']);
    Route::get('flights/{flight}', [FlightController::class, 'show']);
    
    // Protected routes (untuk admin - require authentication)
    // Uncomment jika sudah setup Sanctum
    // Route::middleware('auth:sanctum')->group(function () {
        
        // Airlines Management
        Route::post('airlines', [AirlineController::class, 'store']);
        Route::put('airlines/{airline}', [AirlineController::class, 'update']);
        Route::delete('airlines/{airline}', [AirlineController::class, 'destroy']);
        
        // Airports Management
        Route::post('airports', [AirportController::class, 'store']);
        Route::put('airports/{airport}', [AirportController::class, 'update']);
        Route::delete('airports/{airport}', [AirportController::class, 'destroy']);
        
        // Flights Management
        Route::post('flights', [FlightController::class, 'store']);
        Route::put('flights/{flight}', [FlightController::class, 'update']);
        Route::delete('flights/{flight}', [FlightController::class, 'destroy']);
        Route::patch('flights/{flight}/status', [FlightController::class, 'updateStatus']);
    // });
});