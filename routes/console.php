<?php

use App\Models\LoanCart;
use App\Notifications\LateNotice;
use App\Notifications\ReturnReminder;
use App\Support\Notifier;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * Mete lokasyon ki depase dat fen yo sou "expired".
 * Sou sèvè a, ajoute cron sa a yon sèl fwa:  * * * * * php /chemen/artisan schedule:run
 */
Artisan::command('loans:sync-expired', function () {
    $count = LoanCart::syncExpired();
    $this->info("{$count} location(s) passée(s) en retard.");
})->purpose('Marquer les locations dont la date de fin est dépassée');

Schedule::command('loans:sync-expired')->dailyAt('00:05');

/*
 * Rapèl kliyan yo:
 *  - jou anvan retou a ("Retour du véhicule demain")
 *  - premye jou reta a ("Retour du véhicule en retard")
 * Chak mesaj voye yon sèl fwa (reminder_sent_at / late_notice_sent_at).
 */
Artisan::command('loans:remind', function () {
    LoanCart::syncExpired();

    $tomorrow = LoanCart::with('user', 'vehicle')
        ->whereIn('status', [LoanCart::APPROVED, LoanCart::PENDING_RENEWAL])
        ->whereDate('end_date', Carbon::tomorrow())
        ->whereNull('reminder_sent_at')->get();

    foreach ($tomorrow as $loan) {
        Notifier::send($loan->user, new ReturnReminder($loan));
        $loan->forceFill(['reminder_sent_at' => now()])->save();
    }

    $late = LoanCart::with('user', 'vehicle')
        ->where('status', LoanCart::EXPIRED)
        ->whereNull('late_notice_sent_at')->get();

    foreach ($late as $loan) {
        Notifier::send($loan->user, new LateNotice($loan));
        $loan->forceFill(['late_notice_sent_at' => now()])->save();
    }

    $this->info("{$tomorrow->count()} rappel(s) de retour, {$late->count()} avis de retard.");
})->purpose('Envoyer les rappels de retour et les avis de retard aux clients');

Schedule::command('loans:remind')->dailyAt('08:00');

// Backup baz done a chak swa (kòmand: app/Console/Commands/BackupDatabase.php)
Schedule::command('app:backup')->dailyAt('02:00');
