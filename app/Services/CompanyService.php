<?php

namespace App\Services;

use App\Models\Company;
use App\Mail\VerificationMail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class CompanyService
{
    public function createCompany(array $data): Company
    {
        try {

            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                throw new Exception("Unauthenticated user.");
            }

            // Recruiter can create only one company
            if ($user->company) {
                throw new Exception("You already have a company.");
            }

            /*
            | Logo Upload
            */
            $logoPath = null;

            if (!empty($data['logo']) && $data['logo'] instanceof UploadedFile) {
                $logoPath = $data['logo']->store('logos', 'public');
            }

            /*
            | Verification Code
            */
            $verificationCode = rand(100000, 999999);

            /*
            | Create Company
            */
            $company = Company::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'website' => $data['website'] ?? null,
                'address' => $data['address'] ?? null,
                'logo' => $logoPath,
                'company_domain' => $data['company_domain'] ?? null,
                'verification_code' => $verificationCode,
                'verification_expiry' => now()->addMinutes(10),
                'is_verified' => false,
                'verification_status' => 'pending'
            ]);

            /*
            | Send Verification Email
            */
            Mail::to($user->email)->send(
                new VerificationMail($verificationCode)
            );

            return $company;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    public function verifyCompany($code)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $company = Company::where('user_id', $user->id)
            ->where('verification_code', $code)
            ->first();

        if (!$company) {
            throw new Exception("Invalid verification code.");
        }

        // ⭐ Expiry check
        if (now()->greaterThan($company->verification_expiry)) {
            throw new Exception("Verification code expired.");
        }

        // ⭐ Already verified check
        if ($company->is_verified) {
            return [
                'message' => 'Company already verified'
            ];
        }

        // ⭐ Update company verification status
        $company->update([
            'is_verified' => true,
            'verification_status' => 'approved'
        ]);

        return [
            'message' => 'Company verified successfully'
        ];
    }

    public function getCompany(): ?Company
    {
        $user = JWTAuth::parseToken()->authenticate();

        return $user?->company;
    }

    public function updateCompany(Company $company, array $data): Company
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$company) {
            throw new Exception("Company not found.");
        }

        if ($company->user_id !== $user->id) {
            throw new Exception("Unauthorized company update.");
        }

        if (!empty($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $data['logo'] = $data['logo']->store('logos', 'public');
        }

        $company->update($data);

        return $company;
    }
}
