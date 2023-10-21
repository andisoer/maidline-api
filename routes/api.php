<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MaidController;
use App\Http\Controllers\MaidServicesController;


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

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOTP'])->middleware('throttle:5,1');
    Route::post('/login/google', [AuthController::class, 'loginGoogle']);
});

Route::middleware(['auth:sanctum'])->group(function () {

    Route::group(['prefix' => 'maid-services'], function () {
        Route::post('', [MaidServicesController::class, 'store']);
        Route::get('', [MaidServicesController::class, 'index']);
        Route::get('/{id}', [MaidServicesController::class, 'show']);
        Route::put('/{id}', [MaidServicesController::class, 'update']);
        Route::delete('/{id}', [MaidServicesController::class, 'destroy']);
    });

    Route::group(['prefix' => 'maids'], function () {
        Route::get('', [MaidController::class, 'index']);
    });
});
