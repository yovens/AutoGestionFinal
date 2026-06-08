<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->status === 'BLOCKED') {

            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
    ->with('error', '🚫 Accès refusé : votre compte a été désactivé par l’administrateur système. Toutes les connexions sont bloquées pour ce compte. Pour toute assistance ou réactivation, veuillez contacter le support officiel ou l’équipe de gestion de la plateforme AutoGestion.');
        }

        return $next($request);
    }
}