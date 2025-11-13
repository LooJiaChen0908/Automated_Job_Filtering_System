<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'applicant_id',
        'job_id',
        'status',
        'resume_path',
        'applied_at',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }

    public function job()
    {
        return $this->belongsTo(JobPosting::class, 'job_id');
    }

    public function histories()
    {
        return $this->hasMany(ApplicationHistory::class, 'application_id');
    }

    public function shortlisted()
    {
        return $this->hasMany(Shortlisted::class, 'application_id');
    }

    // pending approved reject 0 1 -1
}
