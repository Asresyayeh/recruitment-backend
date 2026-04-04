<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApplicationService;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    protected ApplicationService $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    /**
     * Submit a new application (User)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'cover_letter' => 'nullable|string',
            'resume_file' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($request->hasFile('resume_file') && $request->file('resume_file')->isValid()) {
            $data['resume_file'] = $request->file('resume_file');
        }

        $user = auth('api')->user();
        $existing = Application::where('user_id', $user->id)
            ->where('job_id', $data['job_id'])
            ->first();
        if ($existing) {
            return response()->json([
                'message' => 'You have already applied for this job'
            ], 400);
        }

        $application = $this->applicationService->createApplication($data);

        return response()->json([
            'message' => 'Application submitted successfully',
            'application' => $application
        ]);
    }

    /**
     * Get all applications for a specific job (Recruiter only)
     */
    public function applicationsByJob(Job $job)
    {
        $applications = $this->applicationService->getApplicationsByJob($job);

        return response()->json($applications);
    }

    /**
     * Update application status (Recruiter only)
     */
    public function updateStatus(Application $application, Request $request)
    {
        $data = $request->validate([
            'status' => 'required|string|in:pending,accepted,rejected'
        ]);

        $updated = $this->applicationService->updateApplicationStatus($application, $data['status']);

        return response()->json([
            'message' => 'Application status updated',
            'application' => $updated
        ]);
    }

   public function myApplications()
{
    $applications = $this->applicationService->getUserApplications();

    $applications = $applications->map(function($app) {
        $app->resume_url = $app->resume_path ? asset('storage/' . $app->resume_path) : null;
        return $app;
    });

    return response()->json($applications);
}
}
