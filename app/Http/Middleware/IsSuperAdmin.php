<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->isSuperAdmin()) {
            return redirect($user->homeRoute())->with('error', 'Cette page est réservée au super administrateur.');
        }

        return $next($request);
    }
}
