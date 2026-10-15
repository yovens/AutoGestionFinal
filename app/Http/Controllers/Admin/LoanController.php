<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanCart;
use App\Models\User;
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

        $filter = $request->query('status');

        $loans = LoanCart::with('user', 'vehicle')
            ->when($filter === 'active', fn ($q) => $q->whereIn('status', [LoanCart::APPROVED, LoanCart::EXPIRED, LoanCart::PENDING_RENEWAL]))
            ->when($filter && $filter !== 'active', fn ($q) => $q->where('status', $filter))
            ->latest()->get();

        $counts = LoanCart::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        $clients = User::where('role', User::CLIENT)->where('status', User::ACTIVE)->orderBy('nom')->get(['id', 'nom', 'email']);
        $vehicles = Vehicle::available()->orderBy('brand')->get();

        return view('admin.loans.index', compact('loans', 'counts', 'filter', 'clients', 'vehicles'));
    }

    public function show(LoanCart $loan)
    {
        LoanCart::syncExpired();
        $loan->refresh()->load('user', 'vehicle', 'transactions');

        return view('admin.loans.show', compact('loan'));
    }

    /** Lokasyon kreye nan biwo a: li kòmanse touswit epi peman an anrejistre. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'       => 'required|exists:users,id',
            'vehicle_id'    => 'required|exists:vehicles,id',
            'duration_days' => 'required|integer|min:1|max:' . FleetService::MAX_DAYS,
        ], [], ['user_id' => 'client', 'vehicle_id' => 'véhicule', 'duration_days' => 'durée']);

        $loan = $this->fleet->createApprovedLoan($data['user_id'], $data['vehicle_id'], (int) $data['duration_days']);

        return redirect()->route('admin.loans.show', $loan)->with('success', 'Location créée et paiement enregistré.');
    }

    public function approve(LoanCart $loan)
    {
        $this->fleet->approveLoan($loan);

        return back()->with('success', 'Location approuvée. Le paiement a été ajouté aux transactions.');
    }

    public function reject(LoanCart $loan)
    {
        $this->fleet->rejectLoan($loan);

        return back()->with('success', 'Demande refusée. Le véhicule est de nouveau disponible.');
    }

    /** Admin nan prolonje dirèkteman. */
    public function extend(Request $request, LoanCart $loan)
    {
        $data = $request->validate([
            'additional_days' => 'required|integer|min:1|max:' . FleetService::MAX_DAYS,
        ], [], ['additional_days' => 'nombre de jours']);

        $this->fleet->extendLoan($loan, (int) $data['additional_days']);

        return back()->with('success', "Location prolongée de {$data['additional_days']} jour(s).");
    }

    /** Aksepte demann prolongasyon kliyan an (admin nan ka chanje kantite jou a). */
    public function approveRenewal(Request $request, LoanCart $loan)
    {
        $data = $request->validate([
            'additional_days' => 'nullable|integer|min:1|max:' . FleetService::MAX_DAYS,
        ]);

        $days = (int) ($data['additional_days'] ?? $loan->renewal_requested_days);

        $this->fleet->extendLoan($loan, $days);

        return back()->with('success', "Prolongation de {$days} jour(s) acceptée.");
    }

    public function declineRenewal(LoanCart $loan)
    {
        $this->fleet->declineRenewal($loan);

        return back()->with('success', 'Demande de prolongation refusée.');
    }

    public function markReturned(LoanCart $loan)
    {
        $this->fleet->returnLoan($loan);

        return back()->with('success', 'Retour enregistré. Le véhicule est de nouveau disponible.');
    }

    public function destroy(LoanCart $loan)
    {
        $this->fleet->deleteLoan($loan);

        return redirect()->route('admin.loans.index')->with('success', 'Location supprimée.');
    }
}
