<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/** Comptoir: admin ak sekretè. */
class IsStaff
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->isStaff()) {
            return redirect($user->homeRoute())->with('error', 'Cette page est réservée au personnel de l’agence.');
        }

        return $next($request);
    }
}
