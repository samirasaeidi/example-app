<?php

if(!function_exists('generate_otp_code')){
    function generate_otp_code()
    {
        return rand(0, 9) . rand(0, 9) . rand(10, 99);
    }
}
