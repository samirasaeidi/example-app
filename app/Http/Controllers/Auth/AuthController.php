<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ExpireRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Models\Otp;
use App\Models\User;
use App\Traits\OtpValidations;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use OtpValidations;

    /**
     * @throws ValidationException
     */
    public function sendOtp(SendOtpRequest $request)
    {
        $mobile = $request->input('mobile');
        $otp = generate_otp_code();

        $user = User::query()->where('mobile', $mobile)->first();

        if ($user) {
            return $this->responseFailed('the mobile number exists');
        }

        $otpCode = (new Otp)->updateOrCreateOtp($mobile, $otp, $password = null);

        if ($otpCode->wasRecentlyCreated) {
            return $this->responseSuccess();
        }

        if ($otpCode->last_sent_at->diffInMinutes(now()) >= 10) {
            $otpCode->resetCounters();
        }

        $this->validateSentCount($otpCode);

        $this->validateLastSentAt($otpCode);

        $otpCode->storeNewOtpCode($otp);

        return $this->responseSuccess(otp: $otp);
    }

    public function register(RegisterRequest $request)
    {
        $mobile = $request->mobile;

        $userMobile = User::query()->where('mobile', $mobile)->first();
        $verifyMobile = Otp::query()->where('mobile', $mobile)->first();

        if ($userMobile) {
            return $this->responseFailed('the mobile number exists');
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
            'mobile_verified_at' => now(),
        ])->save();

        UserRegistered::dispatch();

        $token = JWTAuth::fromUser($data);

        return $this->createResponse(true, 'register was successfully', $otp, $data, $token);
    }

    public function resendOtp(ExpireRequest $request)
    {
        $otp = generate_otp_code();
        $mobile = $request->input('mobile');
        $otpCode = Otp::query()->where('mobile', $mobile)->first();

        if (! $otpCode) {
            return $this->responseFailed('the mobile number is invalid');
        }

        if ($otpCode->last_sent_at->diffInSeconds(now()) <= 60) {
            return $this->responseFailed('code has expired');
        }
        $otpCode->storeNewOtpCode($otp);

        return $this->responseSuccessCreate('resend code', otp: $otp);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['mobile', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return $this->responseFailed('Unauthorized');
        }

        $mobile = $request->input('mobile');
        $password = $request->input('password');
        $otp = generate_otp_code();

        $otpCode = (new Otp)->updateOrCreateOtp($mobile, $otp, $password);

        if ($otpCode->wasRecentlyCreated) {
            return $this->responseSuccess(otp: $otp);
        }

        if ($otpCode->last_sent_at->diffInMinutes(now()) >= 10) {
            $otpCode->resetCounters();
        }

        $this->validateSentCount($otpCode);

        $this->validateLastSentAt($otpCode);

        $otpCode->storeNewOtpCode($otp);

        return $this->responseSuccess(otp: $otp, token: $token);

    }

    private function responseSuccess($otp = '', $token = ''): JsonResponse
    {
        return $this->createResponse(true, 'create is successfully', $otp, $token);
    }

    /**
     * @var string
     */
    private function responseSuccessCreate(string $message, $data = [], $otp = '')
    {
        return $this->createResponse(true, $message, $otp, $data);

    }

    private function responseFailed($message): JsonResponse
    {
        return $this->createResponse(false, $message);
    }

    private function createResponse(bool $status, string $message, $otp = '', $data = [], $token = ''): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'otp' => $otp,
            'data' => $data,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => (auth()->factory()->getTTL() * 60) + time(),

        ]);
    }
}
