<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsClient
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'CLIENT') {
            return redirect()->route('login')->with('error', 'Accès refusé !');
        }
        return $next($request);
    }
}
