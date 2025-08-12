<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/s',function (){


$cars = array(
    array("Volvo", 22, 18),
    array("BMW", 15, 13),
    array("Saab", 5, 2),
    array("Land Rover", 17, 15)
);

foreach ($cars as $car) {
    foreach ($car as $index => $value) {
        if ($index == 0) {
            echo "نام خودرو: $value\n";
        } elseif ($index == 1) {
            echo "تعداد موجودی: $value\n";
        } elseif ($index == 2) {
            echo "تعداد فروش: $value\n";
        }
    }
    echo "----------------------\n";
}




});




