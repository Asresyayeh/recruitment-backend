<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Services\CandidateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidateController extends Controller
{
    protected $candidateService;

    public function __construct(CandidateService $candidateService)
    {
        $this->candidateService = $candidateService;

        // Require authentication for all methods
        $this->middleware('auth');
    }

    // Show current user's candidate profile
    public function show()
    {
        $userId = Auth::id();
        $candidate = $this->candidateService->getByUserId($userId);

        if (!$candidate) {
            return response()->json(['message' => 'Candidate profile not found'], 404);
        }

        return response()->json($candidate);
    }

    // Create candidate profile
    public function store(Request $request)
    {
        $request->validate([
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'profile_photo' => 'nullable|image|max:2048',
            'skills' => 'nullable|string',
            'experience' => 'nullable|string',
            'education' => 'nullable|string',
        ]);

        $candidate = $this->candidateService->createCandidate($request->all(), Auth::id());

        return response()->json($candidate, 201);
    }

    // Update candidate profile
    public function update(Request $request, $id)
    {
        $candidate = Candidate::find($id);

        if (!$candidate || $candidate->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized or candidate not found'], 403);
        }

        $request->validate([
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'profile_photo' => 'nullable|image|max:2048',
            'skills' => 'nullable|string',
            'experience' => 'nullable|string',
            'education' => 'nullable|string',
        ]);

        $candidate = $this->candidateService->updateCandidate($candidate, $request->all());

        return response()->json($candidate);
    }

    // Delete candidate profile
    public function destroy($id)
    {
        $candidate = Candidate::find($id);

        if (!$candidate || $candidate->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized or candidate not found'], 403);
        }

        $this->candidateService->deleteCandidate($candidate);

        return response()->json(['message' => 'Candidate profile deleted successfully']);
    }
}
