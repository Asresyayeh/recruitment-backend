<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\CandidateController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Register + Login
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Routes (JWT Auth)
|--------------------------------------------------------------------------
*/

Route::middleware(['jwt.auth'])->group(function () {

    // User profile
    Route::get('me', [UserController::class, 'me']);
    Route::post('logout', [UserController::class, 'logout']);

    // Jobs
    Route::post('/jobs', [JobController::class, 'store']);
    Route::put('/jobs/{job}', [JobController::class, 'update']);
    Route::delete('/jobs/{job}', [JobController::class, 'destroy']);

    // Company
    Route::post('companies', [CompanyController::class, 'store']);
    Route::get('companies', [CompanyController::class, 'show']);
    Route::put('companies', [CompanyController::class, 'update']);
    Route::post('companies/verify', [CompanyController::class, 'verify']);

    // Candidate
    Route::get('/candidate', [CandidateController::class, 'show']);
    Route::post('/candidate', [CandidateController::class, 'store']);
    Route::put('/candidate/{id}', [CandidateController::class, 'update']);
    Route::delete('/candidate/{id}', [CandidateController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Health Check
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return response()->json(['message' => 'API is working']);
});
