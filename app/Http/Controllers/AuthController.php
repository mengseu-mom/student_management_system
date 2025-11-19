<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                "name" => "required|string",
                "email" => "required|email|unique:users,email",
                "password" => "required|string|min:6"
            ]);

            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => Hash::make($request->password)
            ]);

            if (!$user) {
                return response()->json(["error" => "User creation failed"], 500);
            }

            // Optional: Automatically login after registration
            $token = JWTAuth::fromUser($user);

            return response()->json([
                "message" => "User created successfully",
                "user" => $user,
                "token" => $token
            ], 201);

        } catch (\Exception $e) {
    return response()->json(['error' => $e->getMessage()], 500);
}

    }

    public function login(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required|string"
        ]);

        try {
            if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
                return response()->json(["error" => "Invalid credentials"], 401);
            }

            $user = JWTAuth::user();

            return response()->json([
                "message" => "Login successful",
                "token" => $token,
                "user" => $user
            ], 200);

        } catch (JWTException $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json(["error" => "Could not create token"], 500);
        }
    }

    //Optional logout method
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(["message" => "Logged out successfully"], 200);
        } catch (JWTException $e) {
            return response()->json(["error" => "Failed to logout"], 500);
        }
    }
}
