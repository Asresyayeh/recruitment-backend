<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserService
{
    /**
     * Register a new user
     */
    public function register(array $data)
    {
        // Create user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'candidate', 
        ]);

        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }



    public function login(array $credentials)
    {
        // Attempt to create a token
        if (!$token = JWTAuth::attempt($credentials)) {
            return null; // login failed
        }

        // Get the user based on email
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        $user = JWTAuth::user()->load('company');
        if ($user) {
            $user->load('company');
        }

        return [
            'user' => $user,
            'token' => $token,
        ];
    }



    /**
     * Get authenticated user
     */
    public function me()
    {
        return JWTAuth::parseToken()->authenticate();
    }

    /**
     * Logout user
     */
    public function logout()
    {
        JWTAuth::parseToken()->invalidate();
        return true;
    }
}
