<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\LoanCart;
use App\Models\Transaction;
use App\Models\Vehicle;
use App\Services\FleetService;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function __construct(private FleetService $fleet)
    {
    }

    public function index(Request $request)
    {
        LoanCart::syncExpired();

        $loans = $request->user()->loans()->with('vehicle', 'tickets')->latest()->get();

        return view('client.loan', compact('loans'));
    }

    public function start(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'duration' => 'required|integer|min:1|max:' . FleetService::MAX_DAYS,
        ], [], ['duration' => 'durée']);

        $this->fleet->requestLoan($request->user(), $vehicle->id, (int) $data['duration']);

        return redirect()->route('client.loan')
            ->with('success', 'Demande envoyée. Dès que l’agence la confirme, votre code de retrait apparaît ici.');
    }

    public function renew(Request $request, $id)
    {
        $data = $request->validate([
            'additional_days' => 'required|integer|min:1|max:' . FleetService::MAX_DAYS,
        ], [], ['additional_days' => 'nombre de jours']);

        // Kliyan an ka sèlman manyen pwòp lokasyon pa l.
        $loan = $request->user()->loans()->findOrFail($id);

        $this->fleet->requestRenewal($loan, (int) $data['additional_days']);

        return back()->with('success', 'Demande de prolongation envoyée à l’agence.');
    }

    public function transactions(Request $request)
    {
        $transactions = $request->user()->transactions()->with('vehicle')->latest()->get();

        $totals = [
            'paid'    => $transactions->where('status', Transaction::COMPLETED)->sum('amount'),
            'pending' => $transactions->where('status', Transaction::PENDING)->sum('amount'),
        ];

        $tickets = \App\Models\Ticket::with('vehicle')->where('user_id', $request->user()->id)->latest()->get();

        return view('client.transactions', compact('transactions', 'totals', 'tickets'));
    }
}
