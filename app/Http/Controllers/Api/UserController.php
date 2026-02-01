<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'sometimes|string|in:admin,recruiter,candidate',
        ]);

        $result = $this->userService->register($data);

        return response()->json($result, 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $result = $this->userService->login($credentials);

        if (!$result) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json($result);
    }

    public function me()
    {
        return response()->json($this->userService->me());
    }

    public function logout()
    {
        $this->userService->logout();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
