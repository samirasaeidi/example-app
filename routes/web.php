<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use Illuminate\Support\Arr;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/s', function () {


    $array = [
        'name' => 'Desk',
        'price' => 100,
    ];

    $keyed = Arr::prependKeysWith($array, 'product.');

    dd($keyed);


});




