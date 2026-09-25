<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\RateLimiter;

class IngestionController extends Controller
{
    public function track(Request $request)
    {
        $data = $request->validate([
            'site_name' => 'required|string|max:255',
            'type' => 'required|string|in:view,click,reload,external_link,cta_click,form_start,form_submit,download',
            'session_id' => 'required|string|max:64',
            'region' => 'nullable|string|max:100',
            'duration' => 'nullable|integer',
            'content' => 'nullable|string|max:255',
            'x_coord' => 'nullable|integer',
            'y_coord' => 'nullable|integer'
        ]);

        $sessionId = $data['session_id'];

        // Anti-DDoS: maximo 60 peticiones por minuto por sesion
        $key = 'track_session_' . $sessionId;

        if (RateLimiter::tooManyAttempts($key, 500)) {
            return response()->json(['message' => 'Too Many Requests'], 429);
        }

        RateLimiter::hit($key, 60); // decay de 60 segundos

        Event::create([
            'site_name' => $data['site_name'],
            'type' => $data['type'],
            'session_id' => $sessionId,
            'region' => $data['region'] ?? null,
            'duration' => $data['duration'] ?? null,
            'content' => $data['content'] ?? null,
            'x_coord' => $data['x_coord'] ?? null,
            'y_coord' => $data['y_coord'] ?? null,
        ]);

        return response()->json(['message' => 'Event tracked successfully']);
    }
}
