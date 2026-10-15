<?php

namespace App\Http\Controllers\Counter;

use App\Http\Controllers\Controller;
use App\Models\LoanCart;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\FleetService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LoanController extends Controller
{
    public function __construct(private FleetService $fleet)
    {
    }

    public function index(Request $request)
    {
        LoanCart::syncExpired();

        $filter = $request->query('status');

        $loans = LoanCart::with('user', 'vehicle')
            ->when($filter === 'out', fn ($q) => $q->whereIn('status', LoanCart::OUT))
            ->when($filter && $filter !== 'out', fn ($q) => $q->where('status', $filter))
            ->latest()->get();

        $counts = LoanCart::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('counter.loans.index', compact('loans', 'counts', 'filter'));
    }

    /** Fòm "Nouvelle location" pou yon kliyan ki la nan ajans lan. */
    public function create(Request $request)
    {
        $clients = User::where('role', User::CLIENT)->where('status', User::ACTIVE)->orderBy('nom')->get(['id', 'nom', 'email', 'telephone']);
        $vehicles = Vehicle::available()->orderBy('brand')->orderBy('model')->get();

        return view('counter.loans.create', [
            'clients'  => $clients,
            'vehicles' => $vehicles,
            'selected' => $request->query('client'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'       => ['required', Rule::exists('users', 'id')->where('role', User::CLIENT)],
            'vehicle_id'    => 'required|exists:vehicles,id',
            'duration_days' => 'required|integer|min:1|max:' . FleetService::MAX_DAYS,
        ], [], ['user_id' => 'client', 'vehicle_id' => 'véhicule', 'duration_days' => 'durée']);

        $loan = $this->fleet->createCounterLoan($data['user_id'], $data['vehicle_id'], (int) $data['duration_days']);

        return redirect()->route('counter.loans.checkout', $loan)->with('success', 'Location créée. Complétez la remise des clés.');
    }

    public function show(LoanCart $loan)
    {
        LoanCart::syncExpired();
        $loan->refresh()->load('user', 'vehicle', 'transactions.recorder', 'tickets', 'checkoutAgent', 'checkinAgent');

        return view('counter.loans.show', compact('loan'));
    }

    public function approve(LoanCart $loan)
    {
        $loan = $this->fleet->approveLoan($loan);

        return back()->with('success', "Location confirmée. Code de retrait du client : {$loan->pickup_code}.");
    }

    public function reject(LoanCart $loan)
    {
        $this->fleet->rejectLoan($loan);

        return back()->with('success', 'Demande refusée. Le véhicule est de nouveau disponible.');
    }

    public function cancel(LoanCart $loan)
    {
        $this->fleet->cancelReady($loan);

        return back()->with('success', 'Location annulée. Le véhicule est de nouveau disponible.');
    }

    /* ---------- Remiz kle ---------- */

    public function checkoutForm(LoanCart $loan)
    {
        $loan->load('user', 'vehicle');

        if ($loan->status !== LoanCart::READY) {
            return redirect()->route('counter.loans.show', $loan)->with('error', 'Cette location n’attend pas de remise des clés (' . mb_strtolower($loan->status_label) . ').');
        }

        $lastKm = LoanCart::where('vehicle_id', $loan->vehicle_id)->whereNotNull('km_in')->latest('returned_at')->value('km_in');

        return view('counter.loans.checkout', compact('loan', 'lastKm'));
    }

    public function checkout(Request $request, LoanCart $loan)
    {
        $data = $request->validate([
            'id_checked'    => 'accepted',
            'id_document'   => 'nullable|string|max:60',
            'km_out'        => 'required|integer|min:0|max:2000000',
            'fuel_out'      => 'required|integer|between:0,4',
            'condition_out' => 'nullable|string|max:1000',
            'deposit'       => 'nullable|integer|min:0',
            'method'        => ['required', Rule::in(array_keys(Transaction::METHODS))],
        ], [
            'id_checked.accepted' => 'Confirmez que la pièce d’identité du client a été vérifiée.',
        ], [
            'km_out' => 'kilométrage', 'fuel_out' => 'carburant', 'method' => 'mode de paiement', 'deposit' => 'caution',
        ]);

        $data['deposit'] = (int) ($data['deposit'] ?? 0);
        $ticket = $this->fleet->checkout($loan, $request->user(), $data);

        return redirect()->route('counter.tickets.show', $ticket)->with('success', 'Clés remises. Imprimez le bon de sortie et faites-le signer.');
    }

    /* ---------- Retou ---------- */

    public function checkinForm(LoanCart $loan)
    {
        LoanCart::syncExpired();
        $loan->refresh()->load('user', 'vehicle', 'transactions');

        if (!in_array($loan->status, LoanCart::OUT, true)) {
            return redirect()->route('counter.loans.show', $loan)->with('error', 'Ce véhicule n’est pas sorti : rien à réceptionner.');
        }

        $unpaid = $loan->transactions->where('status', Transaction::PENDING);

        return view('counter.loans.checkin', compact('loan', 'unpaid'));
    }

    public function checkin(Request $request, LoanCart $loan)
    {
        $data = $request->validate([
            'km_in'            => 'required|integer|min:0|max:2000000',
            'fuel_in'          => 'required|integer|between:0,4',
            'condition_in'     => 'nullable|string|max:1000',
            'late_fee'         => 'nullable|integer|min:0',
            'extra_fee'        => 'nullable|integer|min:0',
            'extra_reason'     => 'nullable|string|max:120',
            'deposit_returned' => 'nullable|boolean',
            'method'           => ['nullable', Rule::in(array_keys(Transaction::METHODS))],
        ], [], [
            'km_in' => 'kilométrage', 'fuel_in' => 'carburant', 'late_fee' => 'frais de retard',
            'extra_fee' => 'autres frais', 'extra_reason' => 'motif', 'method' => 'mode de paiement',
        ]);

        $ticket = $this->fleet->checkin($loan, $request->user(), [
            'km_in'            => (int) $data['km_in'],
            'fuel_in'          => (int) $data['fuel_in'],
            'condition_in'     => $data['condition_in'] ?? null,
            'late_fee'         => (int) ($data['late_fee'] ?? 0),
            'extra_fee'        => (int) ($data['extra_fee'] ?? 0),
            'extra_reason'     => $data['extra_reason'] ?? null,
            'deposit_returned' => $request->boolean('deposit_returned'),
            'method'           => $data['method'] ?? null,
        ]);

        return redirect()->route('counter.tickets.show', $ticket)->with('success', 'Retour enregistré. Le véhicule est de nouveau disponible.');
    }

    /* ---------- Prolongasyon ---------- */

    /** Prolongasyon nan comptoir la: sipleman an ankese touswit. */
    public function extend(Request $request, LoanCart $loan)
    {
        $data = $request->validate([
            'additional_days' => 'required|integer|min:1|max:' . FleetService::MAX_DAYS,
            'method'          => ['required', Rule::in(array_keys(Transaction::METHODS))],
        ], [], ['additional_days' => 'nombre de jours', 'method' => 'mode de paiement']);

        $this->fleet->extendLoan($loan, (int) $data['additional_days'], $request->user(), $data['method']);

        return back()->with('success', "Location prolongée de {$data['additional_days']} jour(s), supplément encaissé.");
    }

    /** Aksepte demann kliyan an fè sou sit la: sipleman an ap ankese nan retou a. */
    public function approveRenewal(Request $request, LoanCart $loan)
    {
        $days = (int) $loan->renewal_requested_days;
        $this->fleet->extendLoan($loan, $days, $request->user());

        return back()->with('success', "Prolongation de {$days} jour(s) acceptée. Le supplément sera encaissé au retour.");
    }

    public function declineRenewal(LoanCart $loan)
    {
        $this->fleet->declineRenewal($loan);

        return back()->with('success', 'Demande de prolongation refusée.');
    }

    public function destroy(Request $request, LoanCart $loan)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $this->fleet->deleteLoan($loan);

        return redirect()->route('counter.loans.index')->with('success', 'Location supprimée.');
    }
}
