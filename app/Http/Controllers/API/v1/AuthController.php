<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\LogoutRequest;
use App\Http\Controllers\Controller;

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
                'error' => 'The provided credentials do not match our records.',
            ], 401);
        }

        $request->session()->regenerate();

        return response()->json([], 204);
    }

    /**
     * Logout a user.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(LogoutRequest $request)
    {
        Auth::logout();

        return response()->json([], 204);
    }
}
