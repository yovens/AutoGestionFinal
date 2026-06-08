<?php




namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\LoanCart;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
 
        $userName = $user->nom ?? $user->name ?? 'Client';
        // 🔥 Comptage des données
        $cartCount = Cart::where('user_id', $user->id)->count();

        $loanCount = LoanCart::where('user_id', $user->id)->count();

        $transactionCount = Transaction::where('user_id', $user->id)->count();

        $totalSpent = Transaction::where('user_id', $user->id)->sum('amount');

        // 🔥 Retourne bien toutes les variables à la vue
     
        return view('client.dashboard', compact('userName','cartCount','loanCount','transactionCount','totalSpent'));
 
    }
               public function about() {
        return view('client.about');
    }
}























