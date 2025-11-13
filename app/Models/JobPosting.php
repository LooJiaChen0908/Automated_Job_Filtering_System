<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPosting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'work_location',
        'work_mode',
        'employment_type',
        'required_experience_years',
        'specialization',
        'status',
        'company_id',
        'salary_min',
        'salary_max',
        'education_level',
    ];

    protected $appends = ['specialization_name'];

    public static $specializations = [
        'software-development' => 'Software Development',
        'web-development' => 'Web Development',
        'mobile-app-development' => 'Mobile App Development',
        'data-science' => 'Data Science',
        'cybersecurity' => 'Cybersecurity',
        'cloud-computing' => 'Cloud Computing',
        'ui-ux-design' => 'UI/UX Design',
        'network-engineering' => 'Network Engineering',
        'qa-testing' => 'QA & Testing',
        'accounting' => 'Accounting',
        'finance' => 'Finance',
        'business-analysis' => 'Business Analysis',
        'marketing' => 'Marketing',
        'sales' => 'Sales',
        'human-resources' => 'Human Resources',
        'graphic-design' => 'Graphic Design',
        'content-writing' => 'Content Writing',
        'social-media' => 'Social Media Marketing',
        'video-production' => 'Video Production',
        'animation' => 'Animation',
        'nursing' => 'Nursing',
        'pharmacy' => 'Pharmacy',
        'biotechnology' => 'Biotechnology',
        'public-health' => 'Public Health',
        'education' => 'Education',
        'logistics' => 'Logistics & Supply Chain',
        'legal' => 'Legal Services',
        'hospitality' => 'Hospitality & Tourism',
        'environmental-science' => 'Environmental Science',
        'translation' => 'Translation & Interpretation',
    ];

    public function applications()
    {
        return $this->hasMany(Application::class, 'job_id');
    }

    public function savedJobs() // class diagram need fix
    {
        return $this->hasMany(SavedJob::class, 'job_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function getSpecializationNameAttribute()
    {
        return self::$specializations[$this->specialization] ?? null;
    }
}
