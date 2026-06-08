<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        // Nou ajoute kolòn google_id apre kolòn email
        $table->string('google_id')->nullable()->after('email');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('google_id');
    });
}
};
