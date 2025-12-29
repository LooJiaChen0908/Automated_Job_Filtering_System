<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ZoomService
{   
    protected $baseUrl = "https://api.zoom.us/v2";

    public function getAccessToken()
    {
        $response = Http::asForm()
            ->withBasicAuth(
                config('services.zoom.client_id'),
                config('services.zoom.client_secret')
            )
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => config('services.zoom.account_id'),
            ]);

        if ($response->failed()) {
            throw new \Exception('Failed to get Zoom access token: ' . $response->body());
        }

        return $response['access_token'];
    }

    public function createMeeting($data)
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
        ->post("{$this->baseUrl}/users/me/meetings", [
            'topic' => $data['topic'],
            'type' => $data['type'] ?? 2, // default to scheduled meeting
            'start_time' => $data['start_time'],
            'duration' => $data['duration'] ?? 60,
            'timezone' => $data['timezone'] ?? 'Asia/Kuala_Lumpur',
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to create Zoom meeting: ' . $response->body());
        }

        return $response;
    }

    public function getMeeting($meetingId)
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)->get("{$this->baseUrl}/meetings/{$meetingId}");

        if ($response->failed()) {
            throw new \Exception('Failed to fetch Zoom meeting: ' . $response->body());
        }

        return $response;
    }

    public function deleteMeeting($meetingId)
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->delete("{$this->baseUrl}/meetings/{$meetingId}");

        if ($response->failed()) {
            throw new \Exception('Failed to delete Zoom meeting: ' . $response->body());
        }

        return $response;
    }
}