<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * `php artisan db:seed` kreye kont admin an.
     * `php artisan db:seed --class=DemoSeeder` ajoute machin ak kliyan pou teste.
     */
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);
    }
}
