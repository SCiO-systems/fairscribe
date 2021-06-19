<?php

use App\Http\Controllers\API\v1\AuthController;
use App\Http\Controllers\API\v1\OAuth\ORCIDController;
use App\Http\Controllers\API\v1\TeamInvitesController;
use App\Http\Controllers\API\v1\UserController;
use App\Http\Controllers\API\v1\TeamsController;
use App\Http\Controllers\API\v1\UserAvatarController;
use App\Http\Controllers\API\v1\UserPasswordController;
use App\Http\Controllers\API\v1\UserTeamsController;
use App\Http\Controllers\API\v1\UserInvitesController;
use App\Http\Controllers\API\v1\UserRepositoryController;
use App\Http\Controllers\API\v1\RepositoryTypesController;
use Illuminate\Support\Facades\Route;

// API v1
Route::prefix('v1')->name('api.v1.')->group(function () {

    // --- OAUTH ROUTES ---
    Route::prefix('oauth')->group(function () {

        // ORCID.
        Route::prefix('orcid')->group(function () {
            Route::get('/', [ORCIDController::class, 'redirect']);
            Route::get('/callback', [ORCIDController::class, 'callback']);
        });

        Route::prefix('globus')->group(function () {
            Route::get('/', function () {
                return response()->json("Not Implemented", 501);
            });
        });
    });


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
        Route::get('/users/{user}/teams/all', [UserTeamsController::class, 'all']);
        Route::apiResource('users.teams', UserTeamsController::class);

        // --- USER REPOSITORY ROUTES ---

        // User repositories.
        Route::get('/users/{user}/repositories/all', [UserRepositoryController::class, 'all']);
        Route::apiResource('users.repositories', UserRepositoryController::class);

        // --- REPOSITORY TYPE ROUTES ---
        Route::apiResource('repository_types', RepositoryTypesController::class)->only('index');

        // --- TEAM ROUTES ---

        // All team routes.
        Route::get('/teams/all', [TeamsController::class, 'all']);
        Route::post('/teams/{team}/invite', [TeamInvitesController::class, 'store']);
        Route::apiResource('teams', TeamsController::class)->only(['index', 'show']);

        // Logout.
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});
