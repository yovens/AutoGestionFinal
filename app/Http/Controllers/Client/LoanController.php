<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\LoanCart;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    // Commencer une location
    public function start(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        // 🚨 Tcheke si machin nan disponib nan stock oswa si estati l deja sou 0
        if ($vehicle->quantity <= 0 || $vehicle->status == 0) {
            $vehicle->quantity = 0;
            $vehicle->status = 0;
            $vehicle->save();
            
            return back()->with('error', 'Ce véhicule est indisponible pour le moment.');
        }

        $duration = $request->input('duration', 5);
        $total = $vehicle->loan_price * $duration;

        // 🔻 Diminue stock la
        $vehicle->quantity -= 1;

        if ($vehicle->quantity == 0) {
            $vehicle->status = 0;
        }

        $vehicle->save();

        // KORIJE ISIT LA: Nou mete 'pending' (En attente) pou Admin nan ka jere l
        LoanCart::create([
            'user_id'       => Auth::id(),
            'vehicle_id'    => $vehicle->id,
            'status'        => 'pending', 
            'duration_days' => $duration,
            'total_amount'  => $total,
            'start_date'    => null, // Li fenk mande, li poko kòmanse
            'end_date'      => null,
        ]);

        return redirect()->route('client.loan')
            ->with('success', "Votre demande de location a été soumise. En attente d'approbation.");
    }

    public function index()
    {
        $loans = LoanCart::where('user_id', Auth::id())
                         ->with('vehicle')
                         ->get();

        return view('client.loan', compact('loans'));
    }

    public function transactions()
    {
        // ✅ Ranplase >get() pa ->get()
        $transactions = \App\Models\Transaction::where('user_id', auth()->id())
            ->with('vehicle')
            ->get();

        return view('client.transactions', compact('transactions'));
    }

public function renew(Request $request, $id)
{
    // Validate kantite jou yo
    $request->validate([
        'additional_days' => 'required|integer|min:1'
    ]);

    $loan = LoanCart::findOrFail($id);

    // Kliyan an ka mande renouvèlman sèlman si li apwouve oswa ekspire
    if (!in_array($loan->status, ['approved', 'expired'])) {
        return back()->with('error', 'Action non autorisée.');
    }

    // Nou anrejistre demann lan
    // Nòt: Ou ta dwe ajoute yon kolòn 'renewal_days_requested' nan tab loans ou
   // Chanje non jaden an pou l matche
$loan->update([
    'status' => 'pending_renewal',
    'renewal_requested_days' => $request->additional_days // Fè atansyon ak non an
]);
    
    return back()->with('success', 'Votre demande de renouvellement a été envoyée à l\'administrateur.');
}
}