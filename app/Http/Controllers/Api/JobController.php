<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\JobService;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    protected JobService $jobService;

    public function __construct(JobService $jobService)
    {
        $this->jobService = $jobService;
    }

    /**
     * Public job listing
     */
    public function index()
    {
        return $this->jobService->getAllJobs();
    }

    /**
     * Recruiter dashboard jobs
     */
    public function recruiterJobs()
    {
        return $this->jobService->getRecruiterJobs();
    }

    /**
     * Create job (Recruiter only)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'location' => 'required|string',
            'type' => 'required|string',
            'salary' => 'nullable|string',
            'category' => 'required|string',
        ]);

        return response()->json(
            $this->jobService->createJob($data),
            201
        );
    }

    /**
     * Show single job
     */
    public function show(Job $job)
    {
        return $this->jobService->getJobById($job);
    }

    /**
     * Update job (Ownership protected)
     */
    public function update(Request $request, Job $job)
    {
        $data = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'location' => 'sometimes|string',
            'type' => 'sometimes|string',
            'salary' => 'sometimes|nullable|string',
            'category' => 'sometimes|string|max:255',
        ]);
        return response()->json(
            $this->jobService->updateJob($job, $data)
        );
    }

    /**
     * Delete job (Ownership protected)
     */
    public function destroy(Job $job)
    {
        $this->jobService->deleteJob($job);

        return response()->json([
            'message' => 'Job deleted successfully'
        ]);
    }
    public function jobsByCategory($category)
    {
        return $this->jobService->getJobsByCategory($category);
    }
}
