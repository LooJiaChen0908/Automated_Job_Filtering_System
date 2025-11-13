<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobPosting;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\ApplicationHistory;
use App\Models\Shortlisted;

class TestController extends Controller
{
    public function test()
    {
        $groupedApplications = Application::with('applicant', 'job')
        ->get()
        ->groupBy('job_id');

        return response()->json([
            'groupedApplications' => $groupedApplications
        ]);

      $specializations = collect(Applicant::$specializations)
        ->map(function ($value, $key) {
            return [
                'value' => $key,
                'label' => $value,
            ];
        })
        ->values();

        return response()->json([
            'specializations' => $specializations
        ]);

        return response()->json(['success' => true, 'name' => 'loo']);
    }
}
