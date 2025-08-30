<?php

use App\Http\Controllers\Admin\AdminCntroller;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Profile\ProfileController;
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
    Route::apiResource('users', AdminCntroller::class);
});

Route::apiResource('categories', CategoryController::class);

Route::get('subCategories/{id}', [CategoryController::class, 'subCategories']);

Route::get('children',[CategoryController::class ,'children']);
