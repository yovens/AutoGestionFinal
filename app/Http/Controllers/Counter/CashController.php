<?php

namespace App\Http\Controllers\Counter;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Jounal kès la: tout sa ki ankese yon jou, pa mòd peman ak pa anplwaye.
 * Sekretè a wè sa l ankese li menm; admin nan wè tout moun.
 */
class CashController extends Controller
{
    public function index(Request $request)
    {
        try {
            $date = $request->filled('date') ? Carbon::parse($request->query('date'))->startOfDay() : Carbon::today();
        } catch (\Throwable) {
            $date = Carbon::today();
        }

        $me = $request->user();
        $agentId = $me->isAdmin() ? $request->query('agent') : $me->id;

        $payments = Transaction::with('user', 'vehicle', 'recorder')
            ->completed()
            ->whereNotNull('recorded_by')
            ->whereDate('updated_at', $date)
            ->when($agentId, fn ($q) => $q->where('recorded_by', $agentId))
            ->orderBy('updated_at')
            ->get();

        $byMethod = collect(Transaction::METHODS)->map(fn ($label, $key) => [
            'label' => $label,
            'count' => $payments->where('method', $key)->count(),
            'total' => $payments->where('method', $key)->sum('amount'),
        ]);

        $agents = $me->isAdmin() ? User::whereIn('role', User::STAFF)->orderBy('nom')->get(['id', 'nom']) : collect();

        return view('counter.cash', compact('payments', 'byMethod', 'date', 'agents', 'agentId'));
    }
}
