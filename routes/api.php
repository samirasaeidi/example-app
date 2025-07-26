<?php


use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

//Route::post('auth/register', [AuthController::class, 'register']);
//Route::post('auth/sendOtp', [AuthController::class, 'sendOtp']);
//Route::post('auth/resendCode', [AuthController::class, 'resendCode']);
//Route::post('auth/login', [AuthController::class, 'login']);
//Route::get('auth/showUser/{id}', [AuthController::class, 'showUser']);


Route::prefix('auth')->group(function (){
    Route::post('/register',[AuthController::class , 'register']);
    Route::post('/sendOtp',[AuthController::class , 'sendOtp']);
    Route::post('/resendCode',[AuthController::class , 'resendCode']);
    Route::post('/login',[AuthController::class , 'login']);
    Route::post('/showUser/{id}',[AuthController::class , 'showUser']);
});
