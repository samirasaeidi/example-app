<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{


    public function showUsers()
    {
        $uers = User::all();

        return response()->json([
            'status' => true,
            'message' => "Users Retireved Successfully",
            'data' => $uers
        ]);
    }

    public function register(RegisterRequest $request)
    {

//        dd($request->input('parents.*.mobile'));

        dd($request->all());

//        return response()->json([]);
    }
}
