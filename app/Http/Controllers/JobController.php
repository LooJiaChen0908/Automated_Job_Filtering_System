<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobPosting;
use App\Models\Company;

class JobController extends Controller
{
    public function index()
    {
        $jobs = JobPosting::with('company')->latest()->get();

        return response()->json([
            'data' => $jobs,
            'success' => true
        ]);
    }

    public function getCompanyName(Request $request)
    {
        $companies = Company::get();

        $specializations = collect(JobPosting::$specializations)
        ->map(function ($value, $key) {
            return [
                'value' => $key,
                'label' => $value,
            ];
        })
        ->values();

        return response()->json([
            'companies' => $companies,
            'success' => true,
            'specializations' => $specializations,
        ]);
    }

    public function getJob(Request $request)
    {
        $job = JobPosting::with('company')->findOrFail($request->id);
        $companies = Company::get();

        return response()->json([
            'job' => $job,
            'companies' => $companies,
        ]);
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'salary_min' => 'nullable',
            'salary_max' => 'nullable',
            'work_location' => 'nullable',
            'work_mode' => 'nullable',
            'employment_type' => 'nullable',
            'required_experience_years' => 'nullable',
            'specialization' => 'nullable',
            'company_id' => 'required',
            'education_level' => 'nullable|in:none,spm,diploma,bachelor,master,phd',
        ]);

        $validated['status'] = 1;
        $validated['title'] = ucwords(strtolower($validated['title']));

        JobPosting::create($validated);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $job = JobPosting::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable',
            'salary_min' => 'nullable', // min = 1
            'salary_max' => 'nullable', // greater than min , min = 1
            'work_location' => 'nullable',
            'work_mode' => 'nullable',
            'employment_type' => 'nullable',
            'required_experience_years' => 'nullable',
            'specialization' => 'nullable',
            'company_id' => 'required',
            'status' => 'nullable',
            'education_level' => 'nullable|in:none,spm,diploma,bachelor,master,phd',
        ]);

        $validated['title'] = ucwords(strtolower($validated['title']));
        $job->update($validated);

        return response()->json(['success' => true]);
    }

    public function changeStatus(JobPosting $job)
    {   
        $job->status = !$job->status;
        $job->save();

        return response()->json(['success' => true]);
    }

    public function delete(JobPosting $job){
        if(!JobPosting::find($job)){
            abort(404);
        }

        $job->delete();

        return response()->json(['success' => true]);
    }

    public function search(Request $request)
    {
        $jobs = JobPosting::with('company')
        ->when($request->title, function($query) use ($request){
            $query->where('title', 'like', '%' . $request->title . '%');
        })->when($request->employment_type, function ($query) use ($request){
            $query->where('employment_type', strtolower($request->employment_type));
        })->when($request->work_mode, function($query) use ($request){
            $query->where('work_mode', $request->work_mode);
        })->when($request->salary_min && $request->salary_max, function ($query) use ($request) {
            $query->where('salary_max', '>=', $request->salary_min)
                ->where('salary_min', '<=', $request->salary_max);
        })
        ->orderBy('created_at','desc')
        ->get();

        return response()->json([
            'success' => true,
            'jobs' => $jobs,
        ]);
    }
}
