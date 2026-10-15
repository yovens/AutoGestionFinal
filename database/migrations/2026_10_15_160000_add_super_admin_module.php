<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * Modil super admin:
 *  - paramèt ajans lan (kle → valè)
 *  - jounal aktivite (kiyès ki fè kisa, kilè)
 *  - premye admin nan vin super admin si pa gen youn deja
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 60)->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 40)->index();
            $table->string('description');
            $table->nullableMorphs('subject');
            $table->json('data')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });

        // Pwopriyetè a: admin ki pi ansyen an vin super admin (si pa gen okenn).
        if (!DB::table('users')->where('role', 'SUPER_ADMIN')->exists()) {
            $first = DB::table('users')->where('role', 'ADMIN')->orderBy('id')->value('id');
            if ($first) {
                DB::table('users')->where('id', $first)->update(['role' => 'SUPER_ADMIN']);
            }
        }
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'SUPER_ADMIN')->update(['role' => 'ADMIN']);
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('settings');
    }
};
