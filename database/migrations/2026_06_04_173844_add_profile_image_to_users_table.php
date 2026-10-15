<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Migrasyon sa a te ajoute `profile_image` yon dezyèm fwa (li deja nan 2026_06_01_192413),
 * sa ki te fè `php artisan migrate:fresh` echwe. Kounye a li pa fè anyen si kolòn nan la deja.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'profile_image')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('profile_image')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Kolòn nan jere pa 2026_06_01_192413_add_profile_image_to_users_table.
    }
};
