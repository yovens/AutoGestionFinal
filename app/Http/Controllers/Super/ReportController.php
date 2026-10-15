<?php

namespace App\Http\Controllers\Super;

use App\Http\Controllers\Controller;
use App\Models\LoanCart;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Rapò finansye pou pwopriyetè a. Lajan konte selon dat li antre (updated_at lè peman an "réglée").
 */
class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->period($request);

        $paid = Transaction::with('vehicle', 'recorder')->completed()->whereBetween('updated_at', [$from, $to])->get();

        $totals = [
            'revenue' => $paid->sum('amount'),
            'rentals' => $paid->where('type', Transaction::RENTAL)->sum('amount'),
            'sales'   => $paid->where('type', Transaction::SALE)->sum('amount'),
            'fees'    => $paid->where('type', Transaction::FEE)->sum('amount'),
            'count'   => $paid->count(),
        ];

        $byMethod = collect(Transaction::METHODS)->map(fn ($label, $key) => [
            'label' => $label,
            'total' => $paid->where('method', $key)->sum('amount'),
            'count' => $paid->where('method', $key)->count(),
        ])->put('none', ['label' => 'Non précisé', 'total' => $paid->whereNull('method')->sum('amount'), 'count' => $paid->whereNull('method')->count()])
          ->filter(fn ($m) => $m['count'] > 0);

        $byAgent = $paid->groupBy('recorded_by')->map(fn ($rows) => [
            'name'  => $rows->first()->recorder?->nom ?? 'En ligne / non attribué',
            'role'  => $rows->first()->recorder?->role_label,
            'total' => $rows->sum('amount'),
            'count' => $rows->count(),
        ])->sortByDesc('total')->values();

        // Machin ki rapòte plis + jou lokasyon nan peryòd la
        $rentalDays = LoanCart::whereNotNull('start_date')
            ->where('start_date', '<=', $to)->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $from))
            ->get(['vehicle_id', 'start_date', 'end_date', 'returned_at'])
            ->groupBy('vehicle_id')
            ->map(fn ($loans) => $loans->sum(function ($l) use ($from, $to) {
                $start = $l->start_date->max($from);
                $end = ($l->returned_at ?? $l->end_date ?? $to)->copy()->min($to);

                return max(0, (int) $start->diffInDays($end));
            }));

        $byVehicle = $paid->groupBy('vehicle_id')->map(fn ($rows, $vid) => [
            'vehicle' => $rows->first()->vehicle,
            'total'   => $rows->sum('amount'),
            'days'    => $rentalDays[$vid] ?? 0,
        ])->sortByDesc('total')->take(10)->values();

        // 12 dènye mwa yo (pa depann de peryòd la)
        $months = collect(range(11, 0))->map(function ($i) {
            $start = now()->startOfMonth()->subMonths($i);
            $q = Transaction::completed()->whereBetween('updated_at', [$start, $start->copy()->endOfMonth()]);

            return ['label' => ucfirst($start->locale('fr')->isoFormat('MMM YY')), 'total' => (int) $q->sum('amount')];
        });

        $fleet = [
            'vehicles' => Vehicle::count(),
            'clients'  => User::where('role', User::CLIENT)->count(),
            'out'      => LoanCart::whereIn('status', LoanCart::OUT)->count(),
            'late'     => LoanCart::where('status', LoanCart::EXPIRED)->count(),
        ];

        return view('super.reports', compact('from', 'to', 'totals', 'byMethod', 'byAgent', 'byVehicle', 'months', 'fleet'));
    }

    /** Ekspòte peman peryòd la an CSV (louvri dirèk nan Excel). */
    public function export(Request $request)
    {
        [$from, $to] = $this->period($request);

        $rows = Transaction::with('user', 'vehicle', 'recorder')->completed()
            ->whereBetween('updated_at', [$from, $to])->orderBy('updated_at')->get();

        $filename = 'autogestion-paiements-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM pou Excel li aksan yo byen
            fputcsv($out, ['Date', 'Référence', 'Type', 'Détail', 'Client', 'Véhicule', 'Plaque', 'Mode', 'Agent', 'Montant'], ';');
            foreach ($rows as $t) {
                fputcsv($out, [
                    $t->updated_at->format('d/m/Y H:i'), $t->reference, $t->type_label, $t->note,
                    $t->user?->nom, $t->vehicle?->name, $t->vehicle?->plate, $t->method_label,
                    $t->recorder?->nom, $t->amount,
                ], ';');
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function period(Request $request): array
    {
        try {
            $from = $request->filled('from') ? Carbon::parse($request->query('from'))->startOfDay() : now()->startOfMonth();
            $to = $request->filled('to') ? Carbon::parse($request->query('to'))->endOfDay() : now()->endOfDay();
        } catch (\Throwable) {
            [$from, $to] = [now()->startOfMonth(), now()->endOfDay()];
        }

        return $from->gt($to) ? [$to->copy()->startOfDay(), $from->copy()->endOfDay()] : [$from, $to];
    }
}
