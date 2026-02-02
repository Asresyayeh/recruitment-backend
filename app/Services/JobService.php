<?php

namespace App\Services;

use App\Models\Job;
use Illuminate\Support\Facades\Auth;

class JobService
{
    /**
     * Get all jobs with company data
     */
    public function getAllJobs()
    {
        return Job::with('company')
            ->latest()
            ->get();
    }

    /**
     * Get single job with relations
     */
    public function getJobById(Job $job)
    {
        return $job->load('company', 'applications');
    }

    /**
     * Create a new job (Recruiter only)
     */
    public function createJob(array $data)
    {
        // Attach company automatically (logged-in recruiter)
        $data['company_id'] = Auth::user()->company->id;

        return Job::create($data);
    }

    /**
     * Update a job
     */
    public function updateJob(Job $job, array $data)
    {
        $job->update($data);
        return $job;
    }

    /**
     * Delete a job
     */
    public function deleteJob(Job $job)
    {
        return $job->delete();
    }
}
