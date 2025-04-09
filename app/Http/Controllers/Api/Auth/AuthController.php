<?php

namespace App\Http\Controllers\Api\Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\ApiResponseTrait;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(), 422, 'Validation Error');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->apiResponse($user, 201, 'User registered successfully!');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(), 422, 'Validation Error');
        }

        if (auth()->attempt($request->only('email', 'password'))) {
            $user = auth()->user();
            $token = $user->createToken('API Token')->plainTextToken;
            $user->token = $token;

            return $this->apiResponse($user, 200, 'Login successful!');
        }

        return $this->apiResponse(null, 401, 'Unauthorized');
    }

    public function profile(Request $request)
    {
        return $this->apiResponse(auth()->user(), 200, 'User retrieved successfully!');
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return $this->apiResponse(null, 200, 'Logged out successfully');
    }
}
