<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\SearchController;
use Illuminate\Support\Facades\Route;

// Global API rate limit: 60 requests/minute (spec §4).
Route::middleware('throttle:60,1')->group(function (): void {
    // Auth — stricter throttle on login to slow brute-force attempts.
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    // Public read endpoints.
    Route::get('/hotels', [HotelController::class, 'index']);
    Route::get('/search', SearchController::class);

    // Protected write endpoints (personal access token required).
    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/hotels', [HotelController::class, 'store']);
        Route::post('/rooms', [RoomController::class, 'store']);
        Route::post('/bookings', [BookingController::class, 'store']);
    });
});
