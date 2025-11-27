<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\JobPosting;

use App\Mail\ApplicationApprovedMail;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;
use Smalot\PdfParser\Parser;

class ApplicationController extends Controller
{
    public function index()
    {
        $application = Application::with([
            'applicant',
            'job' => function ($query) {
                $query->withCount('applications');
            },
            'job.company'
            ])
            ->latest()
            ->get();
            // ->groupBy('job_id'); 

            // group by job id , filter by criteria, then update status

        // how to with count with applicant number which shown how much applicant apply with it?

        $specializations = collect(JobPosting::$specializations)
        ->map(function ($value, $key) {
            return [
                'value' => $key,
                'label' => $value,
            ];
        })
        ->values();

        return response()->json([
            'success' => true,
            'data' => $application,
            'specializations' => $specializations,
        ]);
    }

    public function filter(Request $request)
    {
        $application_ids = [];
        $is_specify_specialization = false;

        // read text from resume
        // $resume = $application->resume_path;

        $applications = Application::with('job', 'applicant', 'job.company')->get();

        $filtered = $applications->filter(function ($a) use ($request) {
            $job = $a->job;
            $applicant = $a->applicant;

            if ($request->check_salary) { // between the range salary_min & salary_max
                $expected = (int) ($applicant->expected_salary ?? 0);
                $minimum = (int) ($job->salary_min ?? 0);

                if ($expected > $minimum) {
                    return false;
                }
            }

            if ($request->check_experience) {
                $required = (int) ($job->required_experience_years ?? 0);
                $actual = (int) ($applicant->work_experience ?? 0);

                if ($actual < $required) {
                    return false;
                }
            }

            if ($request->check_specialization) {
                $jobSpec = strtolower(trim($job->specialization ?? ''));
                $appSpec = strtolower(trim($applicant->specialization ?? ''));

                // request->selected_specialization

                if ($jobSpec !== $appSpec) {
                    return false;
                }
            }

            return true;
        });

        return response()->json([
            'success' => true,
            'filterApplications' => $filtered->values(),
        ]);

        // $application = Application::with('job','applicant')->findOrFail(1);
        // $job = $application->job;
        // $applicant = $application->applicant;

        // $matches = true;

        // if ($job->salary_min && $applicant->expected_salary) {
        //     if ((int)$applicant->expected_salary < (int)$job->salary_min) {
        //         $matches = false;
        //     }
        // }

        // if ($job->required_experience_years && $applicant->work_experience) {
        //     if ((int)$applicant->work_experience < (int)$job->required_experience_years) {
        //         $matches = false;
        //     }
        // }

        // if ($job->specialization && $applicant->specialization) {
        //     if ($applicant->specialization != $job->specialization) {
        //         $matches = false;
        //     }
        // }

         // Update application status
        // $application->status = $matches ? 'matched' : 'rejected';
        // $application->save();

        // Optional: send notification or email
        // Notification::send($applicant->user, new ApplicationFiltered($application));



        // expected_salary
        // work_experience
        // specialization
        // $applicant = $application->applicant;

        // when request->criteria
        // where

        //

        // job criteria
        // applicant criteria

        // notification
        // email

        // $application->status = '';
        // $application->save();

    }

    public function updateStatus(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        
        if($application->status == 0){
            $application->status = Application::STATUS_APPROVED;
            $application->save();

            Mail::to($application->applicant->email)->send(new ApplicationApprovedMail($application));
        }

        return response()->json(['success' => true]);
    }

    public function delete(Application $application)
    {
        if(!Application::find($application)){
            abort(404);
        }

        $application->delete();

        return response()->json(['success' => true]);
    }

    public function updateSchedule(Request $request, $id)
    {
        $application = Application::findOrFail($id);

        $validated = $request->validate([
            'interview_mode'  => 'required|in:online,f2f',
            'interview_slots' => 'required|array|min:1|max:3', // up to 3 slots
            'interview_slots.*' => 'date' // each slot must be a valid date
        ]);

        $application->interview_mode = $validated['interview_mode'];

        $slots = collect($validated['interview_slots'])
        ->map(fn($slot) => Carbon::parse($slot)->setTimezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s'))
        ->toArray();

        $application->interview_slots = $slots;
        $application->save();

        return response()->json(['success' => true]);
    }

    public function confirmSchedule(Request $request, $id)
    {
        $application = Application::findOrFail($id);

        $validated = $request->validate([
            'confirmed_slot' => 'required'
        ]);

        // check status

        $application->confirmed_slot = Carbon::parse($request->confirmed_slot);
        $application->interview_status = Application::INTERVIEW_CONFIRMED;
        $application->save();

        return response()->json(['success' => true]);
    }
}
