<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail; // Importez la Facade Mail
use App\Mail\AdminContactNotification; // Importez la classe Mailable

class ContactController extends Controller
{
    public function index(){ return view('client.contact'); }

  public function send(Request $request) {
    $data = $request->validate([
        'subject'=>'required|string|max:255',
        'message'=>'required|string'
    ]);

    // Sove nan baz done a
    \App\Models\Contact::create([
        'user_id' => auth()->id(),
        'subject' => $data['subject'],
        'message' => $data['message']
    ]);

    // (Ou ka kenbe kòd Mail la tou si ou vle)
    return back()->with('success', 'Message envoyé!');
}
}