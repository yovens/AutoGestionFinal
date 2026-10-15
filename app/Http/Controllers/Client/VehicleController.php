<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search'    => 'nullable|string|max:100',
            'brand'     => 'nullable|string|max:100',
            'year'      => 'nullable|integer',
            'min_price' => 'nullable|integer|min:0',
            'max_price' => 'nullable|integer|min:0',
            'sort'      => 'nullable|in:recent,price_asc,price_desc,loan_asc',
        ]);

        $query = Vehicle::available()
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(fn ($q) => $q->where('brand', 'like', "%{$search}%")->orWhere('model', 'like', "%{$search}%"));
            })
            ->when($filters['brand'] ?? null, fn ($q, $brand) => $q->where('brand', $brand))
            ->when($filters['year'] ?? null, fn ($q, $year) => $q->where('year', $year))
            ->when($filters['min_price'] ?? null, fn ($q, $min) => $q->where('price', '>=', $min))
            ->when($filters['max_price'] ?? null, fn ($q, $max) => $q->where('price', '<=', $max));

        match ($filters['sort'] ?? 'recent') {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'loan_asc'   => $query->orderBy('loan_price'),
            default      => $query->latest(),
        };

        return view('client.vehicles', [
            'vehicles' => $query->get(),
            'brands'   => Vehicle::available()->orderBy('brand')->distinct()->pluck('brand'),
            'years'    => Vehicle::available()->orderByDesc('year')->distinct()->pluck('year'),
            'filters'  => $filters,
        ]);
    }

    public function show(Request $request, Vehicle $vehicle)
    {
        $inCart = $request->user()->cartItems()->where('vehicle_id', $vehicle->id)->exists();

        $similar = Vehicle::available()
            ->whereKeyNot($vehicle->id)
            ->where(fn ($q) => $q->where('brand', $vehicle->brand)->orWhereBetween('price', [$vehicle->price * 0.7, $vehicle->price * 1.3]))
            ->take(3)->get();

        return view('client.vehicle_show', compact('vehicle', 'inCart', 'similar'));
    }
}
