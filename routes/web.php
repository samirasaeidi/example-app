<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/s', function () {

    $collection = collect([1, 2, 3, 4, 5]);

    $chunks = $collection->sliding(4);

//    $chunks->toArray();
    dd($chunks->toArray());
});
