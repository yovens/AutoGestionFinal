<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\LoanCart;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        LoanCart::syncExpired();

        $stats = [
            'clients'       => User::where('role', User::CLIENT)->count(),
            'fleet'         => Vehicle::sum('quantity') + LoanCart::whereIn('status', LoanCart::HOLDING)->count(),
            'available'     => (int) Vehicle::sum('quantity'),
            'out'           => LoanCart::whereIn('status', [LoanCart::APPROVED, LoanCart::EXPIRED, LoanCart::PENDING_RENEWAL])->count(),
            'revenueMonth'  => Transaction::completed()->where('created_at', '>=', now()->startOfMonth())->sum('amount'),
            'revenueTotal'  => Transaction::completed()->sum('amount'),
            'unread'        => Contact::where('is_read', false)->count(),
        ];

        // Sa ki bezwen yon aksyon jodi a.
        $todo = [
            'pendingLoans'    => LoanCart::with('user', 'vehicle')->where('status', LoanCart::PENDING)->oldest()->get(),
            'renewals'        => LoanCart::with('user', 'vehicle')->where('status', LoanCart::PENDING_RENEWAL)->oldest()->get(),
            'late'            => LoanCart::with('user', 'vehicle')->where('status', LoanCart::EXPIRED)->orderBy('end_date')->get(),
            'dueSoon'         => LoanCart::with('user', 'vehicle')->where('status', LoanCart::APPROVED)
                                    ->whereBetween('end_date', [Carbon::today(), Carbon::today()->addDays(2)])->orderBy('end_date')->get(),
            'pendingPurchases'=> Transaction::with('user', 'vehicle')->where('type', Transaction::SALE)->where('status', Transaction::PENDING)->oldest()->get(),
        ];

        // Revni 6 dènye mwa yo, separe vant / lokasyon.
        $chart = collect(range(5, 0))->map(function ($i) {
            $start = now()->startOfMonth()->subMonths($i);
            $end = $start->copy()->endOfMonth();
            $q = Transaction::completed()->whereBetween('created_at', [$start, $end]);

            return [
                'label'   => ucfirst($start->locale('fr')->isoFormat('MMM')),
                'sales'   => (clone $q)->where('type', Transaction::SALE)->sum('amount'),
                'rentals' => (clone $q)->where('type', Transaction::RENTAL)->sum('amount'),
            ];
        });

        $recent = Transaction::with('user', 'vehicle')->latest()->take(6)->get();

        return view('admin.dashboard', compact('stats', 'todo', 'chart', 'recent'));
    }
}
