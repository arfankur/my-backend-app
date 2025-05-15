<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token', ['*'], now()->addDays(30))->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user,
            'token' => $token
        ], 201)->cookie('auth_token', $token, 60 * 24 * 30); // 30 days
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember_me' => 'boolean'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        
        // Delete existing tokens if any
        $user->tokens()->delete();
        
        // Create new token with expiration
        $expiration = $request->remember_me ? now()->addDays(30) : now()->addHours(24);
        $token = $user->createToken('auth_token', ['*'], $expiration)->plainTextToken;

        $response = response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);

        // Set cookie if remember_me is true
        if ($request->remember_me) {
            $response->cookie('auth_token', $token, 60 * 24 * 30); // 30 days
        }

        return $response;
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ])->cookie(Cookie::forget('auth_token'));
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function checkAuth(Request $request)
    {
        if ($request->user()) {
            return response()->json([
                'authenticated' => true,
                'user' => $request->user()
            ]);
        }

        return response()->json([
            'authenticated' => false
        ], 401);
    }
}