<?php

namespace App\Http\Controllers\Counter;

use App\Http\Controllers\Controller;
use App\Models\LoanCart;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /** Ekran prensipal comptoir la: rechèch + sa ki gen pou fèt jodi a. */
    public function index(Request $request)
    {
        LoanCart::syncExpired();

        $with = ['user', 'vehicle'];

        $queues = [
            // Kliyan ki dwe vin pran kle (konfime, poko remèt)
            'pickups'   => LoanCart::with($with)->where('status', LoanCart::READY)->oldest('updated_at')->get(),
            // Machin ki dwe retounen jodi a oswa ki an reta
            'returns'   => LoanCart::with($with)->whereIn('status', [LoanCart::APPROVED, LoanCart::PENDING_RENEWAL])
                                ->whereDate('end_date', '<=', Carbon::today())->orderBy('end_date')->get(),
            'late'      => LoanCart::with($with)->where('status', LoanCart::EXPIRED)->orderBy('end_date')->get(),
            // Demann sou sit la pou konfime
            'requests'  => LoanCart::with($with)->where('status', LoanCart::PENDING)->oldest()->get(),
            'renewals'  => LoanCart::with($with)->where('status', LoanCart::PENDING_RENEWAL)->oldest('updated_at')->get(),
            // Acha pou ankese
            'sales'     => Transaction::with($with)->where('type', Transaction::SALE)->where('status', Transaction::PENDING)->oldest()->get(),
        ];

        $me = $request->user();
        $today = Transaction::completed()->whereDate('updated_at', Carbon::today())->where('recorded_by', $me->id);

        $stats = [
            'pickups'    => $queues['pickups']->count(),
            'returns'    => $queues['returns']->count() + $queues['late']->count(),
            'out'        => LoanCart::whereIn('status', LoanCart::OUT)->count(),
            'cashToday'  => (clone $today)->sum('amount'),
            'opsToday'   => (clone $today)->count(),
        ];

        return view('counter.index', compact('queues', 'stats'));
    }

    /**
     * Rechèch inivèsèl: kòd retrè, plak, non, telefòn, e-mail.
     * Si se yon kòd retrè ki egziste, nou ale dirèk sou remiz kle a.
     */
    public function search(Request $request)
    {
        $q = trim((string) $request->query('q'));

        if ($q === '') {
            return redirect()->route('counter.index');
        }

        $code = LoanCart::normalizeCode($q);
        if ($loan = LoanCart::where('pickup_code', $code)->first()) {
            return $loan->status === LoanCart::READY
                ? redirect()->route('counter.loans.checkout', $loan)
                : redirect()->route('counter.loans.show', $loan)->with('success', "Code {$code} : {$loan->status_label}.");
        }

        $like = "%{$q}%";

        $loans = LoanCart::with('user', 'vehicle')
            ->where(function ($w) use ($like, $q) {
                $w->whereHas('vehicle', fn ($v) => $v->where('plate', 'like', $like)->orWhere('model', 'like', $like)->orWhere('brand', 'like', $like))
                  ->orWhereHas('user', fn ($u) => $u->where('nom', 'like', $like)->orWhere('telephone', 'like', $like)->orWhere('email', 'like', $like));
                if (ctype_digit(ltrim(strtoupper($q), 'LOC-'))) {
                    $w->orWhere('id', (int) ltrim(strtoupper($q), 'LOC-'));
                }
            })
            ->orderByRaw("CASE WHEN status IN ('ready','approved','expired','pending_renewal','pending') THEN 0 ELSE 1 END")
            ->latest()->take(30)->get();

        $clients = User::where('role', User::CLIENT)
            ->where(fn ($w) => $w->where('nom', 'like', $like)->orWhere('telephone', 'like', $like)->orWhere('email', 'like', $like))
            ->withCount('loans')->orderBy('nom')->take(10)->get();

        return view('counter.search', compact('q', 'loans', 'clients'));
    }
}
