<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    // Which fields can be mass assigned
    protected $fillable = [
        'company_id',
        'title',
        'description',
        'location',
        'type',
        'salary',
        'category',
    ];

    // Job belongs to a company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Job has many applications
    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
