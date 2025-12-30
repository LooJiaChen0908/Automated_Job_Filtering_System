<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Applicant;
use App\Models\User;
use App\Models\JobPosting;
use App\Models\Application;
use App\Models\SavedJob;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ApplicantController extends Controller
{
    public function register(Request $request)
    {
        if($request->role == 'admin'){
            return response()->json(['success' => false]);
        }
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'confirmedPassword' => 'required|same:password',
            'role' => 'required|in:user'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Invalid ID or Already register.'
            ], 422);
        }

        $user = User::create([
            'name' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash the password
            'role' => $request->role
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully!',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'username' => 'required', // username or email
            'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = [
            'name' => $request->username,
            'password' => $request->password
        ];

        if(!Auth::attempt($credentials)){
            return response()->json([
                'success' => false,
                'message' => 'Invalid username or password.'
            ], 401);
        }

        $user = Auth::user();
        $user->load('applicant');

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if($user){
            $user->currentAccessToken()->delete();
        }

        return response()->json(['success' => true]);
    }

    public function getProfile()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'applicant' => $user->applicant
        ]);
    }

    public function updateProfile(Request $request)
    {
        if (!$user = Auth::user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!$applicant = $user->applicant) {
            return response()->json(['message' => 'Applicant profile not found'], 404);
        }

        if ($request->filled('contact_no')) {
            $contact = preg_replace('/\D/', '', $request->contact_no);

            if (Str::startsWith($contact, '0')) {
                $contact = '6' . $contact;
            } elseif (!Str::startsWith($contact, '6')) {
                $contact = '60' . $contact;
            }

            $request->merge([
                'contact_no' => $contact
            ]);
        }

        $validated = $request->validate([
            'name' => 'required',
            'gender' => 'required|in:male,female',
            'contact_no' => [
                'required',
                'regex:/^601[0-9]{8,9}$/'
            ],
            'country' => "nullable",
            'religion' => 'nullable',
            // 'email' => 'nullable',
            'expected_salary' => 'nullable',
            'work_experience' => 'required',
            'specialization' => 'required',
            'highest_qualification' => 'nullable',
            'day' => 'nullable|integer|min:1|max:31',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'preferred_work_types' => 'nullable|array',
            'preferred_work_types.*' => 'in:full-time,part-time,temporary,internship', // optional validation
        ]);

        if($request->year && $request->month && $request->day){
            $birth_date = date('Y-m-d', strtotime("{$request->year}-{$request->month}-{$request->day}"));
            $validated['birth_date'] = $birth_date;
        }
        
        $validated['name'] = ucwords(strtolower($validated['name']));
        $applicant->update($validated);
       
        return response()->json(['success' => true]);
    }

    public function getAvailableJobs(Request $request)
    {
        if (!$user = Auth::user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!$applicant = $user->applicant) {
            return response()->json(['message' => 'Applicant profile not found'], 404);
        }

        $jobs = JobPosting::with('company')->where('status', 1)->latest()->get();

        $savedJobIds = SavedJob::where('applicant_id', $applicant->id)->pluck('job_id')->toArray();
        $appliedJobIds = Application::where('applicant_id', $applicant->id)->pluck('job_id')->toArray();

        $jobs->each(function ($job) use ($savedJobIds, $appliedJobIds) {
            $job->created_at_human = $job->created_at->diffForHumans();
            $job->is_saved = in_array($job->id, $savedJobIds);
            $job->is_applied = in_array($job->id, $appliedJobIds);
        });

        return response()->json([
            'jobs' => $jobs,
            'success' => true,
        ]);
    }

    public function searchJob(Request $request)
    {
        $jobs = JobPosting::with('company','savedJobs')
        ->where('status', 1)
        ->when($request->keyword, function($query) use ($request){
            $query->where('title', 'like', '%' . $request->keyword . '%')
            ->orWhere('description', 'like', '%' . $request->keyword . '%');
        })->when($request->employment_type, function ($query) use ($request){
            $query->where('employment_type', strtolower($request->employment_type));
        })->when($request->work_mode, function($query) use ($request){
            $query->where('work_mode', $request->work_mode);
        })
        ->orderBy('created_at','desc')
        ->get();

        $jobs->each(function ($job) {
            $job->created_at_human = $job->created_at->diffForHumans();
        });

        return response()->json([
            'jobs' => $jobs,
            'success' => true,
        ]);
    }

    public function getJob(Request $request)
    {
        if (!$user = Auth::user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!$applicant = $user->applicant) {
            return response()->json(['message' => 'Applicant profile not found'], 404);
        }

        $job = JobPosting::with([
            'company', 
            'savedJobs' => function ($query) use ($applicant) {
                $query->where('applicant_id', $applicant->id);
            }
        ])->findOrFail($request->id);

        $job->created_at_human = $job->created_at->diffForHumans();
        $job->is_saved = $job->savedJobs->isNotEmpty();

        $is_applied = Application::where('applicant_id', $applicant->id)->where('job_id', $job->id)->exists();

        return response()->json([
            'data' => $job,
            'is_applied' => $is_applied
        ]);
    }

    public function getSpecialization()
    {
        $specializations = collect(Applicant::$specializations)
        ->map(function ($value, $key) {
            return [
                'value' => $key,
                'label' => $value,
            ];
        })
        ->values();

        return response()->json([
            'data' => $specializations
        ]);
    }

    public function selectSlot(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user || !$user->applicant) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $application = Application::where('applicant_id', $user->applicant->id)->findOrFail($id);

        $application->selected_slot = Carbon::parse($request->selected_slot);
        $application->interview_status = Application::INTERVIEW_AWAITING_ADMIN;
        $application->save();

        return response()->json(['success' => true]);
    }

    public function suggestSlot(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user || !$user->applicant) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'suggested_slots' => 'required|array|min:1|max:3', // up to 3 slots
            'suggested_slots.*' => 'date' // each slot must be a valid date
        ]);

        $application = Application::where('applicant_id', $user->applicant->id)->findOrFail($id);

        $slots = collect($validated['suggested_slots'])
        ->map(fn($slot) => Carbon::parse($slot)->setTimezone('Asia/Kuala_Lumpur')->format('Y-m-d H:i:s'))
        ->toArray();

        $application->suggested_slots = $slots;
        $application->interview_status = Application::INTERVIEW_AWAITING_ADMIN;
        $application->save();

        return response()->json(['success' => true]);
    }
}
