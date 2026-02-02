<?php


namespace App\Http\Controllers\Api;  // 🔴 THIS LINE IS REQUIRED
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

    public function index()
    {
        return $this->jobService->getAllJobs();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'location' => 'required|string',
            'type' => 'required|string',
            'salary' => 'nullable|string',
        ]);

        return $this->jobService->createJob($data);
    }

    public function show(Job $job)
    {
        return $this->jobService->getJobById($job);
    }

    public function update(Request $request, Job $job)
    {
        $data = $request->validate([
            'title' => 'string',
            'description' => 'string',
            'location' => 'string',
            'type' => 'string',
            'salary' => 'nullable|string',
        ]);

        return $this->jobService->updateJob($job, $data);
    }

    public function destroy(Job $job)
    {
        $this->jobService->deleteJob($job);
        return response()->json(['message' => 'Job deleted']);
    }
}
