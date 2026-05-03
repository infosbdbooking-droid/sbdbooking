<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\carController;
use App\Http\Controllers\api\CustomerController;
use App\Http\Controllers\api\settingsController;
use App\Http\Controllers\api\CabOrderController;
use App\Http\Controllers\ChargeCalculationController;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public Car endpoints (read-only)
Route::prefix('v1')->group(function () {
    Route::post('/loginOrRegister', [CustomerController::class, 'loginOrRegister']);
    Route::get('/settings', [settingsController::class, 'index']);
    Route::get('/cars', [CarController::class, 'index']);
    Route::post('/carDetails', [CarController::class, 'carDetails']);

    // Charge Calculation endpoints
    Route::post('/calculate-charges', [ChargeCalculationController::class, 'calculateCharges']);
    Route::get('/car/{id}/charges', [ChargeCalculationController::class, 'getCarCharges']);

    // ── Cab Orders ──────────────────────────────────────────────────────────
    // Place order (public – guest or logged-in customer)
    Route::post('/cab-orders', [CabOrderController::class, 'placeOrder']);

    // Order detail (public read, ownership checked if token present)
    Route::get('/cab-orders/{orderNumber}', [CabOrderController::class, 'orderDetail']);

    // Authenticated customer routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/cab-orders',                          [CabOrderController::class, 'myOrders']);
        Route::post('/cab-orders/{orderNumber}/cancel',    [CabOrderController::class, 'cancelOrder']);
    });
});
