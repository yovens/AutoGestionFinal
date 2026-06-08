<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
// --- AJOUTE LİY SA YO ANBA A ---
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminReply;
// -------------------------------

class ContactController extends Controller
{
    /**
     * Afiche tout mesaj yo nan koòdone admin an.
     */
    public function index()
    {
        $messages = Contact::with('user')->latest()->get();
        return view('admin.contacts.index', compact('messages'));
    }

    /**
     * Efase yon mesaj
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return back()->with('success', 'Message supprimé avec succès.');
    }

    /**
     * Reponn kliyan an pa imel
     */
 public function reply(Request $request, $id) 
{
    $contact = Contact::findOrFail($id);
    
    // Tcheke si gen yon imel
    $emailDestinataire = $contact->email ?? ($contact->user->email ?? null);

    if (!$emailDestinataire) {
        return back()->with('error', 'Impossible de répondre : aucune adresse e-mail trouvée pour ce client.');
    }
    
    // Voye imel la
    \Mail::to($emailDestinataire)->send(new \App\Mail\AdminReply($request->message));
    
    return back()->with('success', 'Réponse envoyée avec succès!');
}
}