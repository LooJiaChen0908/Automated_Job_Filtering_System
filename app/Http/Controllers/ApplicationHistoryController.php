<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApplicationHistory;

class ApplicationHistoryController extends Controller
{
    public function show()
    {
        $histories = ApplicationHistory::get();

        return response()->json($histories);
    }
}
