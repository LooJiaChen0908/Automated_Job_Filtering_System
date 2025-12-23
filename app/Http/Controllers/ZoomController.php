<?php

namespace App\Http\Controllers;

use App\Services\ZoomService;
use Illuminate\Http\Request;
use App\Models\ZoomMeeting;
use App\Models\Application;
use Carbon\Carbon;

class ZoomController extends Controller
{
    public function create(Request $request, ZoomService $zoom)
    {
        $validated = $request->validate([
            'topic' => 'required|string|max:255',
            'application_id' => 'required|exists:applications,id',
        ]);

        $application = Application::findOrFail($request->application_id);

        try {
            $response = $zoom->createMeeting([
                'type'       => 2,
                'duration'   => 60,
                'timezone'   => 'Asia/Kuala_Lumpur',
                'topic'      => $validated['topic'],
                'start_time' => Carbon::parse($application->confirmed_slot)->format('Y-m-d\TH:i:s'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create Zoom meeting',
                'error'   => $e->getMessage(),
            ], 500);
        }

        $data = $response->json();

        ZoomMeeting::create([
            'application_id' => $application->id,
            'uuid' => $data['uuid'],
            'zoom_meeting_id' => $data['id'],
            'host_id' => $data['host_id'],
            'host_email' => $data['host_email'],
            'topic'=> $data['topic'],
            'type' => $data['type'],
            'status' => $data['status'],
            'start_time' => Carbon::parse($data['start_time']),
            'duration' => $data['duration'],
            'timezone' => $data['timezone'],
            'join_url' => $data['join_url'],
            'start_url' => $data['start_url'],
            'password' => $data['password'] ?? null,
            'setting' => isset($data['settings']) ? json_encode($data['settings']) : null,
        ]);
     
        return response()->json([
            'success' => true,
            // 'meeting_id' => $data['id'],
            // 'topic'      => $data['topic'],
            // 'start_time' => $data['start_time'],
            // 'join_url'   => $data['join_url'],
        ], 201);
    }

    public function manualUpdate(ZoomService $zoom)
    {
        $meetings = ZoomMeeting::all();

        $errors = [];

        foreach ($meetings as $meeting) {
            try {
                $response = $zoom->getMeeting($meeting->zoom_meeting_id);
                $data = $response->json();

                $meeting->update([
                    'status' => $data['status'],
                ]);
               
            } catch (\Exception $e) {
               $errors[] = [
                    'meeting_id' => $meeting->zoom_meeting_id,
                    'error' => $e->getMessage(),
                ];
                \Log::error("Zoom update failed", ['meeting_id' => $meeting->zoom_meeting_id, 'error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'success' => true,
            'errors' => $errors,
        ]);
    }
}