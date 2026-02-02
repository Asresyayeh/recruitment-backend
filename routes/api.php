<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\JobController;



// ----------------------
// User Authentication Routes
// ----------------------

// Public routes
Route::post('register', [UserController::class, 'register']); // Register a new user
Route::post('login', [UserController::class, 'login']);       // Login user and get JWT token

// Protected  routes (require JWT token)
Route::middleware('auth:api')->group(function () {
    Route::get('me', [UserController::class, 'me']);       // Get current logged-in user
    Route::post('logout', [UserController::class, 'logout']); // Logout user (invalidate token)
});

Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{job}', [JobController::class, 'show']);

// Route::middleware('auth:api')->group(function () {
//     Route::post('/jobs', [JobController::class, 'store']);
//     Route::put('/jobs/{job}', [JobController::class, 'update']);
//     Route::delete('/jobs/{job}', [JobController::class, 'destroy']);
// });
Route::post('/jobs', [JobController::class, 'store']);

Route::get('/', function () {
    return response()->json(['message' => 'API is working']);
});

