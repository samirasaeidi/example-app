<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AdminCntroller extends Controller
{
    public function index(CreateUserRequest $request)
    {
        $mobile = $request->input('mobile');
        $user = User::query()->where('mobile', $mobile)->first();

        if ($user) {
            return $this->responseFailed('The user has already been created.');
        }
        $data = User::query()->Create(
            $request->safe()->all() +
            [
                'birth_date' => $request->input('birth_date'),
                'father_name' => $request->input('father_name'),
            ]
        );
        return response()->json([
            'status' => true,
            'message' => 'User created successfully.',
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $user = User::query()->findOrFail($id);

        if (!$user) {
            return $this->responseFailed('User does not exist.');
        }
        return response()->json([
            'status' => true,
            'message' => 'User successfully found.',
            'data' => $user
        ]);

    }

    public function update(CreateUserRequest $request, $id)
    {
        $user = User::query()->find($id);
//        dd($user);

        if (!$user) {
            return $this->responseFailed('User does not exist.');
        }

        $user->update([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'mobile' => $request->input('mobile'),
            'password' => $request->input('password'),
            'national_code' => $request->input('national_code')
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User information successfully edited.',
            'data' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::query()->find($id);

        if (!$user) {
            return $this->responseFailed('User does not exist.');
        }

        return response()->json([
            'status' => true,
            'message' => 'User deletion was successful.',
            'data' => $user,
            $user->delete(),
        ]);
    }

    private function createResponse(bool $status, string $message, $user = [])
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $user
        ]);
    }

    private function responseFailed($message)
    {
        return $this->createResponse(false, $message);
    }
}
