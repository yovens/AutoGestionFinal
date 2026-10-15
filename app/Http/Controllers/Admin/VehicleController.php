<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanCart;
use App\Models\Vehicle;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));

        $vehicles = Vehicle::query()
            ->withCount(['loans as out_count' => fn ($q) => $q->whereIn('status', LoanCart::HOLDING)])
            ->when($search, fn ($q) => $q->where(fn ($q) => $q
                ->where('brand', 'like', "%{$search}%")
                ->orWhere('model', 'like', "%{$search}%")
                ->orWhere('plate', 'like', "%{$search}%")))
            ->orderBy('brand')->orderBy('model')
            ->get();

        return view('admin.vehicles.index', compact('vehicles', 'search'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['image'] = $request->file('image')->store('vehicles', 'public');

        $vehicle = Vehicle::create($data);
        Audit::log('vehicle.create', "Véhicule ajouté : {$vehicle->name} ({$vehicle->plate})", $vehicle, $vehicle->only(['price', 'loan_price', 'quantity']));

        return redirect()->route('admin.vehicles.index')->with('success', "{$data['brand']} {$data['model']} ajouté au parc.");
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['loans' => fn ($q) => $q->with('user')->latest(), 'transactions' => fn ($q) => $q->with('user')->latest()]);

        return view('admin.vehicles.show', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $data = $this->validated($request, $vehicle);

        if ($request->hasFile('image')) {
            if ($vehicle->image) {
                Storage::disk('public')->delete($vehicle->image);
            }
            $data['image'] = $request->file('image')->store('vehicles', 'public');
        }

        $vehicle->fill($data);
        $changes = collect($vehicle->getDirty())->except(['updated_at', 'image'])
            ->mapWithKeys(fn ($new, $key) => [$key => [$vehicle->getOriginal($key), $new]])->all();
        $vehicle->save();
        if ($changes || $request->hasFile('image')) {
            $labels = ['price' => 'prix de vente', 'loan_price' => 'prix/jour', 'quantity' => 'stock', 'plate' => 'plaque', 'brand' => 'marque', 'model' => 'modèle', 'year' => 'année', 'status' => 'disponibilité'];
            $summary = collect($changes)->except('status')->map(fn ($v, $k) => ($labels[$k] ?? $k) . ' ' . $v[0] . ' → ' . $v[1])->implode(', ');
            Audit::log('vehicle.update', "Véhicule modifié {$vehicle->plate}" . ($summary ? " : {$summary}" : ' (photo)'), $vehicle, $changes);
        }

        return back()->with('success', "{$vehicle->name} mis à jour.");
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->loans()->whereIn('status', LoanCart::HOLDING)->exists()) {
            return back()->with('error', 'Ce véhicule a une location en cours. Enregistrez le retour avant de le supprimer.');
        }

        if ($vehicle->image) {
            Storage::disk('public')->delete($vehicle->image);
        }

        Audit::log('vehicle.delete', "Véhicule supprimé : {$vehicle->name} ({$vehicle->plate})");
        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')->with('success', 'Véhicule supprimé.');
    }

    private function validated(Request $request, ?Vehicle $vehicle = null): array
    {
        $data = $request->validate([
            'brand'      => 'required|string|max:100',
            'model'      => 'required|string|max:100',
            'plate'      => ['required', 'string', 'max:20', Rule::unique('vehicles', 'plate')->ignore($vehicle?->id)],
            'year'       => 'required|integer|min:1950|max:' . (date('Y') + 1),
            'price'      => 'required|integer|min:0',
            'loan_price' => 'required|integer|min:0',
            'quantity'   => 'required|integer|min:0|max:999',
            'image'      => [$vehicle ? 'nullable' : 'required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ], [], [
            'brand' => 'marque', 'model' => 'modèle', 'plate' => 'plaque', 'year' => 'année',
            'price' => 'prix de vente', 'loan_price' => 'prix par jour', 'quantity' => 'stock',
        ]);

        $data['plate'] = strtoupper(trim($data['plate']));
        // Disponibilite a swiv stock la otomatikman.
        $data['status'] = $data['quantity'] > 0;
        unset($data['image']);

        return $data;
    }
}
