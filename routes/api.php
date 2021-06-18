<?php

use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\API\v1\UserController;
use App\Http\Controllers\API\v1\TeamsController;
use App\Http\Controllers\API\v1\UserAvatarController;
use App\Http\Controllers\API\v1\UserPasswordController;
use App\Http\Controllers\API\v1\UserTeamsController;
use Illuminate\Support\Facades\Route;

// API v1
Route::prefix('v1')->name('api.v1.')->group(function () {

    // Authenticate a user.
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Authenticated and authorized (store) routes.
    Route::middleware(['auth:sanctum'])->group(function () {

        // --- USER ROUTES ---

        // User management.
        Route::apiResource('users', UserController::class)->only(['show', 'update']);

        // User avatar management. Issue with file upload using PUT, must use POST.
        Route::get('/users/{user}/avatar', [UserAvatarController::class, 'show']);
        Route::post('/users/{user}/avatar', [UserAvatarController::class, 'update']);
        Route::delete('/users/{user}/avatar', [UserAvatarController::class, 'destroy']);

        // Update user password.
        // TODO: Implement this.
        Route::post('/users/{user}/password', [UserPasswordController::class, 'update']);

        // Manage user owned teams.
        Route::apiResource('users.teams', UserTeamsController::class);

        // --- TEAM ROUTES ---

        // All team routes.
        Route::apiResource('teams', TeamsController::class);

        // Logout
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});
