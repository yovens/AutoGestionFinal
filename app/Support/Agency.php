<?php

namespace App\Support;

use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;

/**
 * Paramèt ajans lan (super admin ka chanje yo nan "Paramètres").
 * Valè pa defo yo itilize si tab `settings` la vid oswa poko egziste.
 */
class Agency
{
    public const DEFAULTS = [
        'name'             => 'AutoGestion',
        'city'             => 'Simon, Les Cayes',
        'address'          => 'Simon, arrondissement des Cayes, Haïti',
        'phone'            => '+509 40 16 03 35',
        'email'            => 'contact@autogestion.ht',
        'hours'            => 'Lundi au vendredi, 8 h à 17 h',
        'hours_note'       => 'Samedi sur rendez-vous',
        'map_query'        => 'Simon, Cayes, Haiti',
        'late_fee_percent' => 100,   // % pri pa jou pou chak jou reta
        'default_deposit'  => 0,     // kosyon pa defo nan remiz kle
        'methods'          => ['cash', 'moncash', 'natcash', 'card', 'transfer'],
        'ticket_footer'    => '',    // tèks siplemantè anba tikè yo
    ];

    private const CACHE_KEY = 'agency.settings';

    private static ?array $memo = null;

    public static function all(): array
    {
        if (self::$memo !== null) {
            return self::$memo;
        }

        try {
            $stored = Cache::rememberForever(self::CACHE_KEY, fn () => Setting::pluck('value', 'key')
                ->map(fn ($v) => json_decode($v, true))
                ->all());
        } catch (\Throwable) {
            $stored = []; // tab la poko la (anvan migrate) oswa cache pa disponib
        }

        return self::$memo = array_merge(self::DEFAULTS, array_intersect_key($stored, self::DEFAULTS));
    }

    public static function get(string $key, $default = null)
    {
        return self::all()[$key] ?? $default;
    }

    public static function set(array $values): void
    {
        foreach (array_intersect_key($values, self::DEFAULTS) as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => json_encode($value)]);
        }

        Cache::forget(self::CACHE_KEY);
        self::$memo = null;
    }

    /** Lyen tel: pou telefòn nan. */
    public static function telHref(): string
    {
        return 'tel:' . preg_replace('/[^\d+]/', '', (string) self::get('phone'));
    }

    /** Mòd peman ajans lan aksepte (nan lòd lis la). */
    public static function methods(): array
    {
        $enabled = (array) self::get('methods', []);
        $methods = array_intersect_key(Transaction::METHODS, array_flip($enabled));

        return $methods ?: Transaction::METHODS;
    }
}
