<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    protected CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /*
    |--------------------------------------------------------------------------
    | Create Company
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $data = $request->validate([
            'name'    => 'required|string',
            'website' => 'nullable|string',
            'address' => 'required|string',
            'company_domain' => 'nullable|string',
            'logo'    => 'nullable|image|max:2048'
        ]);

        try {

            $company = $this->companyService->createCompany($data);

            return response()->json($company, 201);
        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Get Company
    |--------------------------------------------------------------------------
    */
    public function show()
    {
        $company = $this->companyService->getCompany();

        if (!$company) {
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }

        return response()->json($company);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Company
    |--------------------------------------------------------------------------
    */
    public function update(Request $request)
    {
        $company = $this->companyService->getCompany();

        if (!$company) {
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }

        $data = $request->validate([
            'name'    => 'sometimes|string',
            'website' => 'nullable|string',
            'address' => 'sometimes|string',
            'logo'    => 'nullable|image|max:2048'
        ]);

        try {

            $company = $this->companyService->updateCompany($company, $data);

            return response()->json($company);
        } catch (Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
