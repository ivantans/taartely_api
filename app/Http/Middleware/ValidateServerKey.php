<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateServerKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $serverKey = $request->header('X-Server-Key');

        if ($serverKey !== env('API_SERVER_KEY')) {
            return response()->json(['error' => 'Unauthorized. Invalid server key.'], 401);
        }

        return $next($request);
    }
}
