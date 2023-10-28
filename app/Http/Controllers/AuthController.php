<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOTP;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Helpers\ApiResponse;
use Laravel\Socialite\Facades\Socialite;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;

            $data = ['user' => $user, 'access_token' => $token];

            return ApiResponse::success($data, status: 200);
        }

        return ApiResponse::error(message: 'Account not found', status: 401);
    }

    public function register(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // Generate OTP
        $otp = mt_rand(100000, 999999);

        // Save user with OTP
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'otp' => $otp,
        ]);

        // Send OTP to user's email
        Mail::to($user->email)->send(new SendOTP($otp));

        $user->last_email_send_at = Carbon::now()->toDateTimeString();
        $user->otp_expired_at = Carbon::now()->addMinutes(5)->toDateTimeString();
        $user->save();


        return ApiResponse::success(message: 'OTP have been sent to your email', status: 200);
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ]);

        $user = User::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('otp_expired_at', '>=', Carbon::now())
            ->first();

        if (!$user) {
            return ApiResponse::error(message: 'OTP Expired / Invalid', status: 422);
        }

        $user->otp = null;
        $user->otp_expired_at = null;
        $user->email_verified_at = Carbon::now()->toDateTimeString();
        $user->save();

        // Authenticate the user using Sanctum
        $token = $user->createToken('authToken')->plainTextToken;

        $data = ['user' => $user, 'access_token' => $token];

        return ApiResponse::success($data, status: 200);
    }

    public function loginGoogle(Request $request)
    {
        // Validate the request from the frontend
        $request->validate([
            'google_token' => 'required',
        ]);

        $googleToken = $request->input('google_token');

        // Verify the Google token
        $user = Socialite::driver('google')->stateless()->userFromToken($googleToken);

        // Check if the user with this Google email already exists in your application
        $existingUser = User::where('email', $user->getEmail())->first();

        if ($existingUser) {
            // User exists, log them in using Sanctum
            $token = $existingUser->createToken('authToken')->plainTextToken;
            return response()->json(['message' => 'Login successful', 'token' => $token]);
        } else {
            // User doesn't exist, create a new user
            $newUser = new User();
            $newUser->name = $user->getName();
            $newUser->email = $user->getEmail();
            $newUser->save();

            // Log in the newly created user using Sanctum
            $token = $newUser->createToken('authToken')->plainTextToken;
            return response()->json(['message' => 'Registration and login successful', 'token' => $token]);
        }
    }

    public function resendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $email = $request->input('email');

        $user = User::where('email', $email)
            ->where('otp_expired_at', '>=', Carbon::now())
            ->first();

        if ($user) {
            // OTP exists and is not expired; resend the same OTP
            // You can choose to resend the same OTP code or generate a new one here
            // For example, to resend the same OTP:
            // Send the existing OTP code to the user's email/phone (not implemented here)
            $message = 'OTP resent successfully';
            return ApiResponse::success(message: $message, status: 200);
        } else {
            // OTP doesn't exist or has expired; generate a new OTP
            // Generate OTP
            $otp = mt_rand(100000, 999999);
            $user = User::where('email', $email)->first();

            $user->otp = $otp;
            $user->last_email_send_at = Carbon::now()->toDateTimeString();
            $user->otp_expired_at = Carbon::now()->addMinutes(5)->toDateTimeString();
            $user->save();

            // Send OTP to user's email
            Mail::to($user->email)->send(new SendOTP($otp));

            // Code to send the OTP to the user via email, SMS, etc. (not included here)
            // Implement the logic to send the OTP code to the provided email address
            $message = 'OTP resent successfully';
            return ApiResponse::success(message: $message, status: 200);
        }
    }
}
