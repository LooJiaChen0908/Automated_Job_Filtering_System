<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ZoomMeeting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'application_id', 
        'uuid', 
        'zoom_meeting_id', 
        'host_id', 
        'host_email',
        'topic', 
        'type', 
        'status', 
        'start_time', 
        'duration', 
        'timezone',
        'join_url', 
        'start_url', 
        'password', 
        'settings'
    ];

    public function application() {
        return $this->belongsTo(Application::class);
    }
}
