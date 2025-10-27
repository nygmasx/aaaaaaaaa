<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->is_active) {
            Auth::logout();
            return redirect('/login')->withErrors([
                'email' => 'Votre compte a été désactivé. Contactez l\'administrateur.'
            ]);
        }

        return $next($request);
    }
}