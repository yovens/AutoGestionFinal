<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Throwable $e) {
            Log::warning('Connexion Google échouée', ['error' => $e->getMessage()]);

            return redirect()->route('login')->with('error', 'La connexion avec Google n’a pas abouti. Réessayez ou utilisez votre e-mail.');
        }

        $user = User::where('email', $googleUser->getEmail())->first();
        $isNew = !$user;

        if ($isNew) {
            $user = User::create([
                'nom'       => $googleUser->getName() ?: Str::before($googleUser->getEmail(), '@'),
                'email'     => $googleUser->getEmail(),
                'password'  => Hash::make(Str::random(32)),
                'google_id' => $googleUser->getId(),
                'role'      => User::CLIENT,
                'status'    => User::ACTIVE,
            ]);
        } elseif (!$user->google_id) {
            $user->update(['google_id' => $googleUser->getId()]);
        }

        if ($user->isBlocked()) {
            return redirect()->route('login')->with('error', 'Ce compte a été suspendu. Contactez l’agence pour le réactiver.');
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        if ($isNew) {
            try {
                Mail::raw('Bienvenue sur AutoGestion ! Votre compte a été créé avec Google.', function ($message) use ($user) {
                    $message->to($user->email)->subject('Bienvenue sur AutoGestion');
                });
            } catch (\Throwable $e) {
                Log::error('E-mail de bienvenue non envoyé', ['error' => $e->getMessage()]);
            }
        }

        return redirect($user->homeRoute());
    }
}
