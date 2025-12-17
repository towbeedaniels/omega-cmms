<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckFacilityAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Admin has access to all facilities
        if ($user->access_level === 'admin') {
            return $next($request);
        }

        // Check if facility_id is in the request
        $facilityId = $this->getFacilityIdFromRequest($request);
        
        if ($facilityId) {
            $userFacilities = json_decode($user->facility_scope, true) ?? [];
            
            if (!in_array($facilityId, $userFacilities)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have access to this facility'
                ], 403);
            }
        }

        return $next($request);
    }

    /**
     * Extract facility_id from request.
     */
    private function getFacilityIdFromRequest(Request $request): ?int
    {
        // Check route parameters
        if ($request->route('facility')) {
            return is_object($request->route('facility')) 
                ? $request->route('facility')->id 
                : intval($request->route('facility'));
        }

        // Check request parameters
        if ($request->has('facility_id')) {
            return intval($request->input('facility_id'));
        }

        // Check JSON body
        if ($request->isJson() && $request->has('facility_id')) {
            return intval($request->json('facility_id'));
        }

        return null;
    }
}