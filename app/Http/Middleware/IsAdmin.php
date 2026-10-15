<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->isAdmin()) {
            return redirect($user->homeRoute())->with('error', 'Cette page est réservée à l’administration.');
        }

        return $next($request);
    }
}
