<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $history = Contact::where('user_id', $request->user()->id)->latest()->take(5)->get();

        return view('client.contact', compact('history'));
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        Contact::create($data + ['user_id' => $request->user()->id]);

        return back()->with('success', 'Message envoyé. L’agence vous répond par e-mail.');
    }
}
