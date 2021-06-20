<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\LogoutRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthCheckRequest;
use App\Http\Resources\v1\UserResource;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Authenticate a user.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        $attempt = Auth::attempt($credentials);

        if (!$attempt) {
            return response()->json([
                'message' => 'The provided credentials do not match our records.',
                'errors' => [
                    'email' => 'The provided credentials do not match our records.',
                    'password' => 'The provided credentials do not match our records.',
                ]
            ], 401);
        }

        $request->session()->regenerate();

        return new UserResource($request->user());
    }

    /**
     * Logout a user.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(LogoutRequest $request)
    {
        // https://github.com/laravel/sanctum/issues/87#issuecomment-595952005
        Auth::guard('web')->logout();

        return response()->json([], 204);
    }

    /**
     * Return the authenticated user.
     *
     * @param AuthCheckRequest $request
     * @return user
     */
    public function user(AuthCheckRequest $request)
    {
        if (!$request->user()) {
            return response()->json(['errors' => [
                'error' => 'The user is not logged in.'
            ]], 401);
        }
        return new UserResource($request->user());
    }
}
