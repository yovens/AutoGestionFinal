<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Vehicle;

class PurchaseController extends Controller
{
    public function start($id)
    {
        $userId = Auth::id();
        $vehicle = Vehicle::findOrFail($id);

        // Enregistrer l'achat
        Transaction::create([
            'user_id'    => $userId,
            'vehicle_id' => $id,
            'amount'     => $vehicle->price,
            'type'       => 'Achat'
        ]);

        return redirect()->route('client.transactions')
            ->with('success', 'Achat effectué avec succès !');
    }
}
