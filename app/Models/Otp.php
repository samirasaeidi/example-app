<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'mobile',
        'code',
        'expires_at',
        'last_sent_at',
    ];

    protected function casts()
    {
        return [
            'last_sent_at' => 'datetime',
        ];
    }

    public function storeNewOtpCode($otpCode)
    {
        $this->code = $otpCode;
        $this->expires_at = now()->addSeconds(10);
        $this->last_sent_at = now();
        $this->sent_count += 1;
        $this->try += 1;
        $this->save();
    }

    public function updateOrCreateOtp(int $mobile, int $otp, ?string $password)
    {

        return $this->newQuery()->firstOrCreate([
            'mobile' => $mobile,
        ], [
            'password' => $password,
            $this->code = $otp,
            $this->expires_at = now()->addSeconds(60),
            $this->last_sent_at = now(),
            $this->sent_count = 1,
            $this->try = 0,
        ]);
    }

    public function resetCounters()
    {
        $this->sent_count = 0;
        $this->try = 0;
    }
}
