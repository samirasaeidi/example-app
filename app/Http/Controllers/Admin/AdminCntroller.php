<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\IndexUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;
class AdminCntroller extends Controller
{


    public function index(IndexUserRequest $request)
    {
        $perPage=$request->input('per_page',12);
        $user = Otp::query()->paginate($perPage);
        return $this->createResponse(true, 'Users found successfully', $user);
    }

    public function show($id)
    {
        $user = User::query()->findOrFail($id);
        if (!$user) {
            return $this->responseFailed('User does not exist.');
        }
        return $this->createResponse(true, 'User found successfully.', $user);

    }

    public function store(CreateUserRequest $request)
    {
        $mobile = $request->input('mobile');
        $user = User::query()->where('mobile', $mobile)->first();

        if ($user) {
            return $this->responseFailed('The user has already been created.');
        }
        $user = User::query()->Create(
            $request->safe()->all() +
            [
                'birth_date' => $request->input('birth_date'),
                'father_name' => $request->input('father_name'),
            ]
        );
        return $this->createResponse(true, 'User created successfully.', $user);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::query()->find($id);
//        dd($user);
        if (!$user) {
            return $this->responseFailed('User does not exist.');
        }

        $user->updateUser($request);

        return $this->createResponse(true, 'User information edited successfully.', $user);
    }

    public function destroy($id)
    {
        $user = User::query()->find($id);
        if (!$user) {
            return $this->responseFailed('User does not exist.');
        }
        $user->delete();
        return $this->createResponse(true, 'User deleted successful.');
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

