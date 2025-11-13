<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\JobPosting;
use App\Models\Application;

use DB;

class AdminController extends Controller
{
    public function register(Request $request)
    {
        if($request->role != 'admin'){
            return response()->json(['success' => false]);
        }
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'confirmedPassword' => 'required|same:password',
            'role' => 'required|in:admin'
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
            // 'user' => $user, // Optionally return user info
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

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            // 'user_role' => $request->role,
            // 'user' => $user
        ]);
    }

    public function logout()
    {
        $user = Auth::user();

        if($user){
            $user->currentAccessToken()->delete();
        }

        return response()->json(['success' => true]);
    }

    public function getDashboardData(Request $request)
    {
        $count_company = Company::count();
    
        $count_job = JobPosting::count();
        $active_job = JobPosting::where('status', 1)->count();
        $inactive_job = JobPosting::where('status', 0)->count();

        $count_application = Application::count();

        // $statusCounts = JobPosting::select('status', DB::raw('count(*) as total'))
        // ->groupBy('status')
        // ->pluck('total', 'status');

        return response()->json([
            'success' => true,
            'count_company' => $count_company,
            'count_job' => $count_job,
            'active_job' => $active_job,
            'inactive_job' => $inactive_job,
            'count_application' => $count_application,
            // 'status_counts' => $statusCounts
        ]);
    }
}
