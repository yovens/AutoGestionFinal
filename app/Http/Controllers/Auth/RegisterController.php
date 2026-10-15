<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth', ['mode' => 'register']);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nom'       => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users',
            'telephone' => 'nullable|string|max:30',
            'password'  => 'required|string|min:8|confirmed',
        ], [
            'email.unique' => 'Un compte existe déjà avec cet e-mail. Connectez-vous.',
        ]);

        $user = User::create([
            'nom'       => $data['nom'],
            'email'     => $data['email'],
            'telephone' => $data['telephone'] ?? null,
            'password'  => Hash::make($data['password']),
            'role'      => User::CLIENT,
            'status'    => User::ACTIVE,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('client.dashboard')->with('success', 'Bienvenue ' . $user->first_name . ', votre compte est prêt.');
    }
}
