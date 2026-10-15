<?php

namespace App\Http\Controllers\Counter;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\FleetService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SaleController extends Controller
{
    public function __construct(private FleetService $fleet)
    {
    }

    /** Kliyan an peye machin li te rezève a: nou ankese epi enprime bon de remise a. */
    public function confirm(Request $request, Transaction $transaction)
    {
        $data = $request->validate([
            'method' => ['required', Rule::in(array_keys(Transaction::METHODS))],
        ], [], ['method' => 'mode de paiement']);

        $ticket = $this->fleet->confirmSale($transaction, $request->user(), $data['method']);

        return $ticket
            ? redirect()->route('counter.tickets.show', $ticket)->with('success', "Paiement {$transaction->reference} encaissé. Remettez les clés et les papiers.")
            : back()->with('success', "Paiement {$transaction->reference} encaissé.");
    }

    public function cancel(Transaction $transaction)
    {
        $this->fleet->cancelSale($transaction);

        return back()->with('success', "Achat {$transaction->reference} annulé. Le véhicule est remis en vente.");
    }
}
