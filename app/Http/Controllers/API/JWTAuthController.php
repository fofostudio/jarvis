<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SessionLog;
use Carbon\Carbon;

class JWTAuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            \Log::info('Login attempt with credentials:', $credentials);

            if (!$token = auth('api')->attempt($credentials)) {
                \Log::warning('Invalid credentials');
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $user = auth('api')->user();

            if (!$user) {
                \Log::error('User not found after successful authentication');
                return response()->json(['error' => 'User not found'], 404);
            }

            $groups = $user->groups()
                ->with('girls')
                ->withCount('girls')
                ->get()
                ->map(function ($group) {
                    return [
                        'id' => $group->id,
                        'name' => $group->name,
                        'girls_count' => $group->girls_count,
                        'shift' => $group->pivot->shift,
                        'girls' => $group->girls ? $group->girls->map(function ($girl) {
                            return [
                                'id' => $girl->id,
                                'username' => $girl->username,
                                'password' => $girl->password,
                                'platform' => $girl->platform
                            ];
                        }) : []
                    ];
                });

            \Log::info('User groups loaded:', $groups->toArray());

            $platforms = $user->platforms;
            \Log::info('User platforms loaded:', $platforms->toArray());

            $response = [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'groups' => $groups,
                    'platforms' => $platforms->map(function ($platform) {
                        return [
                            'id' => $platform->id,
                            'name' => $platform->name
                        ];
                    }),
                    'girls' => $user->girls ? $user->girls->map(function ($girl) {
                        return [
                            'id' => $girl->id,
                            'username' => $girl->username,
                            'password' => $girl->password,
                            'platform' => $girl->platform
                        ];
                    }) : []
                ]
            ];

            \Log::info('Login successful, response:', $response);
            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
