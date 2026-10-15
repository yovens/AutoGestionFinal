<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LoanCart extends Model
{
    public const PENDING         = 'pending';
    public const APPROVED        = 'approved';
    public const REJECTED        = 'rejected';
    public const EXPIRED         = 'expired';
    public const PENDING_RENEWAL = 'pending_renewal';
    public const RETURNED        = 'returned';

    /** Estati kote machin nan toujou soti nan stock la (kliyan an genyen l oswa li rezève). */
    public const HOLDING = [self::PENDING, self::APPROVED, self::EXPIRED, self::PENDING_RENEWAL];

    public const LABELS = [
        self::PENDING         => 'En attente',
        self::APPROVED        => 'En cours',
        self::REJECTED        => 'Refusée',
        self::EXPIRED         => 'Délai dépassé',
        self::PENDING_RENEWAL => 'Prolongation demandée',
        self::RETURNED        => 'Rendue',
    ];

    public const TONES = [
        self::PENDING         => 'wait',
        self::APPROVED        => 'ok',
        self::REJECTED        => 'off',
        self::EXPIRED         => 'alert',
        self::PENDING_RENEWAL => 'wait',
        self::RETURNED        => 'off',
    ];

    protected $fillable = [
        'vehicle_id', 'user_id', 'duration_days', 'total_amount', 'status',
        'start_date', 'end_date', 'renewal_requested_days', 'returned_at',
    ];

    protected $casts = [
        'start_date'             => 'date',
        'end_date'               => 'date',
        'returned_at'            => 'datetime',
        'duration_days'          => 'integer',
        'total_amount'           => 'integer',
        'renewal_requested_days' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'loan_cart_id');
    }

    /**
     * Mete tout lokasyon ki depase dat fen yo sou "expired".
     * Rele chak jou pa scheduler la (routes/console.php) epi lè paj lokasyon yo louvri.
     */
    public static function syncExpired(): int
    {
        return static::where('status', self::APPROVED)
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<', Carbon::today())
            ->update(['status' => self::EXPIRED]);
    }

    public function isExpired(): bool
    {
        if ($this->status === self::EXPIRED) {
            return true;
        }

        return $this->end_date && Carbon::today()->gt($this->end_date);
    }

    public function daysLeft(): int
    {
        if (!$this->end_date || $this->isExpired()) {
            return 0;
        }

        return (int) Carbon::today()->diffInDays($this->end_date, false);
    }

    public function daysLate(): int
    {
        if (!$this->end_date || !Carbon::today()->gt($this->end_date)) {
            return 0;
        }

        return (int) $this->end_date->diffInDays(Carbon::today());
    }

    public function canRequestRenewal(): bool
    {
        return in_array($this->status, [self::APPROVED, self::EXPIRED], true);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::LABELS[$this->status] ?? ucfirst((string) $this->status);
    }

    public function getStatusToneAttribute(): string
    {
        return self::TONES[$this->status] ?? 'off';
    }
}
