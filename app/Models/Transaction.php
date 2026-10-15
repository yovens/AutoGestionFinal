<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public const SALE   = 'vente';
    public const RENTAL = 'location';
    public const FEE    = 'frais';     // reta, domaj, kabiran...

    public const METHODS = [
        'cash'     => 'Espèces',
        'moncash'  => 'MonCash',
        'natcash'  => 'NatCash',
        'card'     => 'Carte',
        'transfer' => 'Virement',
    ];

    public const PENDING   = 'pending';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    public const TYPE_LABELS = [
        self::SALE   => 'Achat',
        self::RENTAL => 'Location',
        self::FEE    => 'Frais',
    ];

    public const STATUS_LABELS = [
        self::PENDING   => 'À encaisser',
        self::COMPLETED => 'Réglée',
        self::CANCELLED => 'Annulée',
    ];

    public const STATUS_TONES = [
        self::PENDING   => 'wait',
        self::COMPLETED => 'ok',
        self::CANCELLED => 'off',
    ];

    protected $fillable = ['user_id', 'vehicle_id', 'loan_cart_id', 'amount', 'type', 'status', 'method', 'recorded_by', 'note'];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function loan()
    {
        return $this->belongsTo(LoanCart::class, 'loan_cart_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function getMethodLabelAttribute(): string
    {
        return self::METHODS[$this->method] ?? '—';
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::COMPLETED);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->type] ?? ucfirst((string) $this->type);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst((string) $this->status);
    }

    public function getStatusToneAttribute(): string
    {
        return self::STATUS_TONES[$this->status] ?? 'off';
    }

    /** Referans lizib pou kliyan an ak pou resi (eg. AG-000042). */
    public function getReferenceAttribute(): string
    {
        return 'AG-' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }
}
