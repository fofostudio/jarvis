<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\GroupOperator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\SessionLog;
use Carbon\Carbon;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthController extends Controller
{
    public function getUserInfo(Request $request)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['success' => false, 'error' => 'User not found'], 404);
            }

            // Cargar las relaciones necesarias
            $user->load(['groupOperators.group.girls', 'groupOperators.group.platforms']);

            $userInfo = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'groups' => $user->groupOperators->map(function ($groupOperator) {
                    $group = $groupOperator->group;
                    return [
                        'id' => $group->id,
                        'name' => $group->name,
                        'shift' => $groupOperator->shift,
                        'girls' => $group->girls->map(function ($girl) {
                            return [
                                'id' => $girl->id,
                                'name' => $girl->name,
                                'internal_id' => $girl->internal_id,
                                'username' => $girl->username,
                                'password' => $girl->password, // Asegúrate de que esto esté encriptado
                                'platform' => $girl->platform->name,
                            ];
                        }),
                        'platforms' => $group->platforms->pluck('name')->unique(),
                    ];
                }),
                'platforms' => $user->groupOperators->flatMap(function ($groupOperator) {
                    return $groupOperator->group->platforms;
                })->pluck('name')->unique(),
            ];

            return response()->json(['success' => true, 'user' => $userInfo]);
        } catch (\Exception $e) {
            Log::error('Error parsing token: ' . $e->getMessage(), ['exception' => $e]);

            if ($e instanceof TokenExpiredException || $e instanceof TokenInvalidException || $e instanceof JWTException) {
                return response()->json(['success' => false, 'error' => 'Token expired or invalid'], 401);
            }

            return response()->json(['success' => false, 'error' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            Log::info('Login attempt with credentials:', $credentials);

            if (!$token = auth('api')->attempt($credentials)) {
                Log::warning('Invalid credentials');
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged in',
                'user' => [
                    'id' => auth('api')->user()->id,
                    'name' => auth('api')->user()->name,
                    'email' => auth('api')->user()->email,
                    'role' => auth('api')->user()->role,
                    'platforms' => auth('api')->user()->platforms->map(function ($platform) {
                        return [
                            'id' => $platform->id,
                            'name' => $platform->name
                        ];
                    }),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage(), ['exception' => $e]);
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
