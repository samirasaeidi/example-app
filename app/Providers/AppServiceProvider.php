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
            function (string $attribute, mixed $value) {
                return preg_match('/^(\+98|0)?(9(\d{2})(\d{3})(\d{4}))$/', $value);
            },
            'mobile number is invalid'
        );

        Validator::extend(
            'national_code',
            function (string $attribute, mixed $value) {
                if (! preg_match('/^[0-9]{10}$/', $value)) {
                    return false;
                }
                for ($i = 0; $i < 10; $i++) {
                    if (preg_match('/^'.$i.'{10}$/', $value)) {
                        return false;
                    }
                }
                for ($i = 0, $sum = 0; $i < 9; $i++) {
                    $sum += ((10 - $i) * intval(substr($value, $i, 1)));
                }
                $ret = $sum % 11;
                $parity = intval(substr($value, 9, 1));
                if (($ret < 2 && $ret == $parity) || ($ret >= 2 && $ret == 11 - $parity)) {
                    return true;
                }

                return false;
            },
            'your national code is invalid'
        );

    }
}
