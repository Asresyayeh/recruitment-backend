<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category; // make sure the Category model exists

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Design',
            'Marketing',
            'Development',
            'Finance',
            'HR',
            'Sales',
            'Support'
        ];

        foreach ($categories as $name) {
            Category::create(['name' => $name]);
        }
    }
}
