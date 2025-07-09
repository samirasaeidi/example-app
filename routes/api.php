<?php


use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('auth/register',[AuthController::class , 'register']);

Route::post('auth/sendOtp',[AuthController::class , 'sendOtp']);

Route::post('auth/expiresTime',[AuthController::class , 'expiresTime']);
