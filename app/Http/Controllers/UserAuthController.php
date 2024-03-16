<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\StoreUserRequest;

class UserAuthController extends Controller
{
    use ApiResponseTrait;
    public function register(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($request->password);
        $user = User::create($data);
        $token = $user->createToken('API Token')->accessToken;
        return $this->apiResponse(new UserResource($user),$token,'registered successfully',200);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($data)) {
            return response(['error_message' => 'Incorrect Details.
            Please try again']);
        }
        $token = auth()->user()->createToken('API Token')->accessToken;
        $user = auth()->user();
        return $this->apiResponse(new UserResource($user),$token,'successfully login',200);

    }
}
