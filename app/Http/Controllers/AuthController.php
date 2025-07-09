<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ExpireRequest;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function showUsers()
    {
        $user = User::all();
        return response()->json([
            'status' => true,
            'message' => "Users Retrieved Successfully",
            'data' => $user
        ]);
    }

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
            return $this->responseSucceed($otp);
        }

        if ($otpCode->last_sent_at->diffInMinutes(now()) >= 2) {
            $otpCode->resetCounters();
        }

        $request->validateSentCount($otpCode);

        $request->validateLastSentAt($otpCode);

        $otpCode->storeNewOtpCode($otp);

        return $this->responseSucceed($otp);
    }

    public function register(RegisterRequest $request)
    {

        $mobile = $request->mobile;

        $verifyMobile = Otp::query()->where('mobile', $mobile)->first();

        if (!$verifyMobile) {
            return $this->responseFailed('کد شما نامعتبر است');
        }

        $data = User::query()->Create(
            $request->safe()->all() +
            [
                'birth_date' => $request->input('birth_date'),
                'father_name' => $request->input('father_name')
            ]
        );

        $verifyMobile->delete();

        return $this->responseSucceedCreate('', $data);
    }

    public function expiresTime(ExpireRequest $request)
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

        return $this->responseSucceedCreate('دریافت مجدد کد', otp: $otp);

    }

    private function responseSucceed($otp): JsonResponse
    {
        return $this->createResponse(true, 'create is successfully', $otp);
    }

    /**
     * @var string $message
     */

    private function responseSucceedCreate(string $message, $data = [], $otp = '')
    {
        return $this->createResponse(true, $message, $otp, $data);

    }

    private function createReponseSucceed($data)
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
