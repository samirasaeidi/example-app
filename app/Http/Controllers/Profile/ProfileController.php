<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function showUser()
    {
        return response()->json(['user' => auth()->user()]);
    }
}
