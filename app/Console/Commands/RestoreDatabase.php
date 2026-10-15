<?php

namespace App\Console\Commands;

use App\Support\Backup;
use Illuminate\Console\Command;

/** php artisan app:restore autogestion-2026-10-15-020000.sql.gz */
class RestoreDatabase extends Command
{
    protected $signature = 'app:restore {file? : Nom du fichier dans storage/app/backups (le plus récent si vide)} {--force : Ne pas demander de confirmation}';

    protected $description = 'Restaure une sauvegarde (remplace toutes les données actuelles)';

    public function handle(): int
    {
        $file = $this->argument('file') ?: (Backup::list()[0]['name'] ?? null);

        if (!$file) {
            $this->error('Aucune sauvegarde dans storage/app/backups.');

            return self::FAILURE;
        }

        $this->warn("Toutes les données actuelles seront remplacées par celles de {$file}.");
        if (!$this->option('force') && !$this->confirm('Continuer ?')) {
            return self::SUCCESS;
        }

        // Fè yon backup de sa ki la kounye a anvan, pou nou ka tounen si se pa bon fichye a.
        $safety = Backup::create();
        $this->line('Sauvegarde de sécurité de l’état actuel : ' . basename($safety));

        $count = Backup::restore($file);
        $this->info("Restauration terminée ({$count} instructions).");

        return self::SUCCESS;
    }
}
