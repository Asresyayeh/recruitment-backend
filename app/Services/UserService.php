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
            'role' => $data['role'] ?? 'candidate', // default role
        ]);

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Login a user
     */
    public function login(array $credentials)
    {
        // Attempt to login and create a token
        if (!$token = JWTAuth::attempt($credentials)) {
            return null; // login failed
        }

        // Get authenticated user from token
        $user = JWTAuth::parseToken()->authenticate();

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
 