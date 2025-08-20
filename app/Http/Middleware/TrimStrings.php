<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as TrimStringsBase;
use Illuminate\Support\Str;

class TrimStrings extends TrimStringsBase
{
    protected function transform($key, $value)
    {
        $except = array_merge($this->except, static::$neverTrim);

        if ($this->shouldSkip($key, $except) || ! is_string($value)) {
            return $value;
        }

        if (str_contains($key, 'mobile')) {
            $value = $this->mobileTransform($value);
        }

        return Str::trim($value);
    }

    protected function mobileTransform($value): string
    {
        $pattern = '/^(?:(\+98|0))(9(\d+))/';
        preg_match($pattern, $value, $matches);

        return $matches[2];
    }
}
