<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\FleetService;
use App\Support\Audit;
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
            ->when(array_key_exists((string) $type, Transaction::TYPE_LABELS), fn ($q) => $q->where('type', $type))
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
            'type'       => ['required', Rule::in(array_keys(Transaction::TYPE_LABELS))],
            'method'     => ['required', Rule::in(array_keys(\App\Support\Agency::methods()))],
            'note'       => 'nullable|string|max:255',
        ], [], ['user_id' => 'client', 'vehicle_id' => 'véhicule', 'amount' => 'montant', 'method' => 'mode de paiement']);

        $t = Transaction::create($data + ['status' => Transaction::COMPLETED, 'recorded_by' => $request->user()->id]);
        Audit::log('payment.create', "Paiement saisi {$t->reference} : " . \App\Providers\AppServiceProvider::money($t->amount) . " ({$t->type_label}, {$t->method_label})", $t);

        return redirect()->route('admin.transactions.index')->with('success', 'Paiement enregistré.');
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('user', 'vehicle', 'loan', 'recorder', 'tickets');

        return view('admin.transactions.show', compact('transaction'));
    }



    /** Efase yon peman: super admin sèlman (lajan pa dwe ka disparèt san kontwòl). */
    public function destroy(Request $request, Transaction $transaction)
    {
        abort_unless($request->user()->isSuperAdmin(), 403, 'Seul le super administrateur peut supprimer un paiement.');

        // Yon acha ki t ap tann: lage machin nan anvan n efase.
        if ($transaction->type === Transaction::SALE && $transaction->status === Transaction::PENDING) {
            $this->fleet->cancelSale($transaction);
        }

        Audit::log('payment.delete', "Paiement supprimé {$transaction->reference} : " . \App\Providers\AppServiceProvider::money($transaction->amount) . " ({$transaction->type_label}, {$transaction->status_label})", null, $transaction->only(['id', 'user_id', 'vehicle_id', 'amount', 'type', 'status', 'method']));
        $transaction->delete();

        return redirect()->route('admin.transactions.index')->with('success', 'Transaction supprimée.');
    }
}
