<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-KEY');

        if (!$apiKey) {
            return response()->json(['error' => 'API key missing'], 401);
        }

        $site = \App\Models\Site::where('api_key', $apiKey)->first();

        if (!$site) {
            return response()->json(['error' => 'Invalid API key'], 401);
        }

        $request->merge(['site' => $site]);

        return $next($request);
    }
}
