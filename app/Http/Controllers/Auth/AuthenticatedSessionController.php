<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\SessionLog;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        $this->logSession($request);
        $token = $request->user()->createToken('auth_token')->plainTextToken;
        $cookie = cookie('sanctum_token', $token, 60 * 24, null, null, true, true, false, 'Strict');
        return redirect()->intended(RouteServiceProvider::HOME)->withCookie($cookie);
    }
    public function getAuthenticatedUser(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
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
            Log::error('Error getting user info: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'error' => 'Internal server error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->logLogout();

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // Remove the JWT cookie
        Cookie::queue(Cookie::forget('sanctum_token'));

        return redirect('/');
    }

    /**
     * Handle an incoming API authentication request.
     */
    public function apiLogin(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => $token
            ])->withCookie(cookie('auth_token', $token, 60 * 24, null, null, true, true, false, 'Strict'));
        }

        return response()->json([
            'success' => false,
            'message' => 'The provided credentials are incorrect.'
        ], 401);
    }

    /**
     * Log the user out of the API application.
     */
    public function apiLogout(): JsonResponse
    {
        $this->logLogout();

        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(JWTAuth::refresh());
    }

    /**
     * Get the authenticated User.
     */
    public function me(): JsonResponse
    {
        return response()->json(JWTAuth::user());
    }

    /**
     * Log the session information.
     */
    private function logSession(Request $request): void
    {
        $user = Auth::user();
        $today = Carbon::today();

        $sessionLog = SessionLog::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            [
                'first_login' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]
        );

        $sessionLog->increment('login_count');

        if (!$sessionLog->first_login) {
            $sessionLog->update(['first_login' => now()]);
        }
    }

    /**
     * Log the logout information.
     */
    private function logLogout(): void
    {
        $user = Auth::user();
        if ($user) {
            $today = Carbon::today();
            $sessionLog = SessionLog::where('user_id', $user->id)
                ->where('date', $today)
                ->first();

            if ($sessionLog) {
                $sessionLog->update(['last_logout' => now()]);
            }
        }
    }

    /**
     * Get the token array structure.
     */
    private function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }

    /**
     * Validate the user's JWT token from the cookie.
     */
    public function validateToken(Request $request): JsonResponse
    {
        try {
            $token = $request->cookie('jwt_token');
            if (!$token) {
                return response()->json(['error' => 'Token not found'], 401);
            }

            $user = JWTAuth::setToken($token)->authenticate();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            return response()->json(['user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid token'], 401);
        }
    }
}
