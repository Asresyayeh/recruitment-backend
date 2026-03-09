<?php

namespace App\Services;

use App\Models\Job;

class JobService
{

    public function getAllJobs()
    {
        return Job::with('company')
            ->latest()
            ->get();
    }


    public function getRecruiterJobs()
    {
        $user = auth('api')->user();

        if (!$user || !$user->company) {
            abort(403, 'Unauthorized');
        }

        return Job::with('company')
            ->where('company_id', $user->company->id)
            ->latest()
            ->get();
    }


    public function getJobById(Job $job)
    {
        return $job->load('company', 'applications');
    }

    /**
     * Create a new job (Recruiter only)
     */
    public function createJob(array $data)
    {
        $user = auth('api')->user();

        if (!$user || !$user->company) {
            abort(401, 'Unauthenticated');
        }

        $data['company_id'] = $user->company->id;

        return Job::create($data);
    }

    /**
     * Update a job
     */
    public function updateJob(Job $job, array $data)
    {
        $user = auth('api')->user();

        if (!$user || $job->company_id !== $user->company->id) {
            abort(403, 'Unauthorized');
        }

        $job->update($data);

        return $job;
    }

    /**
     * Delete a job
     */
    public function deleteJob(Job $job)
    {
        $user = auth('api')->user();

        if (!$user || $job->company_id !== $user->company->id) {
            abort(403, 'Unauthorized');
        }

        return $job->delete();
    }
    public function getJobsByCategory($category)
    {
        return Job::with('company')
            ->where('category', $category)
            ->latest()
            ->get();
    }
}
