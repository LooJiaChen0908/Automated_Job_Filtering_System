<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ZoomService
{   
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
        ->post('https://api.zoom.us/v2/users/me/meetings', [
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
}