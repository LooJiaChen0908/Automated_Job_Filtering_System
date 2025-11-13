<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationHistory extends Model
{
    protected $fillable = [
        'application_id',
        'applicant_id',
        'status',
        'comment',
        'changed_by',
        'changed_at'
    ];

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function applicant()
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }
}
