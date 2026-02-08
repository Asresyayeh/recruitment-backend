<?php

namespace App\Services;

use App\Models\Candidate;
use Illuminate\Support\Facades\Storage;

class CandidateService
{
    /**
     * Create a new candidate
     */
    public function createCandidate(array $data, $userId): Candidate
    {
        // Handle file uploads
        if (isset($data['resume'])) {
            $data['resume_path'] = $data['resume']->store('resumes', 'public');
        }

        if (isset($data['profile_photo'])) {
            $data['profile_photo'] = $data['profile_photo']->store('profile_photos', 'public');
        }

        $candidate = Candidate::create([
            'user_id' => $userId,
            'resume_path' => $data['resume_path'] ?? null,
            'skills' => $data['skills'] ?? null,
            'experience' => $data['experience'] ?? null,
            'education' => $data['education'] ?? null,
            'profile_photo' => $data['profile_photo'] ?? null,
        ]);

        return $candidate;
    }

    /**
     * Update an existing candidate
     */
    public function updateCandidate(Candidate $candidate, array $data): Candidate
    {
        // Update file uploads
        if (isset($data['resume'])) {
            if ($candidate->resume_path) {
                Storage::disk('public')->delete($candidate->resume_path);
            }
            $data['resume_path'] = $data['resume']->store('resumes', 'public');
        }

        if (isset($data['profile_photo'])) {
            if ($candidate->profile_photo) {
                Storage::disk('public')->delete($candidate->profile_photo);
            }
            $data['profile_photo'] = $data['profile_photo']->store('profile_photos', 'public');
        }

        $candidate->update([
            'resume_path' => $data['resume_path'] ?? $candidate->resume_path,
            'skills' => $data['skills'] ?? $candidate->skills,
            'experience' => $data['experience'] ?? $candidate->experience,
            'education' => $data['education'] ?? $candidate->education,
            'profile_photo' => $data['profile_photo'] ?? $candidate->profile_photo,
        ]);

        return $candidate;
    }

    /**
     * Get candidate by user
     */
    public function getByUserId($userId): ?Candidate
    {
        return Candidate::where('user_id', $userId)->first();
    }

    /**
     * Delete a candidate
     */
    public function deleteCandidate(Candidate $candidate): bool
    {
        // Delete files
        if ($candidate->resume_path) {
            Storage::disk('public')->delete($candidate->resume_path);
        }

        if ($candidate->profile_photo) {
            Storage::disk('public')->delete($candidate->profile_photo);
        }

        return $candidate->delete();
    }
}

