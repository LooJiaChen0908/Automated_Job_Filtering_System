<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Applicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'birth_date',
        'religion',
        'gender',
        'email',
        'contact_no',
        'expected_salary',
        'work_experience',
        'specialization',
        'highest_qualification',
        'user_id',
    ];

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
    
    public function getSpecializationNameAttribute()
    {
        return self::$specializations[$this->specialization] ?? 'Unknown';
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'applicant_id');
    }
}
