<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend(
            'mobile',
            function (string $attribute,mixed $value){
                return preg_match('/^(\+98|0)?(9(\d{2})(\d{3})(\d{4}))$/', $value);
            },
            "mobile number is invalid"
        );
    }
}
