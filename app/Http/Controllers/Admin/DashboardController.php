<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Transaction;
use App\Models\LoanCart;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
 public function index()
{
    $admin = Auth::user();
    $adminName = $admin->nom ?? $admin->name ?? 'Administrateur';

    $usersCount = User::count();
    $vehiclesCount = Vehicle::count();
    $transactionsCount = Transaction::count();
    $loansCount = LoanCart::count();

    // 1. Done pou grafik Liy
    $months = [];
    $data = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $months[] = $date->format('M Y');
        $data[] = Transaction::whereMonth('created_at', $date->month)
                             ->whereYear('created_at', $date->year)->count();
    }

    // 2. Done pou grafik Doughnut
    $locationsCount = Transaction::where('type', 'location')->count();
    $salesCount = Transaction::where('type', 'vente')->count();
    
    // 3. Konte mesaj ki poko li
    $unreadMessagesCount = \App\Models\Contact::where('is_read', false)->count();
    view()->share('unreadMessagesCount', $unreadMessagesCount);

    return view('admin.dashboard', compact(
        'adminName', 
        'usersCount', 
        'vehiclesCount', 
        'transactionsCount', 
        'loansCount', 
        'months', 
        'data', 
        'locationsCount', 
        'salesCount'
    ));
}

}
