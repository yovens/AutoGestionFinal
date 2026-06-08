<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanCart;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoanController extends Controller
{
    public function index() {
        // Otomatikman tcheke si dat yo ekspire pandan n ap chaje paj la
        $loans = LoanCart::with('user','vehicle')->get();
        foreach($loans as $loan) {
            if ($loan->status === 'approved' && $loan->isExpired()) {
                $loan->status = 'expired';
                $loan->save();
            }
        }
        return view('admin.loans.index', compact('loans'));
    }

    // Paj detay Lokasyon an (Show)
    public function show($id) {
        $loan = LoanCart::with('user', 'vehicle')->findOrFail($id);
        
        // Si estati a te approved men dat la pase, nou mete l sou expired
        if ($loan->status === 'approved' && $loan->isExpired()) {
            $loan->status = 'expired';
            $loan->save();
        }

        return view('admin.loans.show', compact('loan'));
    }

    // Apwouve oswa Refize yon lokasyon
    public function update(Request $request, $id) {
        $loan = LoanCart::with('vehicle')->findOrFail($id);
        $status = $request->input('status');

        if ($status === 'approved') {
            $loan->start_date = Carbon::now();
            // 🔥 KORREKSYON: Fòse l tounen (int) pou Carbon pa bay erè
            $loan->end_date = Carbon::now()->addDays((int) $loan->duration_days);
            $loan->status = 'approved';
        } elseif ($status === 'rejected') {
            $loan->status = 'rejected';
            $loan->start_date = null;
            $loan->end_date = null;

            // Si nou refize l, nou remèt machin nan nan stock la
            $vehicle = $loan->vehicle;
            if ($vehicle) {
                $vehicle->quantity += 1;
                $vehicle->status = 1;
                $vehicle->save();
            }
        } else {
            $loan->status = $status;
        }

        $loan->save();
        return redirect()->route('admin.loans.index')->with('success', 'Statut mis à jour avec succès');
    }

    // Admin Renouvle Lokasyon an
    public function renew(Request $request, $id) {
        $loan = LoanCart::findOrFail($id);
        
        $request->validate([
            'additional_days' => 'required|integer|min:1'
        ]);

        $days = (int) $request->input('additional_days'); // 🔥 KORREKSYON: Konvèti an int
        $vehicle = $loan->vehicle;

        // Kalkile nouvo pri a
        $additional_price = $vehicle->loan_price * $days;

        $loan->duration_days += $days;
        $loan->total_amount += $additional_price;
        
        // Si li te fini deja, nou rekòmanse dat la jodi a, si l te aktif nou ajoute sou dat la
        if ($loan->status === 'expired' || !$loan->end_date) {
            $loan->start_date = Carbon::now();
            $loan->end_date = Carbon::now()->addDays($days);
        } else {
            $loan->end_date = Carbon::parse($loan->end_date)->addDays($days);
        }

        $loan->status = 'approved'; // Li tounen aktif/apwouve ankò
        $loan->save();

        return back()->with('success', "Location renouvelée de $days jours avec succès !");
    }

    public function destroy($id)
    {
        // Chèche lokasyon an ak ID a, si l pa jwenn li l ap bay erè 404 pwòp
        $loan = LoanCart::with('vehicle')->findOrFail($id);
        
        $vehicle = $loan->vehicle;
        if ($vehicle && $loan->status !== 'rejected') {
            $vehicle->quantity += 1;
            if($vehicle->quantity > 0) {
                $vehicle->status = 1;
            }
            $vehicle->save();
        }
        
        $loan->delete();
        
        return redirect()->route('admin.loans.index')->with('success', 'Location supprimée avec succès !');
    }

  public function store(Request $request)
{
    // ... (validation ou deja la) ...

    $vehicle = \App\Models\Vehicle::findOrFail($request->vehicle_id);

    // Tcheke stock
    if ($vehicle->quantity <= 0) {
        return redirect()->back()->with('error', 'Désolé, ce véhicule est épuisé.');
    }

    // DIMINYE STOCK LA
    $vehicle->quantity -= 1;
    if ($vehicle->quantity == 0) {
        $vehicle->status = 0; // Machin nan vin inaktif
    }
    $vehicle->save();

    // KREYE LOKASYON AN
    $loan = new LoanCart();
    $loan->user_id = $request->user_id;
    $loan->vehicle_id = $request->vehicle_id;
    $loan->duration_days = (int) $request->duration_days;
    $loan->total_amount = $vehicle->loan_price * $loan->duration_days;
    
    // 🔥 ASIRE W ESTATI A SE 'approved' ISIT LA
    $loan->status = 'approved'; 
    $loan->start_date = Carbon::now();
    $loan->end_date = Carbon::now()->addDays($loan->duration_days);
    
    $loan->save(); // Sove lokasyon an

    return redirect()->route('admin.loans.index')->with('success', 'Location créée avec succès.');
}

// Nan Admin\LoanController.php
public function approveRenewal(Request $request, $id)
{
    $loan = LoanCart::with('vehicle')->findOrFail($id);
    
    $inputDays = $request->input('additional_days');
    $days = (int)($inputDays ?: $loan->renewal_requested_days);

    if ($days < 1) {
        return back()->with('error', 'Le nombre de jours doit être supérieur à 0.');
    }

    // 1. Kalkile nouvo pri: 
    // Pri total = Pri pou chak jou * (Ansyen total jou + Nouvo jou)
    $vehiclePrice = $loan->vehicle->loan_price;
    $newTotalDays = $loan->duration_days + $days;
    $loan->total_amount = $vehiclePrice * $newTotalDays;

    // 2. Mete ajou dat ak dire
    $loan->end_date = Carbon::parse($loan->end_date)->addDays($days);
    $loan->duration_days = $newTotalDays;
    
    // 3. Reset demann lan epi sove
    $loan->renewal_requested_days = null;
    $loan->status = 'approved'; 
    $loan->save();

    return back()->with('success', "Renouvellement approuvé. Nouveau total : " . $loan->total_amount . " USD");
}
}