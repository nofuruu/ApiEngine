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
            'redirect' => 'dashboard',

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

    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|unique:users,name',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'departement' => 'required|string|max:255',
            'phone' => 'required|digits_between:10,15|unique:users,phone',
            'email' => [
                'required',
                'unique:users,email',
                'regex:/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', // Menambahkan regex untuk validasi format email yang lebih ketat
            ],
        ]);

        // Simpan user ke database
        $user = \App\Models\User::create([
            'name' => $request->name,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'departement' => $request->departement,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt(value: $request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Register berhasil. Silakan login.',
            'redirect' => 'login'
        ], 201);
    }
}
