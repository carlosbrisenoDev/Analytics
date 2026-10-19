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

        $duration = $data['duration'] ?? null;
        if ($duration !== null) {
            // Normalizar duración: Si el cliente envía milisegundos (ej: 4000ms para 4s) o valores inflados
            if ($duration > 1800) {
                if (($duration / 1000) <= 1800) {
                    $duration = (int) round($duration / 1000);
                } else {
                    $duration = 1800; // Cap máximo a 30 minutos de tiempo activo por vista
                }
            }
            if ($duration < 0) {
                $duration = 0;
            }
        }

        Event::create([
            'site_name' => $data['site_name'],
            'type' => $data['type'],
            'session_id' => $sessionId,
            'region' => $data['region'] ?? null,
            'duration' => $duration,
            'content' => $data['content'] ?? null,
            'x_coord' => $data['x_coord'] ?? null,
            'y_coord' => $data['y_coord'] ?? null,
        ]);

        return response()->json(['message' => 'Event tracked successfully']);
    }
}
