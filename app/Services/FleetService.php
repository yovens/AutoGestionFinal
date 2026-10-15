<?php

namespace App\Services;

use App\Models\LoanCart;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Exceptions\FleetException;

/**
 * Tout règ biznis ki touche stock machin yo, lokasyon ak vant.
 * Kontwolè yo (admin, kliyan, epi pita sekretè) pase isit la pou lojik la rete menm.
 *
 * Chak metòd ki ka echwe voye yon FleetException ak yon mesaj ki ka montre itilizatè a.
 */
class FleetService
{
    public const MAX_DAYS = 365;

    /* ------------------------------------------------------------------ */
    /*  Lokasyon                                                           */
    /* ------------------------------------------------------------------ */

    /** Kliyan an mande yon lokasyon: machin nan rezève, estati "pending". */
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

    /** Admin kreye yon lokasyon dirèkteman (kliyan an la nan biwo a): li kòmanse touswit. */
    public function createApprovedLoan(int $userId, int $vehicleId, int $days): LoanCart
    {
        $this->assertDays($days);

        return DB::transaction(function () use ($userId, $vehicleId, $days) {
            $vehicle = Vehicle::lockForUpdate()->findOrFail($vehicleId);
            $vehicle->takeOne();

            $loan = LoanCart::create([
                'user_id'       => $userId,
                'vehicle_id'    => $vehicle->id,
                'status'        => LoanCart::APPROVED,
                'duration_days' => $days,
                'total_amount'  => $vehicle->loan_price * $days,
                'start_date'    => Carbon::today(),
                'end_date'      => Carbon::today()->addDays($days),
            ]);

            $this->recordRentalPayment($loan, $loan->total_amount);

            return $loan;
        });
    }

    public function approveLoan(LoanCart $loan): void
    {
        DB::transaction(function () use ($loan) {
            $loan = LoanCart::lockForUpdate()->findOrFail($loan->id);

            if ($loan->status !== LoanCart::PENDING) {
                throw new FleetException('Seule une demande en attente peut être approuvée.');
            }

            $loan->update([
                'status'     => LoanCart::APPROVED,
                'start_date' => Carbon::today(),
                'end_date'   => Carbon::today()->addDays($loan->duration_days),
            ]);

            $this->recordRentalPayment($loan, $loan->total_amount);
        });
    }

    public function rejectLoan(LoanCart $loan): void
    {
        DB::transaction(function () use ($loan) {
            $loan = LoanCart::lockForUpdate()->findOrFail($loan->id);

            if ($loan->status !== LoanCart::PENDING) {
                throw new FleetException('Seule une demande en attente peut être refusée.');
            }

            $loan->update(['status' => LoanCart::REJECTED, 'start_date' => null, 'end_date' => null]);
            $this->releaseVehicle($loan);
        });
    }

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
     * Prolonje yon lokasyon (swa demann kliyan an, swa admin nan dirèkteman).
     * Si dat fen an deja pase, nouvo jou yo konte apati jodi a.
     */
    public function extendLoan(LoanCart $loan, int $days): void
    {
        $this->assertDays($days);

        DB::transaction(function () use ($loan, $days) {
            $loan = LoanCart::with('vehicle')->lockForUpdate()->findOrFail($loan->id);

            if (!in_array($loan->status, [LoanCart::APPROVED, LoanCart::EXPIRED, LoanCart::PENDING_RENEWAL], true)) {
                throw new FleetException('Seule une location en cours peut être prolongée.');
            }

            $extra = $loan->vehicle->loan_price * $days;
            $base = ($loan->end_date && $loan->end_date->gte(Carbon::today())) ? $loan->end_date->copy() : Carbon::today();

            $loan->update([
                'status'                 => LoanCart::APPROVED,
                'duration_days'          => $loan->duration_days + $days,
                'total_amount'           => $loan->total_amount + $extra,
                'start_date'             => $loan->start_date ?? Carbon::today(),
                'end_date'               => $base->addDays($days),
                'renewal_requested_days' => null,
            ]);

            $this->recordRentalPayment($loan, $extra);
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

    /** Machin nan tounen nan pak la: lokasyon an fini, stock la remonte. */
    public function returnLoan(LoanCart $loan): void
    {
        DB::transaction(function () use ($loan) {
            $loan = LoanCart::lockForUpdate()->findOrFail($loan->id);

            if (!in_array($loan->status, [LoanCart::APPROVED, LoanCart::EXPIRED, LoanCart::PENDING_RENEWAL], true)) {
                throw new FleetException('Ce véhicule n’est pas sorti : rien à réceptionner.');
            }

            $loan->update([
                'status'                 => LoanCart::RETURNED,
                'returned_at'            => now(),
                'renewal_requested_days' => null,
            ]);

            $this->releaseVehicle($loan);
        });
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

    /** Kliyan an achte: machin nan rezève, tranzaksyon an tann konfimasyon peman. */
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

    public function confirmSale(Transaction $transaction): void
    {
        if ($transaction->status !== Transaction::PENDING) {
            throw new FleetException('Cette opération a déjà été traitée.');
        }

        $transaction->update(['status' => Transaction::COMPLETED]);
    }

    /** Anile yon vant ki poko konfime: machin nan retounen nan stock. */
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

    private function recordRentalPayment(LoanCart $loan, int $amount): Transaction
    {
        return Transaction::create([
            'user_id'      => $loan->user_id,
            'vehicle_id'   => $loan->vehicle_id,
            'loan_cart_id' => $loan->id,
            'amount'       => $amount,
            'type'         => Transaction::RENTAL,
            'status'       => Transaction::COMPLETED,
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
