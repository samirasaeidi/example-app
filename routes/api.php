<?php


use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::prefix('profile')->group(function (){
    Route::middleware('guest')->post('/register',[AuthController::class , 'register']);
    Route::middleware('guest')->post('/otp-send',[AuthController::class , 'sendOtp']);
    Route::middleware('guest')->post('/otp-resend',[AuthController::class , 'resendOtp']);
    Route::middleware('guest')->post('/login',[AuthController::class , 'login']);
    Route::middleware('auth')->get('/profiles',[AuthController::class , 'showUser']);
});
