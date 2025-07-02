<?php

namespace App\Http\Controllers;
use App\Models\Otp;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Models\Auth;

class UserController extends Controller
{

    public function auth(){

        $auth=Auth::all();

        return response()->json([
            'status'=>true,
            'message'=>'Auth retireved successfully',
            'data'=>$auth
        ]);

    }

    public function sendOtp(Request $request)
    {
        $otp = rand(1000, 9999);
        Otp::create([
            'mobile' => $request->mobile,
            'otp' => $otp,
        ]);
        return response()->json(['message' => 'OTP sent successfully']);
    }

    public function verifyOtp(Request $request){

        $request->validate([
            'mobile'=>'required|digits:11',
            'code'=>'required|digits:4',
        ]);

        $otpdata=OTP::where('mobile',$request->mobile)->
            where('otp',$request->otp)->
            first();

        if($otpdata){
            return respose()->json([
                'status'=>true,
                'message'=>'OTP verified successfully'
            ]);

        }else{
            return respose()->json([
                'status'=>false,
                'message'=>'invalide OTP or MobileRule'
            ]);
        }
    }

    public function register(Request $request){

        $request->validate([
            'mobile'=>'required|digits:11',
            'code'=>'required|digits:4',
        ]);

        $otpdata=OTP::where('mobile',$request->mobile)->
            where('otp',$request->otp)->
            first();

        if(!$otpdata){
            return response()->json([
                'status'=>false,
                'message'=>'invalide OTP Or MobileRule'
            ]);
        }

        $auth=Auth::create([
            'mobile'=>$request->mobile
        ]);

        return response()->json([
            'status'=>true,
            'message'=>'Create Your Account Successfully',
            'data'=>$auth
        ]);

    }

}
