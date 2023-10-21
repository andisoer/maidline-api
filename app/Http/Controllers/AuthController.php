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

        return ApiResponse::error(message: 'Akun tidak ditemukan', status: 401);
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
        $otp = mt_rand(1000, 9999);

        // Save user with OTP
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'otp' => $otp,
        ]);

        // Send OTP to user's email
        Mail::to($user->email)->send(new SendOTP($otp));


        return ApiResponse::success(message: 'Kode OTP berhasil dikirim', status: 200);
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ]);

        $user = User::where('email', $request->email)->where('otp', $request->otp)->first();

        if (!$user) {
            return ApiResponse::error(message: 'Kode OTP Invalid', status: 422);
        }

        $user->otp = null;
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
}
