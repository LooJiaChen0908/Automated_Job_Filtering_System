<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;

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
            //  ->groupBy('job_id');

            // group by job id , filter by criteria, then update status


            // filter

        // how to with count with applicant number which shown how much applicant apply with it?

        return response()->json([
            'success' => true,
            'data' => $application,
        ]);
    }
}
