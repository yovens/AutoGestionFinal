<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    /** Kalite aksyon yo, gwoupe pou filtè jounal la. */
    public const GROUPS = [
        'loan'     => 'Locations',
        'sale'     => 'Ventes',
        'payment'  => 'Paiements',
        'vehicle'  => 'Parc',
        'user'     => 'Comptes',
        'settings' => 'Paramètres',
        'backup'   => 'Sauvegardes',
        'auth'     => 'Connexions',
    ];

    public const TONES = [
        'delete' => 'alert', 'reject' => 'alert', 'cancel' => 'alert', 'block' => 'alert', 'role' => 'wait',
        'checkout' => 'ok', 'checkin' => 'ok', 'confirm' => 'ok', 'create' => 'brand',
    ];

    protected $fillable = ['user_id', 'action', 'description', 'subject_type', 'subject_id', 'data', 'ip'];

    protected $casts = ['data' => 'array', 'created_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    public function getGroupLabelAttribute(): string
    {
        return self::GROUPS[strtok($this->action, '.')] ?? 'Autre';
    }

    public function getToneAttribute(): string
    {
        $verb = substr(strrchr($this->action, '.') ?: '', 1);

        return self::TONES[$verb] ?? 'off';
    }
}
