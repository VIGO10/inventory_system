<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return $this->redirectToCorrectDashboard();
            }
        }

        return $next($request);
    }

    protected function redirectToCorrectDashboard()
    {
        $user = Auth::user();

        // Method 1: Using role methods (most common)
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isClient()) {
            return redirect()->route('client.dashboard');
        }
    }
}