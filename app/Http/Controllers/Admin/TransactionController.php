<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // Pou administratè a wè tout tranzaksyon yo
    public function index() {
        $transactions = Transaction::with('user','vehicle')->orderBy('created_at', 'desc')->get();
        return view('admin.transactions.index', compact('transactions'));
    }

    // Pou kreyasyon yon tranzaksyon dirèkteman depi nan panèl admin nan
    public function store(Request $request) {
   $request->validate([
        'user_id'    => 'required|exists:users,id',
        'vehicle_id' => 'required|exists:vehicles,id',
        'amount'     => 'required|numeric|min:0',
        // Validasyon an dwe matche ak ENUM lan:
        'type'       => 'required|in:location,vente', 
    ]);
        $transaction = new Transaction();
        $transaction->user_id = $request->user_id;
        $transaction->vehicle_id = $request->vehicle_id;
        $transaction->amount = $request->amount;
        $transaction->type = $request->type;
        $transaction->status = 'completed'; // Tranzaksyon sa fèt an lokal kòm konplete
        $transaction->save();

        return redirect()->route('admin.transactions.index')->with('success', 'Transaction enregistrée avec succès !');
    }

    // Supprimer yon tranzaksyon
    public function destroy(Transaction $transaction){
        $transaction->delete();
        return back()->with('success','Transaction supprimée avec succès.');
    }

    // Pou kliyan an wè pwòp tranzaksyon pa li (si w bezwen metòd sa a)
    public function transactions() {
        $transactions = auth()->user()->transactions()->with('vehicle')->orderBy('created_at', 'desc')->get();
        return view('client.transactions', compact('transactions'));
    }
    public function show(Transaction $transaction) {
    // N ap chaje relasyon yo pou asire nou done yo la
    $transaction->load('user', 'vehicle');
    return view('admin.transactions.show', compact('transaction'));
}
}