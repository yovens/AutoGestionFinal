<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    public function index() {
        $vehicles = Vehicle::all();
        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand'      => 'required|string',
            'model'      => 'required|string',
            'plate'      => 'required|string',
            'year'       => 'required|integer',
            'price'      => 'required|numeric',
            'loan_price' => 'required|numeric',
            'status'     => 'required|integer',
            'image'      => 'required|image',
            'quantity'   => 'required|integer|min:0',
        ]);

        // ⚡ OTO-STATUT : Si admin mete kantite a sou 0, nou fòse estati a tounen Indisponible (0)
        if ($validated['quantity'] == 0) {
            $validated['status'] = 0;
        } else {
            $validated['status'] = 1; // Si gen stock, li dwe disponible
        }

        $imagePath = $request->file('image')->store('vehicles', 'public');
        $validated['image'] = $imagePath;

        Vehicle::create($validated);

        return redirect()->route('admin.vehicles.index')->with('success', 'Véhicule ajouté avec succès !');
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $validated = $request->validate([
            'brand'      => 'required|string',
            'model'      => 'required|string',
            'plate'      => 'required|string',
            'year'       => 'required|integer',
            'price'      => 'required|numeric',
            'loan_price' => 'required|numeric',
            'status'     => 'required|integer',
            'image'      => 'nullable|image',
            'quantity'   => 'required|integer|min:0',
        ]);

        // ⚡ OTO-STATUT : Menm lojik la pou modifikasyon an
        if ($validated['quantity'] == 0) {
            $validated['status'] = 0;
        } else {
            $validated['status'] = 1;
        }

        if ($request->hasFile('image')) {
            if ($vehicle->image) {
                Storage::disk('public')->delete($vehicle->image);
            }
            $imagePath = $request->file('image')->store('vehicles', 'public');
            $validated['image'] = $imagePath;
        }

        $vehicle->update($validated);

        return redirect()->route('admin.vehicles.index')->with('success', 'Véhicule modifié avec succès !');
    }

    public function destroy(Vehicle $vehicle) {
        if($vehicle->image){
            Storage::disk('public')->delete($vehicle->image);
        }
        $vehicle->delete();
        return back()->with('success', 'Véhicule supprimé');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load('loans', 'transactions');
        return view('admin.vehicles.show', compact('vehicle'));
    }
}