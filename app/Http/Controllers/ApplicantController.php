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
use Illuminate\Support\Str;

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
            return response()->json(['success' => false, 'test' => 1]);
        }

        $user = Auth::user();
        $user->load('applicant');

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
            // 'user_role' => $request->role,
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
            // get user?
        ]);
    }

    public function updateProfile(Request $request) // Applicant $applicant
    {
        $applicant = Applicant::find(1); // default

        // Auth::user()->applicant();

        // add profile image for applicant ?
        $validated = $request->validate([
            'name' => 'required',
            'gender' => 'required|in:male,female',
            'contact_no' => 'nullable',
            'religion' => 'nullable',
            // 'email' => 'nullable',
            'expected_salary' => 'nullable',
            'work_experience' => 'nullable',
            'specialization' => 'nullable',
            'highest_qualification' => 'nullable',
            'day' => 'required|integer|min:1|max:31',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
        ]);

        if ($request->contact_no) {
            $contact = preg_replace('/\D/', '', $request->contact_no);

            if (Str::startsWith($contact, '0')) {
                $contact = '6' . $contact;
            } elseif (!Str::startsWith($contact, '6')) {
                $contact = '60' . $contact;
            }

            $validated['contact_no'] = $contact;
        }
             

        $birth_date = date('Y-m-d', strtotime("{$request->year}-{$request->month}-{$request->day}"));
        $validated['birth_date'] = $birth_date;

        $applicant->update($validated);
       
        return response()->json(['success' => true]);
    }

    // paginate?
    public function getAvailableJobs(Request $request)
    {
        $jobs = JobPosting::with('company','savedJobs')->where('status', 1)->latest()->get();

        $jobs->each(function ($job) {
            $job->created_at_human = $job->created_at->diffForHumans();
        });

        // $savedJobs = \App\Models\SavedJob::get();

        return response()->json([
            'jobs' => $jobs,
            // 'savedJobs' => $savedJobs,
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
        $job = JobPosting::with('company', 'savedJobs')->findOrFail($request->id);

        $job->created_at_human = $job->created_at->diffForHumans();

        return response()->json(['data' => $job]);
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
}
