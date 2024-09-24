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

        // Generate JWT token
        $token = JWTAuth::fromUser(Auth::user());

        // Store token in a secure HTTP-only cookie
        Cookie::queue('jwt_token', $token, 60 * 24, null, null, true, true, false, 'Strict');

        return redirect()->intended(RouteServiceProvider::HOME);
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
        // Remove the JWT cookie
        Cookie::queue(Cookie::forget('jwt_token'));

        return redirect('/');
    }

    /**
     * Handle an incoming API authentication request.
     */
    public function apiLogin(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth('api')->user();

        $tokenData = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ];

        $this->logSession($request);

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $tokenData
        ]);
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
