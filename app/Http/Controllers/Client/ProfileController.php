<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller {

    public function show() {
    // Nou chèche itilizatè a ki konekte kounye a
    $user = auth()->user(); 

    // Nou pase varyab 'user' la nan View a
    return view('client.profile', compact('user'));
}

 public function update(Request $request) {
    $user = auth()->user();

    // 1. Validasyon (sèvi ak 'nom' olye de 'name')
    $request->validate([
        'nom' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
    ]);

    // 2. Aktyalizasyon done
    $user->nom = $request->nom; // Sa a enpòtan!
    $user->email = $request->email;
    
    // Si ou gen yon jaden pou foto (profile_image), asire w li nan $fillable nan User.php
    if ($request->hasFile('profile_image')) {
        $path = $request->file('profile_image')->store('profiles', 'public');
        $user->profile_image = $path;
    }

    $user->save();

    return back()->with('success', 'Profil mis à jour avec succès!');
}

public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'password' => 'required|min:8',
    ]);

    $user = auth()->user();

    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.']);
    }

    $user->password = Hash::make($request->password);
    $user->save();

    return back()->with('success', 'Mot de passe modifié avec succès!');
}
    
}