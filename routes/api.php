<?php


use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Admin\AdminCntroller;
use \App\Http\Controllers\Profile\ProfileController;


Route::prefix('auth')->middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/otp-send', [AuthController::class, 'sendOtp']);
    Route::post('/otp-resend', [AuthController::class, 'resendOtp']);
    Route::post('/login', [AuthController::class, 'login']);
});
Route::prefix('profile')->middleware('auth')->group(function(){
    Route::get('/', [ProfileController::class, 'showUser']);
});

Route::prefix('admin')->group(function () {

    Route::resource('items',[AdminCntroller::class]);

    Route::post('/create', [AdminCntroller::class, 'create']);
    Route::post('/read/{id}',[AdminCntroller::class,'read']);
    Route::patch('/update/{id}',[AdminCntroller::class,'update']);
    Route::delete('/delete/{id}',[AdminCntroller::class,'delete']);

});
