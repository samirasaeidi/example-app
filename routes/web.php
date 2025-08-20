<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/s', function () {

    $array = [
        [
            'user.product' => 'shoes',
            'user.color' => 'red',
        ],
    ];
    $sorted = Arr::undot($array);

    dd($sorted);
});
