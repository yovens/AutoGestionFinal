<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tikè comptoir: prèv papye pou chak remiz kle, retou machin oswa remiz machin vann.
 * `data` kenbe yon foto enfòmasyon yo nan moman an (km, kabiran, liy montan...)
 * pou tikè a rete menm jan menm si lokasyon an chanje pita.
 */
class Ticket extends Model
{
    public const CHECKOUT = 'sortie';
    public const CHECKIN  = 'retour';
    public const SALE     = 'vente';

    public const TITLES = [
        self::CHECKOUT => 'Bon de sortie',
        self::CHECKIN  => 'Bon de retour',
        self::SALE     => 'Bon de remise (vente)',
    ];

    public const PREFIXES = [
        self::CHECKOUT => 'BS',
        self::CHECKIN  => 'BR',
        self::SALE     => 'BV',
    ];

    protected $fillable = ['kind', 'loan_cart_id', 'transaction_id', 'vehicle_id', 'user_id', 'issued_by', 'amount', 'data'];

    protected $casts = [
        'data'   => 'array',
        'amount' => 'integer',
    ];

    public function loan()
    {
        return $this->belongsTo(LoanCart::class, 'loan_cart_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function getCodeAttribute(): string
    {
        return (self::PREFIXES[$this->kind] ?? 'TK') . '-' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    public function getTitleAttribute(): string
    {
        return self::TITLES[$this->kind] ?? 'Ticket';
    }

    public function line(string $key, $default = null)
    {
        return data_get($this->data, $key, $default);
    }
}
