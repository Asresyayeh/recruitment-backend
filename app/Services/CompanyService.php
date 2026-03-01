<?php

namespace App\Services;

use App\Models\Company;
use App\Mail\VerificationMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;

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
    |-------------------------------------------------------------
    | Logo Upload
    |-------------------------------------------------------------
    */
        $logoPath = null;

        if (!empty($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $logoPath = $data['logo']->store('logos', 'public');
        }

        /*
    |-------------------------------------------------------------
    | Verification System ⭐
    |-------------------------------------------------------------
    */

        $verificationCode = rand(100000, 999999);

        /*
    |-------------------------------------------------------------
    | Create Company
    |-------------------------------------------------------------
    */

        $company = Company::create([
            'user_id' => $user->id,
            'name' => $data['name'],
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
    |-------------------------------------------------------------
    | Send Verification Email ⭐
    |-------------------------------------------------------------
    */

        if (!empty($data['email'])) {
            Mail::to($data['email'])->send(
                new VerificationMail($verificationCode)
            );
        }

        return $company;
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
