<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth'); // auth.blade.php
    }

    public function register(Request $request)
    {
        // Validation
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed', // <- "confirmed" attend password_confirmation
        ]);

        // Création utilisateur avec hash Bcrypt
        User::create([
            'nom' => $data['nom'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']), // 🔑 important !
            'role' => 'CLIENT',
            'status' => 'ACTIVE',
        ]);

        return redirect()->route('login')->with('success', 'Compte créé, connectez-vous');
    }
}
