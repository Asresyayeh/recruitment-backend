<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Job;


use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // Get all categories
        $categories = Category::all();

        return response()->json($categories);
    }


    public function jobs($categoryId)
    {
        $jobs = Job::where('category_id', $categoryId)->get();
        return response()->json($jobs);
    }
    //
}
