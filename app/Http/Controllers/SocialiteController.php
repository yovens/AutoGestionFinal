<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail; // Enpòtan pou voye imel

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            // Itilize stateless() pou evite erè session nan devlopman
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Mete google_id ajou si l manke
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
                Auth::login($user);
            } else {
                // Kreye nouvo itilizatè
                $user = User::create([
                    'nom' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(uniqid()), 
                    'google_id'=> $googleUser->getId(),
                ]);

                Auth::login($user);
            }

            // Voye imel konfimasyon
            try {
                Mail::raw('Bienvenue sur AutoGestion ! Votre compte a été connecté avec succès via Google.', function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Bienvenue sur AutoGestion');
                });
            } catch (\Exception $e) {
                \Log::error("Erreur envoi email: " . $e->getMessage());
            }

            return redirect('/client/dashboard');

        } catch (\Exception $e) {
            // Si gen erè, montre mesaj la pou debogaj
            dd($e->getMessage()); 
        }
    }
}