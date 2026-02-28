<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Company extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Fields
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'user_id',
        'name',
        'website',
        'address',
        'logo',

        // Verification System
        'company_domain',
        'verification_code',
        'verification_expiry',
        'is_verified',
        'verification_status'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Company owner (recruiter)
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Company jobs
    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Verification Helpers
    |--------------------------------------------------------------------------
    */

    // Check if company is verified
    public function isVerified(): bool
    {
        return $this->is_verified === true;
    }

    // Check verification code validity
    public function isVerificationCodeValid($code): bool
    {
        if (!$this->verification_code) {
            return false;
        }

        if ($this->verification_expiry && Carbon::now()->greaterThan($this->verification_expiry)) {
            return false;
        }

        return $this->verification_code == $code;
    }

    // Generate verification code
    public static function generateVerificationCode(): string
    {
        return (string) random_int(100000, 999999);
    }
}
