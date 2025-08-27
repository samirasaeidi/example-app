<?php

namespace App\Traits;

use App\Models\Otp;
use Illuminate\Validation\ValidationException;

trait OtpValidations
{
    public function validateSentCount(Otp $otpModel)
    {
        if ($otpModel->sent_count >= 5) {
            throw ValidationException::withMessages([
                'mobile' => 'Please wait for 10 minutes!',
            ]);
        }
    }

    public function validateLastSentAt(Otp $otpModel)
    {
        if ($otpModel->last_sent_at->diffInSeconds(now()) <= 10) {
            throw ValidationException::withMessages([
                'mobile' => 'Please wait for 60 seconds!',
            ]);
        }
    }
}
