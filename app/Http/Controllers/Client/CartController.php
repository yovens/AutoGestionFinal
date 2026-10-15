<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = $request->user()->cartItems()->with('vehicle')->latest()->get()
            ->filter(fn ($item) => $item->vehicle); // machin ki efase pa parèt

        return view('client.cart', compact('cartItems'));
    }

    public function add(Request $request, Vehicle $vehicle)
    {
        $item = Cart::firstOrCreate([
            'user_id'    => $request->user()->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $message = $item->wasRecentlyCreated
            ? "{$vehicle->name} ajouté à votre sélection."
            : "{$vehicle->name} est déjà dans votre sélection.";

        return back()->with('success', $message);
    }

    public function delete(Request $request, $id)
    {
        $request->user()->cartItems()->whereKey($id)->delete();

        return back()->with('success', 'Véhicule retiré de votre sélection.');
    }
}
