<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiVersion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $version = 'v1'): Response
    {
        // Set API version in request
        $request->merge(['api_version' => $version]);

        // Add version header to response
        $response = $next($request);
        
        if ($response instanceof Response) {
            $response->headers->set('X-API-Version', $version);
        }

        return $response;
    }
}