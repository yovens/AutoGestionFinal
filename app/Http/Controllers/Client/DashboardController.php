<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\LoanCart;
use App\Models\Transaction;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        LoanCart::syncExpired();

        $user = $request->user();

        // Lokasyon ki toujou ap kouri oswa ki tann repons.
        $activeLoans = $user->loans()->with('vehicle', 'tickets')
            ->whereIn('status', LoanCart::HOLDING)
            ->orderByRaw("CASE status WHEN 'ready' THEN 0 WHEN 'expired' THEN 1 WHEN 'approved' THEN 2 ELSE 3 END")
            ->get();

        $pendingPurchases = $user->transactions()->with('vehicle')
            ->where('type', Transaction::SALE)->where('status', Transaction::PENDING)->get();

        $stats = [
            'cart'         => $user->cartItems()->count(),
            'loans'        => $user->loans()->count(),
            'transactions' => $user->transactions()->count(),
            'spent'        => $user->transactions()->completed()->sum('amount'),
        ];

        $suggestions = Vehicle::available()->latest()->take(3)->get();

        return view('client.dashboard', compact('user', 'activeLoans', 'pendingPurchases', 'stats', 'suggestions'));
    }

    public function about()
    {
        return view('client.about');
    }
}
