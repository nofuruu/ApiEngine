<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (!$token = JWTAuth::attempt(['name' => $credentials['username'], 'password' => $credentials['password']])) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }
        return response()->json([
            'status' => true,
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => JWTAuth::user(),
            'redirect' => '/dashboard',

        ]);
    }

    public function me()
    {
        try {
            return response()->json(JWTAuth::user());
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json(['message' => 'Logout berhasil']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal logout'
            ], 500);
        }
    }
}
