<?php

namespace App\Services;

use App\Exceptions\FleetException;
use App\Models\LoanCart;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Tout règ biznis ki touche stock machin yo, lokasyon, vant ak comptoir la.
 * Admin, sekretè ak kliyan pase isit la pou lojik la rete menm.
 *
 * Sik yon lokasyon:
 *   pending (demann) → ready (konfime, kòd retrè) → approved (kle remèt, peman fèt)
 *   → expired (reta) / pending_renewal → returned (machin tounen, stock +1)
 *   pending → rejected,  ready → cancelled  (stock +1)
 *
 * Chak metòd ki ka echwe voye yon FleetException ak yon mesaj pou itilizatè a.
 */
class FleetService
{
    public const MAX_DAYS = 365;

    /* ------------------------------------------------------------------ */
    /*  Demann ak konfimasyon                                              */
    /* ------------------------------------------------------------------ */

    /** Kliyan an mande yon lokasyon sou sit la: machin nan rezève. */
    public function requestLoan(User $user, int $vehicleId, int $days): LoanCart
    {
        $this->assertDays($days);

        return DB::transaction(function () use ($user, $vehicleId, $days) {
            $vehicle = Vehicle::lockForUpdate()->findOrFail($vehicleId);
            $vehicle->takeOne();

            return LoanCart::create([
                'user_id'       => $user->id,
                'vehicle_id'    => $vehicle->id,
                'status'        => LoanCart::PENDING,
                'duration_days' => $days,
                'total_amount'  => $vehicle->loan_price * $days,
            ]);
        });
    }

    /**
     * Lokasyon kreye nan comptoir la pou yon kliyan ki la a (san rezèvasyon).
     * Li konfime touswit; sekretè a ale dirèk sou remiz kle a.
     */
    public function createCounterLoan(int $userId, int $vehicleId, int $days): LoanCart
    {
        $this->assertDays($days);

        return DB::transaction(function () use ($userId, $vehicleId, $days) {
            $vehicle = Vehicle::lockForUpdate()->findOrFail($vehicleId);
            $vehicle->takeOne();

            return LoanCart::create([
                'user_id'       => $userId,
                'vehicle_id'    => $vehicle->id,
                'status'        => LoanCart::READY,
                'pickup_code'   => LoanCart::newPickupCode(),
                'duration_days' => $days,
                'total_amount'  => $vehicle->loan_price * $days,
            ]);
        });
    }

    /** Ajans lan konfime demann lan: kliyan an resevwa yon kòd retrè. */
    public function approveLoan(LoanCart $loan): LoanCart
    {
        return DB::transaction(function () use ($loan) {
            $loan = LoanCart::lockForUpdate()->findOrFail($loan->id);

            if ($loan->status !== LoanCart::PENDING) {
                throw new FleetException('Seule une demande en attente peut être confirmée.');
            }

            $loan->update(['status' => LoanCart::READY, 'pickup_code' => LoanCart::newPickupCode()]);

            return $loan;
        });
    }

    public function rejectLoan(LoanCart $loan): void
    {
        DB::transaction(function () use ($loan) {
            $loan = LoanCart::lockForUpdate()->findOrFail($loan->id);

            if ($loan->status !== LoanCart::PENDING) {
                throw new FleetException('Seule une demande en attente peut être refusée.');
            }

            $loan->update(['status' => LoanCart::REJECTED]);
            $this->releaseVehicle($loan);
        });
    }

    /** Kliyan an pa janm vin pran machin nan: nou lage l. */
    public function cancelReady(LoanCart $loan): void
    {
        DB::transaction(function () use ($loan) {
            $loan = LoanCart::lockForUpdate()->findOrFail($loan->id);

            if ($loan->status !== LoanCart::READY) {
                throw new FleetException('Seule une location confirmée et non récupérée peut être annulée.');
            }

            $loan->update(['status' => LoanCart::CANCELLED]);
            $this->releaseVehicle($loan);
        });
    }

    /* ------------------------------------------------------------------ */
    /*  Comptoir: remiz kle ak retou                                       */
    /* ------------------------------------------------------------------ */

    /**
     * Remiz kle: sekretè a verifye kliyan an, note eta machin nan, ankese, epi lokasyon an kòmanse.
     *
     * @param array{km_out:int, fuel_out:int, condition_out:?string, id_document:?string, deposit:int, method:string} $data
     */
    public function checkout(LoanCart $loan, User $agent, array $data): Ticket
    {
        return DB::transaction(function () use ($loan, $agent, $data) {
            $loan = LoanCart::with('vehicle', 'user')->lockForUpdate()->findOrFail($loan->id);

            if ($loan->status !== LoanCart::READY) {
                throw new FleetException('Cette location n’est pas prête pour la remise des clés.');
            }

            $loan->update([
                'status'        => LoanCart::APPROVED,
                'start_date'    => Carbon::today(),
                'end_date'      => Carbon::today()->addDays($loan->duration_days),
                'picked_up_at'  => now(),
                'checkout_by'   => $agent->id,
                'km_out'        => $data['km_out'],
                'fuel_out'      => $data['fuel_out'],
                'condition_out' => $data['condition_out'] ?? null,
                'id_document'   => $data['id_document'] ?? null,
                'deposit'       => $data['deposit'] ?? 0,
            ]);

            $payment = $this->recordPayment($loan, Transaction::RENTAL, $loan->total_amount, $agent, $data['method'], 'Location ' . $loan->duration_days . ' jour(s)');

            return Ticket::create([
                'kind'           => Ticket::CHECKOUT,
                'loan_cart_id'   => $loan->id,
                'transaction_id' => $payment->id,
                'vehicle_id'     => $loan->vehicle_id,
                'user_id'        => $loan->user_id,
                'issued_by'      => $agent->id,
                'amount'         => $loan->total_amount,
                'data'           => [
                    'reference'  => $loan->reference,
                    'start'      => $loan->start_date->toDateString(),
                    'end'        => $loan->end_date->toDateString(),
                    'days'       => $loan->duration_days,
                    'per_day'    => $loan->vehicle->loan_price,
                    'km'         => $loan->km_out,
                    'fuel'       => $loan->fuel_out,
                    'condition'  => $loan->condition_out,
                    'deposit'    => $loan->deposit,
                    'method'     => $data['method'],
                    'lines'      => [['label' => 'Location ' . $loan->duration_days . ' jour(s) × ' . \App\Providers\AppServiceProvider::money($loan->vehicle->loan_price), 'amount' => $loan->total_amount]],
                ],
            ]);
        });
    }

    /**
     * Retou machin nan: sekretè a note km/kabiran/eta, kalkile frè yo, ankese sa ki rete, remonte stock la.
     *
     * @param array{km_in:int, fuel_in:int, condition_in:?string, late_fee:int, extra_fee:int, extra_reason:?string, deposit_returned:bool, method:?string} $data
     */
    public function checkin(LoanCart $loan, User $agent, array $data): Ticket
    {
        return DB::transaction(function () use ($loan, $agent, $data) {
            $loan = LoanCart::with('vehicle', 'user')->lockForUpdate()->findOrFail($loan->id);

            if (!in_array($loan->status, LoanCart::OUT, true)) {
                throw new FleetException('Ce véhicule n’est pas sorti : rien à réceptionner.');
            }

            if ($loan->km_out !== null && $data['km_in'] < $loan->km_out) {
                throw new FleetException("Le kilométrage de retour ({$data['km_in']}) est inférieur à celui du départ ({$loan->km_out}).");
            }

            $lines = [];
            $late = max(0, (int) $data['late_fee']);
            $extra = max(0, (int) $data['extra_fee']);

            // Prolongasyon ki te apwouve men ki poko peye
            $unpaid = $loan->transactions()->where('status', Transaction::PENDING)->get();
            foreach ($unpaid as $t) {
                $lines[] = ['label' => $t->note ?: 'Prolongation', 'amount' => $t->amount];
            }
            if ($late > 0) {
                $lines[] = ['label' => 'Retard : ' . $loan->daysLate() . ' jour(s)', 'amount' => $late];
            }
            if ($extra > 0) {
                $lines[] = ['label' => $data['extra_reason'] ?: 'Frais supplémentaires', 'amount' => $extra];
            }

            $due = array_sum(array_column($lines, 'amount'));

            if ($due > 0 && empty($data['method'])) {
                throw new FleetException('Choisissez le mode de paiement du solde à encaisser.');
            }

            foreach ($unpaid as $t) {
                $t->update(['status' => Transaction::COMPLETED, 'method' => $data['method'], 'recorded_by' => $agent->id]);
            }
            if ($late > 0) {
                $this->recordPayment($loan, Transaction::FEE, $late, $agent, $data['method'], 'Retard ' . $loan->daysLate() . ' jour(s)');
            }
            if ($extra > 0) {
                $this->recordPayment($loan, Transaction::FEE, $extra, $agent, $data['method'], $data['extra_reason'] ?: 'Frais supplémentaires');
            }

            $daysLate = $loan->daysLate();

            $loan->update([
                'status'                 => LoanCart::RETURNED,
                'returned_at'            => now(),
                'checkin_by'             => $agent->id,
                'km_in'                  => $data['km_in'],
                'fuel_in'                => $data['fuel_in'],
                'condition_in'           => $data['condition_in'] ?? null,
                'deposit_returned'       => (bool) ($data['deposit_returned'] ?? false),
                'renewal_requested_days' => null,
                'total_amount'           => $loan->total_amount + $late + $extra,
            ]);

            $this->releaseVehicle($loan);

            return Ticket::create([
                'kind'         => Ticket::CHECKIN,
                'loan_cart_id' => $loan->id,
                'vehicle_id'   => $loan->vehicle_id,
                'user_id'      => $loan->user_id,
                'issued_by'    => $agent->id,
                'amount'       => $due,
                'data'         => [
                    'reference'        => $loan->reference,
                    'start'            => $loan->start_date?->toDateString(),
                    'end'              => $loan->end_date?->toDateString(),
                    'returned'         => now()->toDateTimeString(),
                    'days_late'        => $daysLate,
                    'km_out'           => $loan->km_out,
                    'km_in'            => $loan->km_in,
                    'fuel_out'         => $loan->fuel_out,
                    'fuel_in'          => $loan->fuel_in,
                    'condition'        => $loan->condition_in,
                    'deposit'          => $loan->deposit,
                    'deposit_returned' => $loan->deposit_returned,
                    'method'           => $due > 0 ? $data['method'] : null,
                    'lines'            => $lines,
                ],
            ]);
        });
    }

    /* ------------------------------------------------------------------ */
    /*  Prolongasyon                                                       */
    /* ------------------------------------------------------------------ */

    /** Kliyan an mande pou l kenbe machin nan plis jou. */
    public function requestRenewal(LoanCart $loan, int $days): void
    {
        $this->assertDays($days);

        if (!$loan->canRequestRenewal()) {
            throw new FleetException('Cette location ne peut pas être prolongée pour le moment.');
        }

        $loan->update([
            'status'                 => LoanCart::PENDING_RENEWAL,
            'renewal_requested_days' => $days,
        ]);
    }

    /**
     * Prolonje yon lokasyon. Si yon mòd peman bay (kliyan an la nan comptoir la), sipleman an ankese touswit;
     * sinon li rete "à encaisser" e sekretè a ap ankese l nan retou a.
     */
    public function extendLoan(LoanCart $loan, int $days, ?User $agent = null, ?string $method = null): void
    {
        $this->assertDays($days);

        DB::transaction(function () use ($loan, $days, $agent, $method) {
            $loan = LoanCart::with('vehicle')->lockForUpdate()->findOrFail($loan->id);

            if (!in_array($loan->status, LoanCart::OUT, true)) {
                throw new FleetException('Seule une location en cours peut être prolongée.');
            }

            $extra = $loan->vehicle->loan_price * $days;
            $base = ($loan->end_date && $loan->end_date->gte(Carbon::today())) ? $loan->end_date->copy() : Carbon::today();

            $loan->update([
                'status'                 => LoanCart::APPROVED,
                'duration_days'          => $loan->duration_days + $days,
                'total_amount'           => $loan->total_amount + $extra,
                'end_date'               => $base->addDays($days),
                'renewal_requested_days' => null,
            ]);

            $note = "Prolongation {$days} jour(s)";

            if ($method) {
                $this->recordPayment($loan, Transaction::RENTAL, $extra, $agent, $method, $note);
            } else {
                Transaction::create([
                    'user_id' => $loan->user_id, 'vehicle_id' => $loan->vehicle_id, 'loan_cart_id' => $loan->id,
                    'amount' => $extra, 'type' => Transaction::RENTAL, 'status' => Transaction::PENDING,
                    'recorded_by' => $agent?->id, 'note' => $note,
                ]);
            }
        });
    }

    public function declineRenewal(LoanCart $loan): void
    {
        if ($loan->status !== LoanCart::PENDING_RENEWAL) {
            throw new FleetException('Aucune prolongation en attente pour cette location.');
        }

        $loan->update([
            'status'                 => $loan->isExpired() ? LoanCart::EXPIRED : LoanCart::APPROVED,
            'renewal_requested_days' => null,
        ]);
    }

    public function deleteLoan(LoanCart $loan): void
    {
        DB::transaction(function () use ($loan) {
            $loan = LoanCart::lockForUpdate()->findOrFail($loan->id);

            if (in_array($loan->status, LoanCart::HOLDING, true)) {
                $this->releaseVehicle($loan);
            }

            $loan->delete();
        });
    }

    /* ------------------------------------------------------------------ */
    /*  Vant                                                               */
    /* ------------------------------------------------------------------ */

    /** Kliyan an rezève yon acha: machin nan bloke, peman an "à encaisser". */
    public function requestPurchase(User $user, int $vehicleId): Transaction
    {
        return DB::transaction(function () use ($user, $vehicleId) {
            $vehicle = Vehicle::lockForUpdate()->findOrFail($vehicleId);
            $vehicle->takeOne();

            return Transaction::create([
                'user_id'    => $user->id,
                'vehicle_id' => $vehicle->id,
                'amount'     => $vehicle->price,
                'type'       => Transaction::SALE,
                'status'     => Transaction::PENDING,
            ]);
        });
    }

    /** Peman vant lan resevwa nan comptoir la: machin nan remèt, bon de remise enprime. */
    public function confirmSale(Transaction $transaction, ?User $agent = null, ?string $method = null): ?Ticket
    {
        return DB::transaction(function () use ($transaction, $agent, $method) {
            $transaction = Transaction::with('vehicle')->lockForUpdate()->findOrFail($transaction->id);

            if ($transaction->status !== Transaction::PENDING) {
                throw new FleetException('Cette opération a déjà été traitée.');
            }

            $transaction->update(['status' => Transaction::COMPLETED, 'method' => $method, 'recorded_by' => $agent?->id]);

            if ($transaction->type !== Transaction::SALE || !$agent) {
                return null;
            }

            return Ticket::create([
                'kind'           => Ticket::SALE,
                'transaction_id' => $transaction->id,
                'vehicle_id'     => $transaction->vehicle_id,
                'user_id'        => $transaction->user_id,
                'issued_by'      => $agent->id,
                'amount'         => $transaction->amount,
                'data'           => [
                    'reference' => $transaction->reference,
                    'method'    => $method,
                    'lines'     => [['label' => 'Achat ' . ($transaction->vehicle?->name ?? 'véhicule'), 'amount' => $transaction->amount]],
                ],
            ]);
        });
    }

    /** Anile yon acha ki poko peye: machin nan retounen nan stock. */
    public function cancelSale(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $transaction = Transaction::lockForUpdate()->findOrFail($transaction->id);

            if ($transaction->status !== Transaction::PENDING || $transaction->type !== Transaction::SALE) {
                throw new FleetException('Seul un achat en attente peut être annulé.');
            }

            $transaction->update(['status' => Transaction::CANCELLED]);

            if ($vehicle = Vehicle::lockForUpdate()->find($transaction->vehicle_id)) {
                $vehicle->releaseOne();
            }
        });
    }

    /* ------------------------------------------------------------------ */

    private function recordPayment(LoanCart $loan, string $type, int $amount, ?User $agent, ?string $method, ?string $note = null): Transaction
    {
        return Transaction::create([
            'user_id'      => $loan->user_id,
            'vehicle_id'   => $loan->vehicle_id,
            'loan_cart_id' => $loan->id,
            'amount'       => $amount,
            'type'         => $type,
            'status'       => Transaction::COMPLETED,
            'method'       => $method,
            'recorded_by'  => $agent?->id,
            'note'         => $note,
        ]);
    }

    private function releaseVehicle(LoanCart $loan): void
    {
        if ($vehicle = Vehicle::lockForUpdate()->find($loan->vehicle_id)) {
            $vehicle->releaseOne();
        }
    }

    private function assertDays(int $days): void
    {
        if ($days < 1 || $days > self::MAX_DAYS) {
            throw new FleetException('La durée doit être comprise entre 1 et ' . self::MAX_DAYS . ' jours.');
        }
    }
}
