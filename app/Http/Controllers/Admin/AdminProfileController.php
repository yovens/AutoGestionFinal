<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminProfileController extends Controller
{
    /**
     * Affiche la page profil admin
     */
    public function edit()
    {
        $admin = Auth::user(); 

        if (!$admin) {
            return redirect('/login')->withErrors('Veuillez vous connecter.');
        }

        return view('admin.profile', [
            'adminName' => $admin->nom ?? $admin->name ?? 'Administrateur',
            'adminRole' => $admin->role ?? 'Admin Principal',
            'adminProfilePic' => $admin->profile_photo_path 
                ? Storage::url($admin->profile_photo_path)
                : 'https://via.placeholder.com/120/38bdf8/ffffff?text=AD',

            'adminPresentation' => $admin->bio ?? 'Aucune bio définie.'
        ]);
    }

    /**
     * Mise à jour des informations de base
     */
    public function update(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'nom' => 'required|string|max:255',
            'presentation' => 'nullable|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            if ($admin->profile_photo_path) {
                Storage::delete($admin->profile_photo_path);
            }

            $admin->profile_photo_path = $request->file('profile_image')
                                                ->store('profile-photos', 'public');
        }

        $admin->nom = $request->nom;
        $admin->bio = $request->presentation;
        $admin->save();

        return back()->with('success', 'Profil mis à jour avec succès !');
    }

    /**
     * Mise à jour du mot de passe de l'administrateur
     */
    public function updatePassword(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'La confirmation du nouveau mot de passe ne correspond pas.',
            'password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.'
        ]);

        // Tcheke si ansyen modpas li tape a se sa ki nan baz de done a vre
        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        // Chanje modpas la epi ankripte l ak Hash
        $admin->password = Hash::make($request->password);
        $admin->save();

        return back()->with('success', 'Votre mot de passe a été modifié avec succès !');
    }
}