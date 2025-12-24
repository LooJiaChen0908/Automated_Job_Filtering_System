<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SavedJob;
use App\Models\JobPosting;

use Auth;

class SaveController extends Controller
{
    public function saveJob($id)
    {
        if (!$user = Auth::user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!$user->applicant) {
            return response()->json(['message' => 'Applicant profile not found'], 404);
        }

        $job = JobPosting::findOrFail($id);
      
        $job->savedJobs()->create([
            'applicant_id' => $user->applicant->id
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function unsaveJob($id)
    {
        if (!$user = Auth::user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!$user->applicant) {
            return response()->json(['message' => 'Applicant profile not found'], 404);
        }

        $job = SavedJob::where('id', $id)->where('applicant_id', $user->applicant->id)->first();
        
        if($job){
            $job->delete();

            return response()->json([
                'success' => true
            ]);
        }

        return response()->json([
            'success' => false
        ]);
    }
}
