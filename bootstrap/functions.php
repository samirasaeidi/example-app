<?php

if (! function_exists('generate_otp_code')) {
    function generate_otp_code()
    {
        return rand(0, 9).rand(0, 9).rand(10, 99);
    }
}

if (! function_exists('createSlug')) {
    function createSlug($string, $separator = '-')
    {
        $slug = preg_replace('/[^\p{L}\p{N}\s]+/u', '', $string);
        $slug = preg_replace('/\s+/', $separator, $slug);
        $slug = trim($slug, $separator);

        return strtolower($slug);

    }
}
