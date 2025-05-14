<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;
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
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghitung jumlah pengguna',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // old login function using email input
    // public function login(Request $request)
    // {
    //     $credentials = $request->only('username', 'password');
    //     $user = \App\Models\User::where('name', $credentials['username'])->first();

    //     if (!$user) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Username tidak ditemukan'
    //         ], 404);
    //     }
    //     $email = $user->email;
    //     if (!auth()->attempt(['name' => $credentials['username'], 'password' => $credentials['password']])) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Username atau password salah'
    //         ], 401);
    //     }

    //     $lock = Cache::lock('otp_lock_' . $email, 300);
    //     if (!$lock->get()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'OTP sedang dikirim ke email ini. Tunggu beberapa saat.'
    //         ]);
    //     }

    //     try {
    //         Cache::put('pending_user_' . $credentials['email'], $credentials['username'], now()->addMinutes(10));
    //         $this->sendOtp($email);
    //     } finally {
    //         $lock->release();
    //     }
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Login berhasil, OTP dikirim ke email',
    //         'redirect' => 'otp'
    //     ]);
    // }

    // public function login(Request $request)
    // {
    //     $credentials = $request->only('username', 'password');
    //     $user = \App\Models\User::where('name', $credentials['username'])->first();

    //     if (!$user) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Username tidak ditemukan'
    //         ], 404);
    //     }

    //     $email = $user->email;

    //     if (!auth()->attempt(['name' => $credentials['username'], 'password' => $credentials['password']])) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Username atau password salah'
    //         ], 401);
    //     }

    //     // Cek apakah sudah ada OTP aktif
    //     if (Cache::has('otp_' . $email)) {
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Login berhasil, OTP sudah dikirim sebelumnya',
    //             'redirect' => 'otp',
    //             'email' => $email
    //         ]);
    //     }

    //     // Lock untuk mencegah pengiriman ganda
    //     $lock = Cache::lock('otp_lock_' . $email, 5); // Cegah race condition
    //     if (!$lock->get()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'OTP sedang diproses. Coba beberapa detik lagi.'
    //         ]);
    //     }

    //     try {
    //         Cache::put('pending_user_' . $email, $credentials['username'], now()->addMinutes(10));
    //         $this->sendOtp($email);
    //     } finally {
    //         $lock->release();
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Login berhasil, OTP dikirim ke email',
    //         'redirect' => 'otp',
    //         'email' => $email
    //     ]);
    // }


    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        $user = \App\Models\User::where('name', $credentials['username'])->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Username tidak ditemukan'
            ], 404);
        }

        if (!auth()->attempt(['name' => $credentials['username'], 'password' => $credentials['password']])) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        $email = $user->email;

        $otpKey = 'otp_' . $user->id;
        $requestCountKey = 'otp_count_' . $user->id;
        $maxRequests = 5;

        // Cek apakah OTP sudah ada
        if (Cache::has($otpKey)) {
            return response()->json([
                'status' => true,
                'message' => 'Login berhasil, OTP sudah dikirim sebelumnya',
                'redirect' => 'otp',
                'email' => $email
            ]);
        }

        $requestCount = Cache::get($requestCountKey, 0);

        if ($requestCount >= $maxRequests) {
            return response()->json([
                'status' => false,
                'message' => 'Terlalu banyak permintaan OTP. Silakan coba lagi dalam beberapa saat.'
            ]);
        }

        // Tambah count dan simpan selama 30 detik
        Cache::put($requestCountKey, $requestCount + 1, now()->addSeconds(30));

        // Simpan user pending dan kirim OTP
        Cache::put('pending_user_' . $user->id, $user->name, now()->addMinutes(10));
        $this->sendOtp($user);

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil, OTP dikirim ke email',
            'redirect' => 'otp',
            'email' => $email
        ]);
    }

    public function sendOtp($user)
    {
        $otp = rand(100000, 999999);
        $otpKey = 'otp_' . $user->id;

        Cache::put($otpKey, $otp, now()->addMinutes(5));

        $user->otp = $otp;
        $user->save();

        try {
            Mail::to($user->email)->send(new OtpMail($otp, $user->name));
        } catch (Exception $e) {
            Log::error("Gagal mengirim OTP ke {$user->email}: " . $e->getMessage());
        }
    }

    public function verifyOtp(Request $request)
    {
        $email = $request->email;
        $otpInput = $request->otp;

        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $otpKey = 'otp_' . $user->id;
        $cachedOtp = Cache::get($otpKey);

        if (!$cachedOtp || (string)$cachedOtp !== (string)$otpInput) {
            return response()->json([
                'status' => false,
                'message' => 'OTP tidak valid atau telah kadaluarsa.'
            ], 400);
        }

        Cache::forget($otpKey);

        $user->otp = null;
        $user->save();

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
    }

    public function me()
    {
        try {
            return response()->json(JWTAuth::user());
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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
