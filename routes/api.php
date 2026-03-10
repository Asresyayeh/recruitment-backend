<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CategoryController;




Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);



Route::get('/categories', [CategoryController::class, 'index']);

Route::get('/categories/{id}/jobs', [CategoryController::class, 'jobs']);

Route::middleware(['jwt.auth'])->group(function () {

    // User profile
    Route::get('me', [UserController::class, 'me']);
    Route::post('logout', [UserController::class, 'logout']);

    Route::post('/jobs', [JobController::class, 'store']);
    Route::put('/jobs/{job}', [JobController::class, 'update']);
    Route::delete('/jobs/{job}', [JobController::class, 'destroy']);
    Route::get('/recruiter/jobs', [JobController::class, 'recruiterJobs']);
    Route::get('/jobs/category/{category}', [JobController::class, 'jobsByCategory']);

    Route::post('companies', [CompanyController::class, 'store']);
    Route::get('companies', [CompanyController::class, 'show']);
    Route::put('companies', [CompanyController::class, 'update']);
    Route::post('companies/verify', [CompanyController::class, 'verify']);

    Route::get('/candidate', [CandidateController::class, 'show']);
    Route::post('/candidate', [CandidateController::class, 'store']);
    Route::put('/candidate/{id}', [CandidateController::class, 'update']);
    Route::delete('/candidate/{id}', [CandidateController::class, 'destroy']);
});



Route::get('/', function () {
    return response()->json(['message' => 'API is working']);
});
