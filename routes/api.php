<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check
Route::get('/healthz', [AuthController::class, 'healthz']);

// Public authentication routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes
Route::group(['middleware' => 'auth:api', 'prefix' => 'auth'], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/profile', [AuthController::class, 'profile']);
});

// Default authenticated user route
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// API status endpoint
Route::get('/status', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'Laravel Authentication API',
        'version' => '1.0.0',
        'timestamp' => now(),
    ]);
}); 