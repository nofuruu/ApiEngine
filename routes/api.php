    <?php

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\Api\ProdukController;
    use App\Http\Controllers\Api\AuthController;

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


    Route::middleware(['auth:api'])->group(function () {
        Route::get('/produk', [ProdukController::class, 'index']);
        Route::post('/produk', [ProdukController::class, 'store']);
        Route::get('/produk/search',[ProdukController::class, 'search']);
        Route::get('/produk/{id}', [ProdukController::class, 'show']);
        Route::put('/produk/{id}', [ProdukController::class, 'update']);
        Route::delete('/produk/{id}', [ProdukController::class, 'destroy']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
