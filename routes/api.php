<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeliveryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/verifyOtp', [AuthController::class, 'verifyOtp'])->name('verifyOtp');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::middleware(['jwt.auth'])->group(function () {
    Route::get('/users', [AuthController::class, 'userCount']);

    //Routes Produk
    Route::get('/produk', [ProdukController::class, 'datatable']);
    Route::post('/produk', [ProdukController::class, 'store']);
    Route::get('/produk/search', [ProdukController::class, 'search']);
    Route::get('/produk/{id}', [ProdukController::class, 'show']);
    Route::put('/produk/{id}', [ProdukController::class, 'update']);
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    //Route Delivery
    Route::get('/delivery', [DeliveryController::class, 'datatable']);
    Route::post('/delivery', [DeliveryController::class, 'store']);
    Route::get('/delivery/{id}', [DeliveryController::class, 'show']);
    Route::put('/delivery/{id}', [DeliveryController::class, 'update']);
    Route::delete('/delivery/{id}', [DeliveryController::class, 'destroy']);
});
