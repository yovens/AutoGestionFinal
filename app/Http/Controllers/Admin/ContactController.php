<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminReply;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter');

        $messages = Contact::with('user')
            ->when($filter === 'unread', fn ($q) => $q->where('is_read', false))
            ->latest()->get();

        return view('admin.contacts.index', compact('messages', 'filter'));
    }

    public function markRead(Contact $contact)
    {
        $contact->update(['is_read' => true]);

        return back();
    }

    public function reply(Request $request, Contact $contact)
    {
        $data = $request->validate(['message' => 'required|string|max:5000']);

        $email = $contact->user?->email;

        if (!$email) {
            return back()->with('error', 'Ce client n’a pas d’adresse e-mail : impossible de répondre.');
        }

        try {
            Mail::to($email)->send(new AdminReply($data['message']));
        } catch (\Throwable $e) {
            Log::error('Réponse contact non envoyée', ['contact' => $contact->id, 'error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'L’e-mail n’a pas pu partir. Vérifiez la configuration MAIL du fichier .env.');
        }

        $contact->update(['is_read' => true]);

        return back()->with('success', "Réponse envoyée à {$email}.");
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return back()->with('success', 'Message supprimé.');
    }
}
