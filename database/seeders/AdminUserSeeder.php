<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'nom'       => 'Admin',
                'password'  => Hash::make('123456'),
                'role'      => User::SUPER_ADMIN,
                'status'    => User::ACTIVE,
                'telephone' => '00000000',
            ]
        );
    }
}
