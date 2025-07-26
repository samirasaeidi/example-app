<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ExpireRequest;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function sendOtp(RegisterRequest $request)
    {
        $mobile = $request->input('mobile');
        $otp = generate_otp_code();

        $user = User::where('mobile', $mobile)->fisrt();

        if ($user) {
            return $this->responseFailed('شماره کاربری وجود دارد');
        }

        $otpCode = Otp::query()->firstOrCreate([
            'mobile' => $mobile,
        ], [
            'mobile' => $mobile,
            'code' => $otp,
            'expires_at' => now()->addSeconds(60),
            'last_sent_at' => now(),
            'sent_count' => 1,
            'try' => 0
        ]);

        if ($otpCode->wasRecentlyCreated) {
            return $this->responseSuccess($otp);
        }

        if ($otpCode->last_sent_at->diffInMinutes(now()) >= 2) {
            $otpCode->resetCounters();
        }

        $request->validateSentCount($otpCode);

        $request->validateLastSentAt($otpCode);

        $otpCode->storeNewOtpCode($otp);

        return $this->responseSuccess($otp);
    }

    public function register(RegisterRequest $request)
    {
        $mobile = $request->mobile;

        $userMobile = User::query()->where('mobile', $mobile)->first();
        $verifyMobile = Otp::query()->where('mobile', $mobile)->first();
//        dd($verifyMobile);

        if ($userMobile) {
            return $this->responseFailed('شماره کاربری در سیستم وجود دارد');
        }

        if ($verifyMobile) {
            $verifyMobile->delete();
        }
        $otp = generate_otp_code();

        $data = User::query()->Create(
            $request->safe()->all() +
            [
                'birth_date' => $request->input('birth_date'),
                'father_name' => $request->input('father_name'),
            ],
        );
        $data->forceFill([
            'mobile_verified_at'=>now()
        ])->save();


        return $this->createResponse(true, 'عضویت با موفقیت انجام شد', $otp, $data);
    }

    public function resendCode(ExpireRequest $request)
    {
        $otp = generate_otp_code();
        $mobile = $request->input('mobile');
        $otpCode = Otp::query()->where('mobile', $mobile)->first();

        if (!$otpCode) {
            return $this->responseFailed('شماره موبایل نامعتبر است');
        }

        if ($otpCode->last_sent_at->diffInSeconds(now()) <= 60) {
            return $this->responseFailed('زمان انقضای کد تمام شد');
        }
        $otpCode->storeNewOtpCode($otp);

        return $this->responseSuccessCreate('دریافت مجدد کد', otp: $otp);
    }

    public function login(LoginRequest $request)
    {
        $mobile = $request->input('mobile');
        $otp = generate_otp_code();
        $user = User::query()->where('mobile', $mobile)->first();

        if (!$user) {
            return $this->responseFailed('user not found');
        }
        if ($user->mobile == $request->mobile) {

            $otpCode = Otp::query()->update(
                [
                    'mobile' => $mobile,
                    'code' => $otp,
                    'expires_at' => now()->addSeconds(60),
                    'last_sent_at' => now(),
                    'sent_count' => 1,
                    'try' => 0
                ]);

            if ($otpCode->wasRecentlyCreated) {
                return $this->responseSuccess($otp);
            }

            if ($otpCode->last_sent_at->diffInMinutes(now()) >= 2) {
                $otpCode->resetCounters();
            }

            $request->validateSentCount($otpCode);

            $request->validateLastSentAt($otpCode);

            $otpCode->storeNewOtpCode($otp);

            return $this->responseSuccess($otp);

        }
    }

    public function showUser($id)
    {
        $data = User::find($id);

        if(!$data){
            return $this->responseFailed('user information dose not exsist');
        }
        return $this->createResponse(true,'user information found successfully',data:$data);

    }

    private function responseSuccess($otp): JsonResponse
    {
        return $this->createResponse(true, 'create is successfully', $otp);
    }

    /**
     * @var string $message
     */
    private function responseSuccessCreate(string $message, $data = [], $otp = '')
    {
        return $this->createResponse(true, $message, $otp, $data);

    }

    private function createReponseSuccess($data)
    {
        return $this->createResponse(true, 'ثبت نام انجام شد', data: $data);

    }

    private function responseFailed($message): JsonResponse
    {
        return $this->createResponse(false, $message);
    }

    private function createResponse($status, $message, $otp = '', $data = []): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'otp' => $otp,
            'data' => $data
        ]);
    }
}


$user = User::find(1);
if ($user->status === UserStatus::Active) {
    // ...
}
