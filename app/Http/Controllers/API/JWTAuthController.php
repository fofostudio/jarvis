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
            Log::info('Received token: ' . $request->bearerToken());

            if (!$token = $request->bearerToken()) {
                return response()->json(['success' => false, 'error' => 'No token provided'], 401);
            }

            try {
                if (!$user = JWTAuth::parseToken()->authenticate()) {
                    return response()->json(['success' => false, 'error' => 'User not found'], 404);
                }
                Log::info('User auth attempt result: ' . ($user ? 'Success' : 'Failure'));
            } catch (TokenExpiredException $e) {
                return response()->json(['success' => false, 'error' => 'Token expired'], 401);
            } catch (TokenInvalidException $e) {
                return response()->json(['success' => false, 'error' => 'Token invalid'], 401);
            } catch (JWTException $e) {
                return response()->json(['success' => false, 'error' => 'Token absent'], 401);
            }

            $user = auth('api')->user();

            if (!$user) {
                Log::error('User not found when trying to get user info');
                return response()->json(['success' => false, 'error' => 'User not authenticated'], 401);
            }

            // ... (resto del código para obtener información del usuario)
        } catch (\Exception $e) {
            Log::error('Error getting user info: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'error' => 'Internal server error: ' . $e->getMessage()
            ], 500);
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

            $user = auth('api')->user();

            if (!$user) {
                Log::error('User not found after successful authentication');
                return response()->json(['error' => 'User not found'], 404);
            }

            // Obtener los grupos asignados al usuario a través de GroupOperator
            $groupOperators = GroupOperator::where('user_id', $user->id)
                ->with(['group' => function ($query) {
                    $query->with(['girls' => function ($query) {
                        $query->select('id', 'username', 'platform', 'group_id');
                    }]);
                }])
                ->get();

            $groups = $groupOperators->map(function ($groupOperator) {
                $group = $groupOperator->group;
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'girls_count' => $group->girls->count(),
                    'shift' => $groupOperator->shift,
                    'girls' => $group->girls->map(function ($girl) {
                        return [
                            'id' => $girl->id,
                            'username' => $girl->username,
                            'platform' => $girl->platform
                        ];
                    })
                ];
            });

            Log::info('User groups loaded:', $groups->toArray());

            $platforms = $user->platforms;
            Log::info('User platforms loaded:', $platforms->toArray());

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

            Log::info('Login successful, response:', $response);
            return response()->json($response);
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
