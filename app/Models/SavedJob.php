<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedJob extends Model
{
    protected $fillable = [
        'job_id',
        'applicant_id',
        'notes',
    ];

    public function job()
    {
        return $this->belongsTo(JobPosting::class, 'job_id');
    }
}
