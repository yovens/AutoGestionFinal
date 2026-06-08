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
        Schema::table('loan_carts', function (Blueprint $table) {
            // N ap ajoute start_date ak end_date apre kolòn status a, epi yo nullable
            $table->date('start_date')->nullable()->after('status');
            $table->date('end_date')->nullable()->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_carts', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date']);
        });
    }
};