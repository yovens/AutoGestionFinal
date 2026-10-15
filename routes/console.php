<?php

use App\Models\LoanCart;
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
