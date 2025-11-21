<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailOtp;

class AuthController extends Controller
{
   // Step 1: Send OTP to email
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email'
        ]);

        $otp = rand(100000, 999999);

        // Save OTP in table (no user created yet)
        EmailOtp::updateOrCreate(
            ['email' => $request->email], // You can store OTP by email instead of user_id
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(10)
            ]
        );

        Mail::raw("Your verification code is: $otp", function ($message) use ($request) {
            $message->to($request->email)
                ->subject('Email Verification Code');
        });

        return response()->json([
            'message' => 'OTP sent to email'
        ],200);
    }

    // Step 2: Verify OTP and Register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'otp' => 'required'
        ]);

        // Check OTP
        $otpRecord = EmailOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>=', now())
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'message' => 'Invalid or expired OTP'
            ], 400);
        }

        // Create user after OTP verified
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now()
        ]);

        // Delete OTP after verification
        $otpRecord->delete();

        // Optionally login user automatically
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User registered and email verified successfully',
            'user' => $user,
            'token' => $token
        ], 201);
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
