<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Exception;

class CompanyService
{
    /*
    |--------------------------------------------------------------------------
    | Create Company
    |--------------------------------------------------------------------------
    */
    public function createCompany(array $data): Company
    {
        $user = Auth::user();

        if (!$user) {
            throw new Exception("Unauthenticated user.");
        }

        // Recruiter can create only one company
        if ($user->company) {
            throw new Exception("You already have a company.");
        }

        /*
        |--------------------------------------------------------------------------
        | Logo Upload
        |--------------------------------------------------------------------------
        */
        $logoPath = null;

        if (!empty($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $logoPath = $data['logo']->store('logos', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Create Company
        |--------------------------------------------------------------------------
        */
        return Company::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'website' => $data['website'] ?? null,
            'address' => $data['address'] ?? null,
            'logo' => $logoPath,

            // Verification system (startup safe defaults)
            'company_domain' => $data['company_domain'] ?? null,
            'is_verified' => false,
            'verification_status' => 'pending'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Recruiter Company
    |--------------------------------------------------------------------------
    */
    public function getCompany(): ?Company
    {
        return Auth::user()?->company;
    }

    /*
    |--------------------------------------------------------------------------
    | Update Company
    |--------------------------------------------------------------------------
    */
    public function updateCompany(Company $company, array $data): Company
    {
        if (!$company) {
            throw new Exception("Company not found.");
        }

        if ($company->user_id !== Auth::id()) {
            throw new Exception("Unauthorized company update.");
        }

        /*
        |--------------------------------------------------------------------------
        | Logo Upload
        |--------------------------------------------------------------------------
        */
        if (!empty($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $data['logo'] = $data['logo']->store('logos', 'public');
        }

        $company->update($data);

        return $company;
    }
}
