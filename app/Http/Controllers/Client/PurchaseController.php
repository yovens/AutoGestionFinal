<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Vehicle;
use App\Services\FleetService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(private FleetService $fleet)
    {
    }

    /** Kliyan an rezève machin nan pou achte l; agence la konfime lè peman an fèt. */
    public function start(Request $request, Vehicle $vehicle)
    {
        $this->fleet->requestPurchase($request->user(), $vehicle->id);

        // Machin nan pa bezwen rete nan panye a ankò.
        Cart::where('user_id', $request->user()->id)->where('vehicle_id', $vehicle->id)->delete();

        return redirect()->route('client.transactions')
            ->with('success', 'Véhicule réservé. Passez à l’agence pour finaliser le paiement.');
    }
}
