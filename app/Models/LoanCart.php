<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LoanCart extends Model
{
    public const PENDING         = 'pending';
    public const READY           = 'ready';      // konfime, kliyan an poko vin pran kle yo
    public const APPROVED        = 'approved';   // kliyan an gen machin nan (an kou)
    public const REJECTED        = 'rejected';
    public const EXPIRED         = 'expired';
    public const PENDING_RENEWAL = 'pending_renewal';
    public const RETURNED        = 'returned';
    public const CANCELLED       = 'cancelled';  // konfime men kliyan an pa janm vin

    /** Estati kote machin nan toujou soti nan stock la (kliyan an genyen l oswa li rezève). */
    public const HOLDING = [self::PENDING, self::READY, self::APPROVED, self::EXPIRED, self::PENDING_RENEWAL];

    /** Estati kote kliyan an gen machin nan nan men l. */
    public const OUT = [self::APPROVED, self::EXPIRED, self::PENDING_RENEWAL];

    /** Nivo kabiran (an ka). */
    public const FUEL = [0 => 'Vide', 1 => '1/4', 2 => '1/2', 3 => '3/4', 4 => 'Plein'];

    public const LABELS = [
        self::PENDING         => 'En attente',
        self::READY           => 'À récupérer',
        self::APPROVED        => 'En cours',
        self::REJECTED        => 'Refusée',
        self::EXPIRED         => 'Délai dépassé',
        self::PENDING_RENEWAL => 'Prolongation demandée',
        self::RETURNED        => 'Rendue',
        self::CANCELLED       => 'Annulée',
    ];

    public const TONES = [
        self::PENDING         => 'wait',
        self::READY           => 'brand',
        self::APPROVED        => 'ok',
        self::REJECTED        => 'off',
        self::EXPIRED         => 'alert',
        self::PENDING_RENEWAL => 'wait',
        self::RETURNED        => 'off',
        self::CANCELLED       => 'off',
    ];

    protected $fillable = [
        'vehicle_id', 'user_id', 'duration_days', 'total_amount', 'status',
        'start_date', 'end_date', 'renewal_requested_days', 'returned_at',
        'pickup_code', 'picked_up_at', 'checkout_by', 'checkin_by', 'id_document',
        'km_out', 'km_in', 'fuel_out', 'fuel_in', 'condition_out', 'condition_in',
        'deposit', 'deposit_returned', 'reminder_sent_at', 'late_notice_sent_at',
    ];

    protected $casts = [
        'start_date'             => 'date',
        'end_date'               => 'date',
        'returned_at'            => 'datetime',
        'picked_up_at'           => 'datetime',
        'reminder_sent_at'       => 'datetime',
        'late_notice_sent_at'    => 'datetime',
        'km_out'                 => 'integer',
        'km_in'                  => 'integer',
        'fuel_out'               => 'integer',
        'fuel_in'                => 'integer',
        'deposit'                => 'integer',
        'deposit_returned'       => 'boolean',
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

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'loan_cart_id');
    }

    public function checkoutAgent()
    {
        return $this->belongsTo(User::class, 'checkout_by');
    }

    public function checkinAgent()
    {
        return $this->belongsTo(User::class, 'checkin_by');
    }

    /** Kòd retrè kliyan an montre nan ajans lan (eg. K7P-29Q). San lèt ki ka konfonn (0/O, 1/I). */
    public static function newPickupCode(): string
    {
        $alphabet = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        do {
            $raw = '';
            for ($i = 0; $i < 6; $i++) {
                $raw .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
            $code = substr($raw, 0, 3) . '-' . substr($raw, 3);
        } while (static::where('pickup_code', $code)->exists());

        return $code;
    }

    /** Netwaye sa sekretè a tape (espas, tire, miniskil) pou konpare ak kòd la. */
    public static function normalizeCode(?string $input): string
    {
        $clean = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $input));

        return strlen($clean) === 6 ? substr($clean, 0, 3) . '-' . substr($clean, 3) : $clean;
    }

    public static function fuelLabel(?int $level): string
    {
        return $level === null ? '—' : (self::FUEL[$level] ?? '—');
    }

    /** Montan ki poko peye sou lokasyon sa (prolongasyon ki pa ankese). */
    public function outstanding(): int
    {
        return (int) $this->transactions()->where('status', Transaction::PENDING)->sum('amount');
    }

    /** Frè reta sijere: jou reta × pri pa jou × % ajans lan (Paramètres). */
    public function suggestedLateFee(): int
    {
        $percent = (int) \App\Support\Agency::get('late_fee_percent', 100);

        return (int) round($this->daysLate() * (int) ($this->vehicle?->loan_price ?? 0) * $percent / 100);
    }

    public function getReferenceAttribute(): string
    {
        return 'LOC-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
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
