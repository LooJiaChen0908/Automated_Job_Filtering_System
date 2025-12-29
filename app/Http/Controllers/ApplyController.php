<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobPosting;
use App\Models\SavedJob;
use App\Models\Application;
use Auth;

class ApplyController extends Controller
{
    public function applyJob(Request $request)
    {
        if (!$user = Auth::user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!$user->applicant) {
            return response()->json(['message' => 'Applicant profile not found'], 404);
        }

        // $request->validate([
        //     'job_id' => 'required|exists:job_postings,id',
        //     'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
        //     'cover_letter' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        // ]);

        $applicantId = $user->applicant->id; 

        $job = JobPosting::findOrFail($request->job_id);

        if ($job->applications()->where('applicant_id', $applicantId)->exists()) {
            return response()->json(['message' => 'You have already applied for this job'], 409);
        }

        $resumePath = null;
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'public');
        }

        $job->applications()->create([
            'applicant_id' => $applicantId,
            'resume_path' => $resumePath,
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function getAppliedJob(Request $request)
    {
        if (!$user = Auth::user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!$user->applicant) {
            return response()->json(['message' => 'Applicant profile not found'], 404);
        }

        $applicant_id = $user->applicant->id; 
        $tab = $request->tab;
        $appliedJobs = [];
        $savedJobs = [];
        $countSavedJobs = 0;
        $countAppliedJobs = 0;

        if ($tab == 'applied') {
            $appliedJobs = Application::with('job','job.company')->where('applicant_id', $applicant_id)->latest()->get();

            $countAppliedJobs = $appliedJobs->count();

        } else {
            $savedJobs = SavedJob::with('job','job.company')->where('applicant_id', $applicant_id)->latest()->get();

            $appliedJobIds = Application::where('applicant_id', $applicant_id)->pluck('job_id')->toArray();

            $savedJobs->each(function ($savedJob) use ($appliedJobIds) {
                $savedJob->job_created_at_human = $savedJob->job->created_at->diffForHumans();
                $savedJob->is_applied = in_array($savedJob->job_id, $appliedJobIds);
            });
            
            $countSavedJobs = $savedJobs->count();
        }

        return response()->json([
            'success' => true,
            'savedJobs' => $savedJobs,
            'appliedJobs' => $appliedJobs,
            'countSavedJobs' => $countSavedJobs,
            'countAppliedJobs' => $countAppliedJobs,
        ]);
    }
}
