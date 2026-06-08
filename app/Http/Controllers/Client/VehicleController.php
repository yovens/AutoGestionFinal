<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::where('status', 1);

        // --- Recherche générale ---
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('brand', 'like', "%{$request->search}%")
                  ->orWhere('model', 'like', "%{$request->search}%");
            });
        }

        // --- Filtre marque ---
        if ($request->brand) {
            $query->where('brand', $request->brand);
        }

        // --- Filtre année ---
        if ($request->year) {
            $query->where('year', $request->year);
        }

        // --- Filtre prix ---
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Options pour les listes déroulantes
        $brands = Vehicle::pluck('brand')->unique();
        $years = Vehicle::pluck('year')->unique()->sortDesc();

        // Renvoyer la vue complète
        return view('client.vehicles', [
            'vehicles' => $query->get(),
            'brands'   => $brands,
            'years'    => $years
        ]);
    }

public function show($id)
{
    $vehicle = Vehicle::findOrFail($id);
    return view('client.vehicle_show', compact('vehicle'));
}

}
