<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Mail\OtpMail;

class AuthController extends Controller
{
    public function userCount()
    {
        try {
            $count = \App\Models\User::count();

            return response()->json([
                'status' => true,
                'total_users' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghitung jumlah pengguna',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password', 'email');

        if (empty($credentials['email'])) {
            return response()->json([
                'status' => false,
                'message' => 'Email harus disertakan'
            ], 400);
        }

        if (!auth()->attempt(['name' => $credentials['username'], 'password' => $credentials['password']])) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        // Simpan data login sementara ke cache
        Cache::put('pending_user_' . $credentials['email'], $credentials['username'], now()->addMinutes(10));

        // Kirim OTP
        $this->sendOtp($credentials['email']);

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil, OTP dikirim ke email',
            'redirect' => 'otp'
        ]);
    }


    public function sendOtp($email)
    {
        $otp = rand(100000, 999999); // Generate OTP 6 digit
        Cache::put('otp_' . $email, $otp, now()->addMinutes(30)); // Simpan OTP di cache selama 10 menit

        // Kirim email OTP
        Mail::to($email)->send(new OtpMail($otp)); // Pastikan Anda membuat mail class OtpMail

        return response()->json([
            'status' => true,
            'message' => 'OTP berhasil dikirim ke email Anda'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $email = $request->email;
        $otp = $request->otp;

        $storedOtp = Cache::get('otp_' . $email);
        $username = Cache::get('pending_user_' . $email);

        if ($storedOtp && $storedOtp == $otp && $username) {
            Cache::forget('otp_' . $email);
            Cache::forget('pending_user_' . $email);

            // Ambil user berdasarkan username
            $user = \App\Models\User::where('name', $username)->first();

            if (!$user) {
                return response()->json(['status' => false, 'message' => 'User tidak ditemukan.'], 404);
            }

            // Buat token JWT
            $token = JWTAuth::fromUser($user);

            return response()->json([
                'status' => true,
                'message' => 'OTP terverifikasi, login berhasil.',
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
                'user' => $user,
                'redirect' => 'dashboard'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'OTP tidak valid atau telah kedaluwarsa.'
            ], 400);
        }
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
