<?php


use App\Http\Controllers\Admin\AdminCntroller;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Category\CategoryController;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/otp-send', [AuthController::class, 'sendOtp']);
    Route::post('/otp-resend', [AuthController::class, 'resendOtp']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::prefix('profile')->middleware('auth')->group(function () {
    Route::get('/', [ProfileController::class, 'showUser']);
});

Route::prefix('admin')->group(function () {
    Route::get('users/test', [AdminCntroller::class, 'indexTest']);
    Route::apiResource('users', AdminCntroller::class);
});

Route::prefix('category')->group(function () {
    Route::apiResource('categories', CategoryController::class);
});


