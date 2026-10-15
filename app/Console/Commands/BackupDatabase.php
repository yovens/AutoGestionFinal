<?php

namespace App\Console\Commands;

use App\Support\Agency;
use App\Support\Audit;
use App\Support\Backup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/** php artisan app:backup  → sove baz done a nan storage/app/backups (planifye chak swa a 2 h). */
class BackupDatabase extends Command
{
    protected $signature = 'app:backup {--no-mail : Ne pas envoyer la copie par e-mail}';

    protected $description = 'Sauvegarde complète de la base de données (compressée)';

    public function handle(): int
    {
        try {
            $path = Backup::create();
        } catch (\Throwable $e) {
            Log::error('Sauvegarde échouée', ['error' => $e->getMessage()]);
            $this->error('Sauvegarde échouée : ' . $e->getMessage());

            return self::FAILURE;
        }

        $size = Backup::humanSize(filesize($path));
        $this->info('Sauvegarde créée : ' . basename($path) . " ({$size})");
        Audit::log('backup.create', 'Sauvegarde ' . basename($path) . " ({$size})");

        $deleted = Backup::prune((int) Agency::get('backup_keep_days', 14));
        if ($deleted) {
            $this->line("{$deleted} ancienne(s) sauvegarde(s) supprimée(s).");
        }

        $to = Agency::get('backup_email');
        if ($to && !$this->option('no-mail')) {
            if (filesize($path) > 20 * 1048576) {
                $this->warn('Copie par e-mail ignorée : fichier trop volumineux (> 20 Mo).');
            } else {
                try {
                    Mail::raw('Sauvegarde automatique de la base AutoGestion du ' . now()->format('d/m/Y H:i') . " ({$size}). Conservez ce fichier en lieu sûr.",
                        fn ($m) => $m->to($to)->subject('Sauvegarde ' . Agency::get('name') . ' ' . now()->format('d/m/Y'))->attach($path));
                    $this->line("Copie envoyée à {$to}.");
                } catch (\Throwable $e) {
                    Log::warning('Copie de sauvegarde non envoyée', ['error' => $e->getMessage()]);
                    $this->warn('Copie par e-mail non envoyée : ' . $e->getMessage());
                }
            }
        }

        return self::SUCCESS;
    }
}
