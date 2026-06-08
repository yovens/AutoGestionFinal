<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // 📌 Affichage du panier
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())
                        ->with('vehicle')
                        ->get();

        return view('client.cart', compact('cartItems')); // <<< IMPORTANT
    }

    // 📌 Ajout au panier
    public function add($vehicle_id)
    {
        Cart::create([
            'user_id' => Auth::id(),
            'vehicle_id' => $vehicle_id,
        ]);

        return redirect()->route('client.cart')->with('success', 'Véhicule ajouté au panier !');
    }

    // 📌 Suppression du panier
    public function delete($id)
    {
        Cart::where('user_id', Auth::id())->where('id', $id)->delete();

        return back()->with('success', 'Élément supprimé du panier');
    }
}
