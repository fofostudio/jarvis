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

        $user = Auth::user();
        $today = Carbon::today();


        $sessionLog = SessionLog::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['first_login' => now(), 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]
        );

        $sessionLog->increment('login_count');

        if (!$sessionLog->first_login) {
            $sessionLog->update(['first_login' => now()]);
        }


        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $today = Carbon::today();

        if ($user) {
            $sessionLog = SessionLog::where('user_id', $user->id)
                ->where('date', $today)
                ->first();

            if ($sessionLog) {
                $sessionLog->update(['last_logout' => now()]);
            }
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
