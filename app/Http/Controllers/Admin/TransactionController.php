<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\FleetService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function __construct(private FleetService $fleet)
    {
    }

    public function index(Request $request)
    {
        $type = $request->query('type');
        $status = $request->query('status');

        $transactions = Transaction::with('user', 'vehicle')
            ->when(in_array($type, [Transaction::SALE, Transaction::RENTAL], true), fn ($q) => $q->where('type', $type))
            ->when(array_key_exists((string) $status, Transaction::STATUS_LABELS), fn ($q) => $q->where('status', $status))
            ->latest()->get();

        $totals = [
            'revenue' => Transaction::completed()->sum('amount'),
            'sales'   => Transaction::completed()->where('type', Transaction::SALE)->sum('amount'),
            'rentals' => Transaction::completed()->where('type', Transaction::RENTAL)->sum('amount'),
            'pending' => Transaction::where('status', Transaction::PENDING)->count(),
        ];

        $clients = User::where('role', User::CLIENT)->orderBy('nom')->get(['id', 'nom', 'email']);
        $vehicles = Vehicle::orderBy('brand')->get();

        return view('admin.transactions.index', compact('transactions', 'totals', 'type', 'status', 'clients', 'vehicles'));
    }

    /** Antre yon peman manyèlman (eg. peman nan biwo ki pa pase sou sit la). */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'    => 'required|exists:users,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'amount'     => 'required|integer|min:0',
            'type'       => ['required', Rule::in([Transaction::SALE, Transaction::RENTAL])],
        ], [], ['user_id' => 'client', 'vehicle_id' => 'véhicule', 'amount' => 'montant']);

        Transaction::create($data + ['status' => Transaction::COMPLETED]);

        return redirect()->route('admin.transactions.index')->with('success', 'Paiement enregistré.');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('user', 'vehicle', 'loan');

        return view('admin.transactions.show', compact('transaction'));
    }

    public function confirm(Transaction $transaction)
    {
        $this->fleet->confirmSale($transaction);

        return back()->with('success', "Paiement {$transaction->reference} confirmé.");
    }

    public function cancel(Transaction $transaction)
    {
        $this->fleet->cancelSale($transaction);

        return back()->with('success', "Achat {$transaction->reference} annulé. Le véhicule est remis en vente.");
    }

    public function destroy(Transaction $transaction)
    {
        // Yon acha ki t ap tann: lage machin nan anvan n efase.
        if ($transaction->type === Transaction::SALE && $transaction->status === Transaction::PENDING) {
            $this->fleet->cancelSale($transaction);
        }

        $transaction->delete();

        return redirect()->route('admin.transactions.index')->with('success', 'Transaction supprimée.');
    }
}
