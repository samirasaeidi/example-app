<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
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

        $otpCode = Otp::query()->firstOrCreate([
            'mobile' => $mobile,
        ], [
            'mobile' => $mobile,
            'code' => $otp,
            'expires_at' => now()->addSeconds(10),
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
        $data = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile' => $request->mobile
        ]);

        return response()->json([
            'status' => true,
            'message' => 'successfully',
            'data' => $data
        ]);

    }

    private function responseSucceed($otp): JsonResponse
    {
        return $this->createResponse(true, 'create is successfully', $otp);
    }

    private function responseFailed($message): JsonResponse
    {
        return $this->createResponse(false, $message);
    }

    private function createResponse($status, $message, $otp = ''): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'otp' => $otp,
        ]);
    }
}
