<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;

class HomeController extends Controller
{
    public function __invoke()
    {
        $vehicles = Vehicle::available()->latest()->take(6)->get();

        $stats = [
            'models'   => Vehicle::count(),
            'fromDay'  => Vehicle::available()->min('loan_price'),
            'brands'   => Vehicle::distinct('brand')->count('brand'),
        ];

        return view('home', compact('vehicles', 'stats'));
    }
}
