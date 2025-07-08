<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Carbon\Carbon;


Route::get('/', function () {
    return view('welcome');
});



Route::get('show_users',[UserController::class , 'showUsers']);
//
//Route::get('sendOtp',[UserController::class , 'sendOtp']);


//Route::get('carbon',function (){
//
//    $now = Carbon::now();
//    $future = Carbon::now()->addHours(6);
//    echo $now->diffInHours($future);
//});










