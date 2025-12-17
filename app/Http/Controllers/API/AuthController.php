<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = User::where('email', $request->email)
                        ->where('is_active', true)
                        ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ], 401);
            }

            // Update last login
            $user->update(['last_login_at' => now()]);

            // Create token with abilities based on role
            $abilities = $this->getAbilitiesForRole($user->role_id);
            $token = $user->createToken('omega-cmms-token', $abilities)->plainTextToken;

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role_id' => $user->role_id,
                    'facility_scope' => $user->facility_scope,
                    'access_level' => $user->access_level
                ],
                'token' => $token,
                'abilities' => $abilities
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed'
            ], 500);
        }
    }

    public function refreshToken(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $user->tokens()->delete();
            
            $abilities = $this->getAbilitiesForRole($user->role_id);
            $token = $user->createToken('omega-cmms-token', $abilities)->plainTextToken;
            
            return response()->json([
                'success' => true,
                'token' => $token,
                'abilities' => $abilities
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed'
            ], 500);
        }
    }

    private function getAbilitiesForRole($roleId): array
    {
        return match($roleId) {
            1 => ['*'], // Admin
            2 => [ // Manager
                'facility:view', 'facility:edit',
                'asset:view', 'asset:edit',
                'work-order:view', 'work-order:edit', 'work-order:approve',
                'inventory:view', 'inventory:edit',
                'report:view'
            ],
            3 => [ // Technician
                'work-order:view', 'work-order:edit',
                'asset:view',
                'inventory:view'
            ],
            default => ['user:basic']
        };
    }
}