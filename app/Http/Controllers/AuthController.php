<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\StoreUserRequest;

class AuthController extends Controller
{
    // using a trait for customising the response
    use ApiResponseTrait;

    public function register(StoreUserRequest $request){
        $user = $request->validated();
        $user = User::create([
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => Hash::make($user['password']),
        ]);

        $token = $user->createToken('authToken')->plainTextToken;
        return $this->apiResponse(new UserResource($user),$token,'registered successfully',200);
    }

    public function login(Request $request){
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }
        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('authToken')->plainTextToken;
        return $this->apiResponse(new UserResource($user),$token,'successfully login',200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
