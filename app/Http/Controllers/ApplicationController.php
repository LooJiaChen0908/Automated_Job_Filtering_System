<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\JobPosting;
use Carbon\Carbon;
use App\Mail\ApplicationApprovedMail;
use Illuminate\Support\Facades\Mail;

use Smalot\PdfParser\Parser;
use App\Services\ResumeParserService;

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
        $is_specify_specialization = false;

        $applications = Application::with('job', 'applicant', 'job.company')->get();

        $filtered = $applications->filter(function ($a) use ($request) {
            $job = $a->job;
            $applicant = $a->applicant;

            $resumePath = storage_path('app/public/' . $a->resume_path);
            if (!file_exists($resumePath)) {
                return false; // skip if resume missing
            } 

            $parser = new ResumeParserService();
            $parsed = $parser->parse($resumePath);

            // Experience check (from resume parsing)
            if ($request->check_experience) {
                $required = (int) ($job->required_experience_years ?? 0);
                $actual   = (int) ($parsed['experience_years'] ?? 0);

                if ($actual < $required) {
                    return false;
                }
            }

            // Specialization check (from resume parsing)
            if ($request->check_specialization) {
                $jobSpec = strtolower(trim($job->specialization ?? ''));
                $resumeSpecs = array_map('strtolower', $parsed['specializations'] ?? []);

                if (!in_array($jobSpec, $resumeSpecs)) {
                    return false;
                }
            }

            // Education level check (from resume parsing)
            if ($request->check_education) {
               $requiredLevel = strtolower($job->education_level ?? 'none');
                $resumeLevel   = strtolower($parsed['education_level'] ?? 'none');

                // applicant must have >= required level
                if ($this->compareEducationLevel($resumeLevel, $requiredLevel) < 0) {
                    return false;
                }

            }

            // previous code

            if ($request->check_salary) { // between the range salary_min & salary_max
                $expected = (int) ($applicant->expected_salary ?? 0);
                $minimum = (int) ($job->salary_min ?? 0);

                if ($expected > $minimum) {
                    return false;
                }
            }

            // if ($request->check_experience) {
            //     $required = (int) ($job->required_experience_years ?? 0);
            //     $actual = (int) ($applicant->work_experience ?? 0);

            //     if ($actual < $required) {
            //         return false;
            //     }
            // }

            // if ($request->check_specialization) {
            //     $jobSpec = strtolower(trim($job->specialization ?? ''));
            //     $appSpec = strtolower(trim($applicant->specialization ?? ''));

            //     if ($jobSpec !== $appSpec) {
            //         return false;
            //     }
            // }

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

        // Optional: send notification or email
        // Notification::send($applicant->user, new ApplicationFiltered($application));
    }

    protected function compareEducationLevel(string $a, string $b): int
    {
        $order = ['none', 'spm', 'diploma', 'bachelor', 'master', 'phd'];

        $posA = array_search($a, $order);
        $posB = array_search($b, $order);

        return $posA <=> $posB; // returns -1, 0, or 1
    }

    public function updateStatus(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        
        if($application->status == 0){
            $application->status = Application::STATUS_MATCHED;
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
        // $application->status = Application::STATUS_SHORTLISTED;
        $application->save();

        return response()->json(['success' => true]);
    }

    public function reject(Request $request, $id)
    {
        $application = Application::findOrFail($id);

        if($application->status != 2){
            $application->status = Application::STATUS_REJECTED;
            $application->save();
        }

        return response()->json(['success' => true]);
    }

    public function getConfirmedInterview(Request $request)
    {
        $confirmed_interview = Application::with('applicant')
        ->whereNotNull('confirmed_slot')
        ->get()
        ->map(function($a) {
            $mode = $a->interview_mode == 'online' ? 'Online': 'Face-to-Face';

            return [
                'title' => "{$a->applicant->name} ({$mode}) +{$a->applicant->contact_no}",
                'start' => Carbon::make($a->confirmed_slot)?->toDateTimeString(),
            ];
        });

        return response()->json([
            'success' => true,
            'confirmed_interview' => $confirmed_interview,
        ]);
    }
}
