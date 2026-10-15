<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Jounal aktivite: anrejistre kiyès ki fè kisa.
 *   Audit::log('loan.checkout', 'Clés remises pour LOC-00012', $loan, ['montant' => 360]);
 * Yon erè nan jounal la pa janm bloke operasyon an.
 */
class Audit
{
    public static function log(string $action, string $description, ?Model $subject = null, array $data = []): void
    {
        try {
            $request = app()->runningInConsole() ? null : request();

            ActivityLog::create([
                'user_id'      => auth()->id(),
                'action'       => $action,
                'description'  => mb_substr($description, 0, 255),
                'subject_type' => $subject ? $subject->getMorphClass() : null,
                'subject_id'   => $subject?->getKey(),
                'data'         => $data ?: null,
                'ip'           => $request?->ip(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Journal d’activité non enregistré', ['action' => $action, 'error' => $e->getMessage()]);
        }
    }
}
