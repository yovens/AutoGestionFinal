<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Affiche le formulaire login/register
    public function showLoginForm()
    {
        return view('auth'); // auth.blade.php
    }

    // Traitement login
    public function login(Request $request)
    {
        // Validation
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Tentative d'authentification
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirection selon rôle
            $user = Auth::user();
            if ($user->role === 'ADMIN') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('client.dashboard');
            }
        }

        // En cas d'erreur
        return back()->withErrors(['email' => 'Identifiants incorrects']);
    }

    // Déconnexion
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
    
}
