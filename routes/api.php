<?php

use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\API\v1\UserController;
use App\Http\Controllers\API\v1\TeamsController;
use App\Http\Controllers\API\v1\UserAvatarController;
use App\Http\Controllers\API\v1\UserPasswordController;
use App\Http\Controllers\API\v1\UserTeamsController;
use App\Http\Controllers\API\v1\UserInvitesController;
use App\Http\Controllers\API\v1\UserTeamInvitesController;
use Illuminate\Support\Facades\Route;

// API v1
Route::prefix('v1')->name('api.v1.')->group(function () {

    // Authenticate a user.
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Register a new user.
    Route::post('/register', [UserController::class, 'store']);

    // Authenticated and authorized (store) routes.
    Route::middleware(['auth:sanctum'])->group(function () {

        // --- USER ROUTES ---

        // User management.
        Route::apiResource('users', UserController::class)->only(['index', 'show', 'update']);

        // User invites.
        Route::post('users/{user}/invites/{invite}/accept', [
            UserInvitesController::class, 'accept'
        ]);
        Route::post('users/{user}/invites/{invite}/reject', [
            UserInvitesController::class, 'reject'
        ]);
        Route::apiResource('users.invites', UserInvitesController::class)->only(['index']);

        // User avatar management. Issue with file upload using PUT, must use POST.
        Route::get('/users/{user}/avatar', [UserAvatarController::class, 'show']);
        Route::post('/users/{user}/avatar', [UserAvatarController::class, 'update']);
        Route::delete('/users/{user}/avatar', [UserAvatarController::class, 'destroy']);

        // Update user password.
        // TODO: Implement this.
        Route::post('/users/{user}/password', [UserPasswordController::class, 'update']);

        // User owned teams.
        Route::post('users/{user}/teams/{team}/invite', [
            UserTeamInvitesController::class, 'store'
        ]);
        Route::apiResource('users.teams', UserTeamsController::class);

        // --- TEAM ROUTES ---

        // All team routes.
        Route::apiResource('teams', TeamsController::class);

        // Logout.
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});
