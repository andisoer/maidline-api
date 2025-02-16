<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MaidController;
use App\Http\Controllers\MaidHourlyPriceController;
use App\Http\Controllers\MaidScheduleController;
use App\Http\Controllers\MaidServicesController;
use App\Http\Controllers\PromosController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\AddressController;

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
    Route::post('/resend-otp', [AuthController::class, 'resendOTP'])->middleware('throttle:5,1');
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

    Route::group(['prefix' => 'promos'], function () {
        Route::get('', [PromosController::class, 'index']);
        Route::post('', [PromosController::class, 'store']);
    });

    Route::group(['prefix' => 'user'], function () {
        Route::group(['prefix' => 'profile'], function () {
            Route::post('/edit', [AuthController::class, 'editProfile']);
        });

        Route::group(['prefix' => 'address'], function () {
            Route::get('', [AddressController::class, 'index']);
            Route::post('', [AddressController::class, 'store']);
            Route::put('/{id}', [AddressController::class, 'update']);
        });
    });

    Route::group(['prefix' => 'maid'], function () {
        Route::get('', [MaidController::class, 'index']);
        Route::get('/{id}', [MaidController::class, 'show']);
        Route::post('', [MaidController::class, 'store']);

        Route::group(['prefix' => 'hourly-price'], function () {
            Route::post('', [MaidHourlyPriceController::class, 'store']);
        });

        Route::group(['prefix' => 'schedule'], function () {
            Route::post('', [MaidScheduleController::class, 'store']);
        });
    });

    Route::group(['prefix' => 'transaction'], function () {
        Route::get('', [TransactionsController::class, 'index']);
        Route::get('/{transaction_id}', [TransactionsController::class, 'show']);
        Route::post('callback', [TransactionsController::class, 'handleCallback']);
    });
});
