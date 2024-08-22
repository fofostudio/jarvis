<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && in_array(auth()->user()->role, ['super_admin', 'admin'])) {
            return $next($request);
        }

        return redirect('/')->with('error', 'No tienes permiso para acceder a esta página.');
    }
}
