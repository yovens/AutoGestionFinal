<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/** php artisan app:super-admin email@exemple.ht  → kont sa a vin super admin. */
class MakeSuperAdmin extends Command
{
    protected $signature = 'app:super-admin {email : E-mail du compte à promouvoir}';

    protected $description = 'Donne le rôle de super administrateur à un compte existant';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (!$user) {
            $this->error('Aucun compte avec cet e-mail.');

            return self::FAILURE;
        }

        $user->update(['role' => User::SUPER_ADMIN, 'status' => User::ACTIVE]);
        $this->info("{$user->nom} est maintenant super administrateur.");

        return self::SUCCESS;
    }
}
