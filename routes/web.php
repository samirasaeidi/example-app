<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


Route::get('/', function () {
    return view('welcome');
});



Route::get('show_users',[UserController::class , 'showUsers']);
//
//Route::get('sendOtp',[UserController::class , 'sendOtp']);
//










