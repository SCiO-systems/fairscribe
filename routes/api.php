<?php

use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\TeamsController;
use Illuminate\Support\Facades\Route;

// API v1
Route::prefix('v1')->name('api.v1.')->group(function () {

    // Authenticate a user.
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Authenticated and authorized (store) routes.
    Route::middleware(['auth:sanctum'])->group(function () {

        // Teams
        Route::apiResource('teams', TeamsController::class);

        // Logout
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});
